<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // First seed the courses
        $this->call([
            CourseSeeder::class,        // First seed courses
            AssignmentSeeder::class,    // Then seed assignments
            StudentSeeder::class,       // Finally seed students and their assignments
        ]);
    }
}
