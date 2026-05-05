<?php


namespace App\Services;

use App\Models\User;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Jobs\SendResetPasswordCodeJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use App\Mail\SendCodeResetPassword;
use Spatie\Permission\Models\Role;
use Illuminate\Http\JsonResponse;
use App\Models\ResetCodePassword;
use App\Http\Responses\Response;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Exception;
use Throwable;
use Storage;

class UserService {

    public function register($request): array{
        $clientRole = Role::query()->where('name', 'Client')->firstOrFail();

        $defaultPhoto = url('storage/uploads/det/defualtProfilePhoto.png');

        $user = User::query()->create([
        'role_id' =>  $clientRole->id,
        'name' => $request['name'],
        'nick_name' => $request['nick_name'],
        'email' => $request['email'],
        'password' => Hash::make($request['password']),
        'nationality_id' => $request['nationality_id'],
        'age' => $request['age'],
        'photo' => $defaultPhoto
        ]);

        $user->assignRole($clientRole);

        $permissions = $clientRole->permissions()->pluck('name')->toArray();
        $user->givePermissionTo($permissions);

        $user->load('roles' , 'permissions');

        $user = User::query()->find($user['id']);
        $user = $this->appendRolesAndPermissions($user);
        $user['token'] = $user->createToken("token")->plainTextToken;

        $message = 'User created successfully';

        return ['user' => $user , 'message' => $message];
    }

    public function signin($request): array{
        $user = User::query()->where('email',$request['email'])->first();

            if (!$user) {
                throw new Exception("User not found.", 404);
            }

            if (is_null($user->password)) {
                throw new Exception(
                    "This account was created using Google. Please login with Google or set a password first.",
                    403
                );
            }
            if(!Auth::attempt($request->only(['email' , 'password']))){
            throw new Exception("User email & password does not with our record.", 401 );
            }

        $user = $this->appendRolesAndPermissions($user);
        $user['token'] = $user->createToken("token")->plainTextToken;
        $message = 'User logged in successfully';
        $code = 200;

     return ['user' => $user , 'message' => $message , 'code' => $code];
    }

    public function setPassword($request , $email): array{
        $user = User::where('email' , $email)->first();

        if(!$user){
            throw new Exception("Wrong email user not found.",  404);
        }

        if (!is_null($user->password)) {
            throw new Exception("Password already set.",  400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $message = 'Password set successfully.';
        return ['user' => $user , 'message' => $message];
    }

    public function logout(): array{
        $user = Auth::user();
        if(!is_null(Auth::user())){
            Auth::user()->currentAccessToken()->delete();
            $message = 'User logged out successfully';
            $code = 200;
        }

        else{
            throw new Exception("invalid token.", 404);
        }

        return ['user' => $user , 'message' => $message , 'code' => $code];
    }

     public function forgotPassword($request): array{
              //Delete all old code user send before
              ResetCodePassword::query()->where('email' , $request['email'])->delete();
                $data['email'] =  $request['email'];
              //generate random code
                $data['code'] = mt_rand(100000, 999999);

                $data['role'] = User::query()->firstWhere('email' , $request['email'])->role_id;
                //Create a new code
                $codeData = ResetCodePassword::query()->create($data);

                //Send email to user
                SendResetPasswordCodeJob::dispatch($request['email'], $data['code']);
                $message = 'code sent';
                $code = 200;
            return ['user' => $data , 'message' => $message , 'code' => $code];
    }

    public function checkCode($request): array{
        //find the code
               $passwordReset = ResetCodePassword::query()->firstWhere('code' , $request['code']);

               $user = User::where('email' , $passwordReset->email)->first();

        //  check if it is not expired: the time is one hour
                if($passwordReset->created_at->addHour()->isPast()){
                    ResetCodePassword::where('email', $passwordReset->email)->delete();
                    $message = 'code_is_expire';
                    $code = 422;
                    return ['verifyCode' => 'expire', 'message' => $message , 'code' => $code];
                }

                $verifyCode['token'] = $user->createToken("token")->plainTextToken;

                $verifyCode['code'] = $passwordReset['code'];

               $message = 'code_is_valid';
                $code = 200;
            return ['verifyCode' => $verifyCode , 'message' => $message , 'code' => $code];
    }

    public function resetPassword($request , $codeR) : array{

                //find the code
                $passwordReset = ResetCodePassword::query()->firstWhere('code' , $codeR);
                $passwordResetDelete =  ResetCodePassword::where('email', $passwordReset->email);

                // check if it is not expired: the time is one hour
                if($passwordReset->created_at->addHour()->isPast()){
                      $passwordResetDelete->delete();
                      $message = 'code_is_expire';
                      $code = 422;

                   return ['role' => 'expire', 'message' => $message , 'code' => $code];
                }

                //find user's email
                     $user = User::query()->firstWhere('email' , $passwordReset['email']);

                //update user password
                     $request['password'] = bcrypt($request['password']);

                     $user->update([
                        'password' =>  $request['password']
                     ]);

                     $data['token'] = $user->createToken("token")->plainTextToken;
                     $data['role'] = $user->role_id;

                     $message = 'password has been successfully reset';
                     $code = 200;

                //delete current code
                $passwordResetDelete->delete();

        return ['role' =>  $data ,'message' => $message  , 'code' => $code];
    }

    public function googleSignIn($request) : array{
        try {
            // Socialite automatically validates the token with Google
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($request->id_token);

            // Normalize email (e.g., for Gmail aliases)
            $email = $this->normalizeEmail($googleUser->getEmail());

            // 1. Check if user with Google ID exists
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                // If yes, return auth token and user
                $user = $this->appendRolesAndPermissions($user);
                $user['token'] = $user->createToken("auth_token")->plainTextToken;

                $message = 'Successfully authenticated with Google';

                return ['user' => $user , 'message' => $message];
            }

            // 2. Check if user with Google email exists but doesn't have Google Sign-In yet
            $user = User::where('email', $email)->first();

            if ($user) {
                // User with email found - add Google ID
                $user->google_id = $googleUser->getId();
                $user->save();
                $user['token'] = $user->createToken("auth_token")->plainTextToken;
                $user = $this->appendRolesAndPermissions($user);
                $message = 'Successfully authenticated with Google';
                return ['user' => $user , 'message' => $message];
            }

            $clientRole = Role::query()->where('name', 'Client')->firstOrFail();
            $defaultPhoto = url('storage/uploads/det/defualtProfilePhoto.png');

            // 3. Create new user
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'role_id' =>  $clientRole->id,
                'photo' => $defaultPhoto
            ]);

            $user->assignRole('client');
            $permissions = $clientRole->permissions()->pluck('name')->toArray();
            $user->givePermissionTo($permissions);

            $user->load('roles' , 'permissions');

            $user = User::query()->find($user['id']);
            $user = $this->appendRolesAndPermissions($user);
            $user['token'] = $user->createToken("auth_token")->plainTextToken;
            $message = 'Successfully authenticated with Google';
            return ['user' => $user , 'message' => $message];
        } catch (\Exception $e) {
            throw new Exception(config('app.debug') ? $e->getMessage() : 'Authentication failed',  403);
        }
    }

    /**
     * Normalize email (handle Gmail aliases)
     */
    private function normalizeEmail(string $email): string{
         $email = Str::lower(Str::trim($email));

        // Replace googlemail.com with gmail.com
        if (Str::endsWith($email, '@googlemail.com')) {
            $email = Str::replace('@googlemail.com', '@gmail.com', $email);
        }

        return $email;
    }

    private function appendRolesAndPermissions($user){
           $roles = [];

           foreach ($user->roles as $role){
            $roles[] = $role->name;
           }

           unset($user['roles']);

           $user['roles']= $role;

           $permissions = [];
           foreach ($user->permissions as $permission){
            $permissions[] = $permission->name;
           }
           $user['permissions']= $permission;

        return $user; 
    }

}
git