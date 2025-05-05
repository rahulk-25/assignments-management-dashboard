<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $courses = [
            [
                'name' => 'Introduction to Programming',
                'code' => 'cs101',
                'code_name' => 'intro_prog',
                'description' => 'Basic programming concepts and fundamentals',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Web Development',
                'code' => 'cs201',
                'code_name' => 'web_dev',
                'description' => 'Web development using modern technologies',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Database Management',
                'code' => 'cs301',
                'code_name' => 'db_mgmt',
                'description' => 'Database design and management',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Software Engineering',
                'code' => 'cs401',
                'code_name' => 'soft_eng',
                'description' => 'Software development principles and practices',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(
                ['code' => $course['code']],
                $course
            );
        }
    }
}
