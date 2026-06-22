<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Resource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin account
        User::create([
            'name' => 'Admin',
            'email' => 'admin@icct.edu',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        // Sample faculty
        User::create([
            'name' => 'Faculty User',
            'email' => 'faculty@icct.edu',
            'password' => bcrypt('faculty123'),
            'role' => 'faculty',
        ]);

        // Sample resources
        $resources = ['HDMI Cable', 'Remote Control', 'Extension Wire', 'Speaker', 'Projector', 'Laptop'];
        foreach ($resources as $name) {
            Resource::create([
                'name' => $name,
                'description' => "Standard $name for classroom use",
                'quantity' => rand(3, 10),
                'is_available' => true,
            ]);
        }
    }
}
