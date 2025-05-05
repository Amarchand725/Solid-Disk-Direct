<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'name' => 'Solid Disk Direct',
            'banner' => '1684433621.png',
            'country' => 'United State',
            'state' => 'Lincolnwood',
            'area' => '6600 N Lincoln Ave Ste 316',
            'city' => 'Lincolnwood',
            'address' => '6600 N Lincoln Ave Ste 316 Lincolnwood Illinois 60712 United States.',
            'currency_symbol' => '$',
        ]);

        $admin = User::create([
            'is_employee' => 0,
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'status' => 1,
            'password' => Hash::make('admin@123'),
        ]);

        $roles = [
            'Admin', 'Developer', 'Employee'
        ];

        foreach($roles as $role) {
            Role::create(
                [
                    'name' => $role,
                    'guard_name' => 'web',
                ]
            );
        }
        $admin_role = Role::where('name', 'Admin')->first();
        $permissions = include(config_path('seederData/permissions.php'));

        foreach ($permissions as $permission) {
            $underscoreSeparated = explode('-', $permission);
            $label = str_replace('_', ' ', $underscoreSeparated[0]);
            Permission::create([
                'label' => $label,
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
        //Assign Permissions and Role "Admin User".
        $permissions = Permission::get();
        $admin_role->givePermissionTo($permissions);
        $admin->assignRole($admin_role);
    }
}
