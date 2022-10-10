<?php

namespace Database\Seeders;

use App\Models\Roles;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        Seed normal users
        User::factory(10)->create();

//        Create Super Admin data
         $super1 = User::create([
             'first_name' => 'Super',
             'last_name' => 'Admin 1',
             'email' => 'superadmin1@admin.com',
             'email_verified_at' => now(),
             'password' => 'password', // password
             'approved' => true,
         ]);


         $super2 = User::create([
             'first_name' => 'Super',
             'last_name' => 'Admin 2',
             'email' => 'superadmin2@admin.com',
             'email_verified_at' => now(),
             'password' => 'password', // password
             'approved' => true,
         ]);

//        Create data for Roles
        $role = Role::create(['name' => 'Admin']);
        Role::create(['name' => 'User']);
        $super1->assignRole($role);
        $super2->assignRole($role);


    }
}
