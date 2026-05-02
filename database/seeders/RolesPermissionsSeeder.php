<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Storage;


class RolesPermissionsSeeder extends Seeder
{
    public function run(): void {
    // 1. Create roles
        $superAdminRole = Role::create(['name' => 'superAdmin']);
        $clientRole = Role::create(['name' => 'Client']);

    // 2. Create permissions
        // $permissions = ['register' , 'signin'];

        // foreach ($permissions as $permissionName) {
        //     Permission::findOrCreate($permissionName, 'web');
        // }
        $permissions = ['register', 'signin'];

foreach ($permissions as $permissionName) {
    Permission::firstOrCreate([
        'name' => $permissionName,
        'guard_name' => 'web'
    ]);
}

    // assign permissions to roles
        $clientRole->syncPermissions($permissions);

    // 3. Assign permissions
        $superAdminRole->syncPermissions($permissions);

        $sourcePath = public_path('uploads/seeder_photos/defualtProfilePhoto.png');
        $targetPath = 'uploads/det/defualtProfilePhoto.png';

        Storage::disk('public')->put($targetPath, File::get($sourcePath));

    // 4. Create users for each role

        $superAdmin = User::factory()->create([
            'role_id' => $superAdminRole->id,
            'nationality_id' => 1,
            'age' => '20',
            'name' => 'Super',
            'nick_name' => 'Admin',
            'email' => 'SuperAdmin@example.com',
            'password' => bcrypt('password') ,
            'photo' => url(Storage::url($targetPath))
        ]);

        $superAdmin->assignRole($superAdminRole);

    //assign permissions with the role to the user
        $permissions = $superAdminRole->permissions()->pluck('name')->toArray();
        $superAdmin->givePermissionTo($permissions);
        
        $clientUser = User::factory()->create([
            'role_id' => $clientRole->id,
            'nationality_id' => 1,
            'age' => '20',
            'name' => 'Donor',
            'nick_name' => 'Admin',
            'email' => 'Donor@example.com',
            'password' => bcrypt('password') ,
            'photo' => url(Storage::url($targetPath))
        ]);

        $clientUser->assignRole($clientRole);

    //assign permissions with the role to the user
        $permissions = $clientRole->permissions()->pluck('name')->toArray();
        $clientUser->givePermissionTo($permissions);
  
    // 5. Create additional client users
        $names = ['shahed', 'dana', 'rama', 'yumna', 'rania', 'lana', 'rayan', 'mohammed', 'marwa', 'sawsan'];
        $nationalities = [1, 2, 3, 4, 5, 6, 7, 8, 3, 1];
        $ages = [20, 30, 25, 19, 21, 35, 29, 18, 29, 37];
        $emails = ['shahed@gmail.com', 'dana@gmail.com', 'rama@gmail.com', 'yumna@gmail.com', 'rania@gmail.com',
                   'lana@gmail.com', 'rayan@gmail.com', 'mohammed@gmail.com', 'marwa@gmail.com', 'sawsan@gmail.com'];
        $phones = ['0977665542', '09777865542', '09790665542', '09887665542', '09776655491',
                   '0977654235', '0966554229', '0977655218', '0929665542', '0973765542'];
        $genders = [1, 1, 1, 1, 1, 1, 2, 2, 1, 1];
        $passwords = ['123456789shahed', '123456789dana', '123456789rama', '123456789yumna',
                      '123456789rania', '123456789lana', '123456789rayan', '123456789mohammed',
                      '123456789marwa', '123456789sawsan'];

        for ($i = 0; $i < 10; $i++) {
            $user = User::create([
                'role_id' => $clientRole->id,
                'name' => $names[$i],
                'nick_name' => $names[$i],
                'age' => $ages[$i],
                'email' => $emails[$i],
                'nationality_id' => $nationalities[$i],
                'password' => Hash::make($passwords[$i]),
                'photo' => url(Storage::url($targetPath))

            ]);

            $user->assignRole($clientRole);

    //assign permissions with the role to the user
            $permissions = $clientRole->permissions()->pluck('name')->toArray();
            $user->givePermissionTo ($permissions);
        }
    }
}
