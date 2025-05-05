<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    public function run()
    {
        // First ensure we have courses
        if (Course::count() === 0) {
            $this->call(CourseSeeder::class);
        }

        // Get courses and store them in an array with code as key
        $courses = Course::all()->keyBy('code');
        
        // Check if we have all required courses
        if ($courses->isEmpty()) {
            throw new \Exception('No courses found. Please run CourseSeeder first.');
        }

        $assignments = [
            // cs101 Assignments
            [
                'title' => 'Basic Programming Concepts Quiz',
                'course_id' => $courses['cs101']->id,
                'due_date' => now()->addDays(7),
                'total_points' => 100,
                'instructions' => 'Complete the online quiz covering basic programming concepts.',
                'allow_late_submissions' => false,
                'enable_automatic_grading' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Variables and Data Types Exercise',
                'course_id' => $courses['cs101']->id,
                'due_date' => now()->addDays(14),
                'total_points' => 50,
                'instructions' => 'Complete the exercises on variables and data types.',
                'allow_late_submissions' => true,
                'enable_automatic_grading' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // cs201 Assignments
            [
                'title' => 'HTML/CSS Project',
                'course_id' => $courses['cs201']->id,
                'due_date' => now()->addDays(21),
                'total_points' => 150,
                'instructions' => 'Create a responsive website using HTML and CSS.',
                'allow_late_submissions' => true,
                'enable_automatic_grading' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'JavaScript Basics',
                'course_id' => $courses['cs201']->id,
                'due_date' => now()->addDays(28),
                'total_points' => 100,
                'instructions' => 'Complete JavaScript exercises and mini-project.',
                'allow_late_submissions' => true,
                'enable_automatic_grading' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // cs301 Assignments
            [
                'title' => 'Database Design Project',
                'course_id' => $courses['cs301']->id,
                'due_date' => now()->addDays(35),
                'total_points' => 200,
                'instructions' => 'Design and implement a database for a given case study.',
                'allow_late_submissions' => false,
                'enable_automatic_grading' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'SQL Queries Practice',
                'course_id' => $courses['cs301']->id,
                'due_date' => now()->addDays(42),
                'total_points' => 75,
                'instructions' => 'Write SQL queries to solve given database problems.',
                'allow_late_submissions' => true,
                'enable_automatic_grading' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // cs401 Assignments
            [
                'title' => 'Software Requirements Document',
                'course_id' => $courses['cs401']->id,
                'due_date' => now()->addDays(49),
                'total_points' => 150,
                'instructions' => 'Create a detailed software requirements document.',
                'allow_late_submissions' => false,
                'enable_automatic_grading' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Agile Development Exercise',
                'course_id' => $courses['cs401']->id,
                'due_date' => now()->addDays(56),
                'total_points' => 100,
                'instructions' => 'Complete the agile development team exercise.',
                'allow_late_submissions' => true,
                'enable_automatic_grading' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($assignments as $assignment) {
            Assignment::create($assignment);
        }
    }
} 