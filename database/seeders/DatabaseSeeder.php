<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Group;
use Illuminate\Database\Seeder;
use illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Membuat user/admin utama
        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'username' => 'Admin',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Membuat group utama dan mengaitkannya dengan user/admin utama
        $adminGroup = Group::create([
            'name' => 'ADMIN',
            'status' => 'active',
        ]);

        $adminUser->groups()->attach($adminGroup->id);

        // Membuat grup lain dengan factory dan mengaitkannya dengan user-user yang dibuat
        $otherGroups = Group::factory()->count(3)->create();

        // Membuat user-user dengan factory dan mengaitkannya dengan grup-grup yang dibuat
        User::factory()->count(10)->create()->each(function ($user) use ($otherGroups) {
            $user->groups()->attach($otherGroups->random());
        });
    }
}
