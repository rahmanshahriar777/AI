<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder as Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
           'role-list',
           'role-create',
           'role-edit',
           'role-delete',
              'user-list',
              'user-create',
                'user-edit',
                'user-delete',
              'customer-list',
                'customer-create',
                'customer-edit',
                'customer-delete',
              'enquiry-list',
              'enquiry-create',
                'enquiry-edit',
                'enquiry-delete',
              'enquiry-address-list',
              'enquiry-address-create',
                'enquiry-address-edit',
                'enquiry-address-delete',
        ];
        
        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }
}
