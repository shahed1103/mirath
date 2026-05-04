<?php

namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\UserSigninRequest;
use App\Http\Requests\Auth\UserSignupRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\CheckCodeRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Responses\response;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Models\ResetCodePassword;
use App\Models\User;
use App\Mail\SendCodeResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private UserService $userService;

    public function __construct(UserService  $userService){
        $this->userService = $userService;
    }

    public function register(UserSignupRequest $request): JsonResponse {
        $data = [] ;
        try{
            $data = $this->userService->register($request->validated(),$request );
            return Response::Success($data['user'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function signin(UserSigninRequest $request): JsonResponse {
        $data = [] ;
       try{
            $data = $this->userService->signin($request);
            return Response::Success($data['user'], $data['message'], $data['code']);
       }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message ;
            $code = $th->getCode();
            return Response::ErrorX($data , $message , $errors , $code );
        }
    }

    public function logout(): JsonResponse {
        $data = [] ;
        try{
            $data = $this->userService->logout();
            return Response::Success($data['user'], $data['message'], $data['code']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::ErrorX($data , $message , $errors , $code );
        }
    }

    public function userForgotPassword(ForgotPasswordRequest $request): JsonResponse{
        $data = [] ;
        try{
            $data = $this->userService->forgotPassword($request);
            return Response::Success($data['user'], $data['message'], $data['code']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;

            return Response::Error($data , $message , $errors);
        }
    }

    public function userCheckCode(CheckCodeRequest $request): JsonResponse {
        $data = [];
        try{
            $data = $this->userService->checkCode($request);
            return Response::Success($data['verifyCode'], $data['message'], $data['code']);
        }

            catch(Throwable $th){
                $message = $th->getMessage();
                $errors [] = $message;

                return Response::Error($data , $message , $errors);
        }
    }

    public function userResetPassword(ResetPasswordRequest $request , $codeR): JsonResponse {

        $data = [];
       try{
            $data = $this->userService->resetPassword($request , $codeR);
            return Response::Success($data['role'], $data['message'] , $data['code'] );
       }

            catch(Throwable $th){
                $message = $th->getMessage();
                $errors [] = $message;
                $code = $th->getCode();
                return Response::Errorx($data , $message , $errors , $code);
        }
    }


    /**
     * Handle Google Sign-In with ID Token from Flutter
     */
    public function googleSignIn(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

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
                return response()->json([
                    'access_token' => $user->createToken('auth_token')->plainTextToken,
                    'user' => $user,
                    'message' => 'Successfully authenticated with Google'
                ], 200);
            }

            // 2. Check if user with Google email exists but doesn't have Google Sign-In yet
            $user = User::where('email', $email)->first();

            if ($user) {
                // User with email found - add Google ID
                $user->google_id = $googleUser->getId();
                $user->save();

                return response()->json([
                    'access_token' => $user->createToken('auth_token')->plainTextToken,
                    'user' => $user,
                    'message' => 'Successfully authenticated with Google'
                ], 200);
            }

            // 3. Create new user
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'password' => Hash::make(str()->random(32)),
                'role_id' => 2,
            ]);

            $user->assignRole('client');

            return response()->json([
                'access_token' => $user->createToken('auth_token')->plainTextToken,
                'user' => $user,
                'message' => 'Successfully authenticated with Google'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to authenticate with Google',
                'error' => config('app.debug') ? $e->getMessage() : 'Authentication failed'
            ], 500);
        }
    }

    /**
     * Normalize email (handle Gmail aliases)
     */
    private function normalizeEmail(string $email): string
    {
         $email = Str::lower(Str::trim($email));

        // Replace googlemail.com with gmail.com
        if (Str::endsWith($email, '@googlemail.com')) {
            $email = Str::replace('@googlemail.com', '@gmail.com', $email);
        }

        return $email;
    }
}

