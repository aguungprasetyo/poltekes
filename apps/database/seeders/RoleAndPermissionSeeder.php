<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Lecture;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = ['super_admin', 'admin', 'lecture', 'student',];

        foreach ($role as $key => $value) {
            Role::create(['name' => $value]);
        }

        $super_admin = Role::where('name', 'super_admin')->first();
        $super_admin->givePermissionTo(Permission::all());

        $super_user = User::create([
            'name' => 'Super Admin ',
            'email' => 'super_admin@mail.com',
            'password' => bcrypt('12341234'),
        ]);
        $super_user->assignRole(Role::where('name', 'super_admin')->first());

        $user = User::create([
            'name' => 'Admin ',
            'email' => 'admin@mail.com',
            'password' => bcrypt('12341234'),
        ]);
        $user->assignRole(Role::where('name', 'admin')->first());

        $admin = Admin::create([
            'user_id' => $user->id,
        ]);

        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'name' => 'Student ' . $i,
                'email' => 'student' . $i . '@mail.com',
                'password' => bcrypt('12341234'),
            ]);
            $user->assignRole(Role::where('name', 'student')->first());

            $student = Student::create([
                'user_id' => $user->id,
                'id_number' => rand(1000000000, 9999999999),
                'address' => '-',
                'phone' => rand(1000000000, 9999999999),
            ]);
        }

        for ($k = 1; $k <= 10; $k++) {
            $user = User::create([
                'name' => 'Lecture ' . $k,
                'email' => 'lecture' . $k . '@mail.com',
                'password' => bcrypt('12341234'),
            ]);
            $user->assignRole(Role::where('name', 'lecture')->first());

            $lecture = Lecture::create([
                'user_id' => $user->id,
                'id_number' => rand(1000000000, 9999999999),
                'address' => '-',
                'phone' => rand(1000000000, 9999999999),
            ]);
        }
    }
}
