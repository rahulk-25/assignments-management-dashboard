<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Assignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run()
    {
        // Check if we have assignments, if not run AssignmentSeeder first
        if (Assignment::count() === 0) {
            $this->call(AssignmentSeeder::class);
        }

        // Only truncate student-related tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('student_assignments')->truncate();
        DB::table('students')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create 50 students with timestamp suffix to ensure unique emails
        $timestamp = now()->format('His');
        $students = [];
        for ($i = 1; $i <= 50; $i++) {
            $students[] = [
                'name' => "Student {$i}",
                'email' => "student{$i}_{$timestamp}@example.com",
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        // Insert students in chunks
        foreach (array_chunk($students, 10) as $chunk) {
            Student::insert($chunk);
        }

        // Get all assignments and students for creating relationships
        $assignments = Assignment::all();
        $students = Student::all();
        
        // For each student, assign some random assignments
        foreach ($students as $student) {
            // Randomly select 3-7 assignments for each student
            $selectedAssignments = $assignments->random(min(rand(3, 7), $assignments->count()));
            
            foreach ($selectedAssignments as $assignment) {
                // Calculate a random submission date between assignment creation and due date
                $submissionDate = fake()->dateTimeBetween(
                    $assignment->created_at,
                    $assignment->due_date
                );
                
                // Determine if submission is late
                $isLate = $submissionDate > $assignment->due_date;
                
                // Calculate grade based on submission timing
                $maxGrade = $assignment->total_points;
                if ($isLate && !$assignment->allow_late_submissions) {
                    $grade = 0; // No points for late submission if not allowed
                } else {
                    // Random grade between 60% and 100% of total points if on time
                    // Random grade between 0% and 60% of total points if late but allowed
                    $minPercent = $isLate ? 0 : 60;
                    $maxPercent = $isLate ? 60 : 100;
                    $grade = round(($maxGrade * rand($minPercent, $maxPercent)) / 100, 2);
                }

                // Create the student assignment record
                DB::table('student_assignments')->insert([
                    'student_id' => $student->id,
                    'assignment_id' => $assignment->id,
                    'avg_grade' => $grade,
                    'submission_date' => $submissionDate,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
} 