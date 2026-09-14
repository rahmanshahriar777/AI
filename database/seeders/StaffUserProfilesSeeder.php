<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class StaffUserProfilesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure all required Spatie roles exist
        $requiredRoles = [
            'super_admin',
            'admin',
            'accounts-manager',
            'enquiry-manager',
            'lead-manager',
            'lead-job-manager',
            'job-manager',
            'office-manager',
            'inventory-manager',
            'fleet-manager',
            'health-safety-manager',
            'scheduler',
            'Scheduler',
            'scheduler-manager',
            'staff',
            'customer',
        ];

        foreach ($requiredRoles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Give super_admin all permissions
        $allPermissions = Permission::all();
        if ($allPermissions->count() > 0) {
            $superAdminRole = Role::where('name', 'super_admin')->first();
            if ($superAdminRole) {
                $superAdminRole->syncPermissions($allPermissions);
            }
        }

        // 2. Define the 12 Staff/Manager profiles requested by the user
        $profiles = [
            [
                'name' => 'Ryan Cross',
                'email' => 'rc@gmail.com',
                'roles' => ['scheduler', 'Scheduler', 'job-manager'],
            ],
            [
                'name' => 'Debbie White',
                'email' => 'dw@gmail.com',
                'roles' => ['accounts-manager'],
            ],
            [
                'name' => 'Aimee Daffin',
                'email' => 'ad2@gmail.com',
                'roles' => ['super_admin'],
            ],
            [
                'name' => 'Aimee Daffin',
                'email' => 'ad@gmail.com',
                'roles' => ['enquiry-manager'],
            ],
            [
                'name' => 'Stephanie White',
                'email' => 'sw@gmail.com',
                'roles' => ['lead-job-manager'],
            ],
            [
                'name' => 'Frankie White',
                'email' => 'fw@gmail.com',
                'roles' => ['accounts-manager'],
            ],
            [
                'name' => 'Natalie White',
                'email' => 'nw@gmail.com',
                'roles' => ['lead-job-manager'],
            ],
            [
                'name' => 'Rasel Mahmood',
                'email' => 'rm@gmail.com',
                'roles' => ['office-manager'],
            ],
            [
                'name' => 'Tony Lloyd',
                'email' => 'tl@gmail.com',
                'roles' => ['inventory-manager'],
            ],
            [
                'name' => 'Deke Rivers',
                'email' => 'dr@gmail.com',
                'roles' => ['fleet-manager'],
            ],
            [
                'name' => 'Charles Kingham',
                'email' => 'ck@gmail.com',
                'roles' => ['health-safety-manager'],
            ],
            [
                'name' => 'Pani Rezvani',
                'email' => 'pr@gmail.com',
                'roles' => ['accounts-manager'],
            ],
        ];

        foreach ($profiles as $p) {
            $user = User::updateOrCreate(
                ['email' => $p['email']],
                [
                    'name' => $p['name'],
                    'password' => Hash::make('12345678'),
                ]
            );

            // Assign roles
            $user->syncRoles($p['roles']);
        }
    }
}
