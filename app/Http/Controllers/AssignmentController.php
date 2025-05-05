<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the assignments.
     */
    public function index()
    {
        $assignments = Assignment::with('course')->get();
        $courses = Course::all();
        return view('assignments.index', compact('assignments', 'courses'));
    }

    /**
     * Show the form for creating a new assignment.
     */
    public function create()
    {
        $courses = Course::all();
        return view('assignments.create', compact('courses'));
    }

    /**
     * Store a newly created assignment in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'course_id' => 'required|exists:courses,id',
                'due_date' => 'required|date',
                'due_time' => 'required',
                'total_points' => 'required|integer|min:0',
                'instructions' => 'required|string',
                'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
                'allow_late_submissions' => 'boolean',
                'enable_automatic_grading' => 'boolean'
            ]);

            // Combine date and time
            $validated['due_date'] = $validated['due_date'] . ' ' . $validated['due_time'];
            // unset($validated['due_time']);

            // Handle file upload
            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store('assignments', 'public');
                $validated['attachment'] = $path;
            }

            // Start database transaction
            DB::beginTransaction();

            try {
                // Create the assignment
                $assignment = Assignment::create($validated);

                // Get all students enrolled in the course
                $students = \App\Models\Student::whereHas('courses', function($query) use ($validated) {
                    $query->where('course_id', $validated['course_id']);
                })->get();

                // Prepare student assignment data
                $studentAssignments = [];
                foreach ($students as $student) {
                    $studentAssignments[] = [
                        'student_id' => $student->id,
                        'assignment_id' => $assignment->id,
                        'status' => 'pending',
                        'submission_date' => null,
                        'grade' => null,
                        'feedback' => null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                // Bulk insert student assignments
                if (!empty($studentAssignments)) {
                    DB::table('student_assignments')->insert($studentAssignments);
                }

                // Commit transaction
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Assignment created and students assigned successfully!',
                    'data' => [
                        'assignment' => $assignment,
                        'assigned_students_count' => count($students)
                    ]
                ]);

            } catch (\Exception $e) {
                // Rollback transaction on error
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified assignment.
     */
    public function show($id)
    {
        try {
            $assignment = Assignment::with(['course', 'students'])
                ->withCount([
                    'students as total_students',
                    'students as submissions_count' => function($query) {
                        $query->whereNotNull('submission_date');
                    }
                ])
                ->withCount(['students as on_time_submissions' => function($query) {
                    $query->whereNotNull('submission_date')
                          ->whereColumn('student_assignments.submission_date', '<=', 'assignments.due_date');
                }])
                ->withAvg('students as avg_grade', 'student_assignments.avg_grade')
                ->findOrFail($id);

            // Calculate on-time rate
            $onTimeRate = $assignment->submissions_count > 0 
                ? round(($assignment->on_time_submissions / $assignment->submissions_count) * 100)
                : 0;

            // Format attachment data if exists
            $attachment = null;
            if ($assignment->attachment) {
                $attachment = [
                    'name' => basename($assignment->attachment),
                    'url' => Storage::url($assignment->attachment)
                ];
            }

            // Determine status based on due date
            $status = now() > $assignment->due_date ? 'Completed' : 'Active';

            return response()->json([
                'success' => true,
                'data' => [
                    'title' => $assignment->title,
                    'course_name' => $assignment->course->name,
                    'due_date' => $assignment->due_date,
                    'total_points' => $assignment->total_points,
                    'status' => $status,
                    'instructions' => $assignment->instructions,
                    'submissions_count' => $assignment->submissions_count,
                    'total_students' => $assignment->total_students,
                    'on_time_rate' => $onTimeRate,
                    'avg_grade' => round($assignment->avg_grade ?? 0, 2),
                    'attachment' => $attachment
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified assignment.
     */
    public function edit(Assignment $assignment)
    {
        $courses = Course::all();
        return view('assignments.edit', compact('assignment', 'courses'));
    }

    /**
     * Update the specified assignment in storage.
     */
    public function update(Request $request, Assignment $assignment)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'course_id' => 'required|exists:courses,id',
                'due_date' => 'required|date',
                'due_time' => 'required',
                'total_points' => 'required|integer|min:0',
                'instructions' => 'required|string',
                'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
                'allow_late_submissions' => 'boolean',
                'enable_automatic_grading' => 'boolean'
            ]);

            // Combine date and time
            $validated['due_date'] = $validated['due_date'] . ' ' . $validated['due_time'];
            unset($validated['due_time']);

            // Handle file upload
            if ($request->hasFile('attachment')) {
                // Delete old file if exists
                if ($assignment->attachment) {
                    Storage::disk('public')->delete($assignment->attachment);
                }
                $path = $request->file('attachment')->store('assignments', 'public');
                $validated['attachment'] = $path;
            }

            $assignment->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Assignment updated successfully!',
                'data' => $assignment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified assignment from storage.
     */
    public function destroy(Assignment $assignment)
    {
        try {
            // Delete attachment if exists
            if ($assignment->attachment) {
                Storage::disk('public')->delete($assignment->attachment);
            }
            
            $assignment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Assignment deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get assignments with detailed statistics
     */
    public function getAssignmentStats(Request $request)
    {
        try {
            $query = Assignment::with(['course', 'students'])
                ->select('assignments.*')
                ->withCount([
                    'students as total_students',
                    'students as on_time_submissions' => function ($query) {
                        $query->whereColumn('student_assignments.submission_date', '<=', 'assignments.due_date');
                    },
                    'students as late_submissions' => function ($query) {
                        $query->whereColumn('student_assignments.submission_date', '>', 'assignments.due_date');
                    }
                ])
                ->withAvg('students as current_avg_grade', 'student_assignments.avg_grade')
                ->join('courses', 'assignments.course_id', '=', 'courses.id');

            // Filter by time period (default: current and future)
            $timePeriod = $request->input('time_period', 'current_future');
            switch ($timePeriod) {
                case 'past':
                    $query->where('due_date', '<', now());
                    break;
                case 'current':
                    $query->whereDate('due_date', now());
                    break;
                case 'future':
                    $query->where('due_date', '>', now());
                    break;
                case 'current_future':
                    $query->where('due_date', '>=', now());
                    break;
                default:
                    // All assignments
                    break;
            }

            // Filter by course
            if ($request->has('course_id')) {
                $query->where('course_id', $request->course_id);
            }

            // Filter by course name
            if ($request->has('course_name')) {
                $query->where('courses.name', 'LIKE', '%' . $request->course_name . '%');
            }

            // Search by title or course
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('assignments.title', 'LIKE', '%' . $search . '%')
                        ->orWhere('courses.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('courses.code', 'LIKE', '%' . $search . '%');
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('assignments.status', $request->status);
            }

            // Get assignments with statistics
            $assignments = $query->get()->map(function ($assignment) {
                // Calculate submission percentage
                $totalSubmissions = $assignment->total_students ?: 1; // Avoid division by zero
                $onTimePercentage = ($assignment->on_time_submissions / $totalSubmissions) * 100;
                $latePercentage = ($assignment->late_submissions / $totalSubmissions) * 100;

                // Calculate grade trend
                $previousAvgGrade = $assignment->students()
                    ->where('student_assignments.created_at', '<', now()->subDays(7))
                    ->avg('student_assignments.avg_grade') ?: 0;

                $currentAvgGrade = $assignment->current_avg_grade ?: 0;
                $gradeTrend = $previousAvgGrade > 0 
                    ? (($currentAvgGrade - $previousAvgGrade) / $previousAvgGrade) * 100 
                    : 0;

                // Determine assignment status
                $status = now() > $assignment->due_date ? 'completed' : 'active';

                return [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'course' => [
                        'id' => $assignment->course->id,
                        'name' => $assignment->course->name,
                        'code' => $assignment->course->code,
                    ],
                    'due_date' => $assignment->due_date,
                    'total_points' => $assignment->total_points,
                    'status' => $status,
                    'statistics' => [
                        'total_students' => $assignment->total_students,
                        'submissions' => [
                            'on_time' => [
                                'count' => $assignment->on_time_submissions,
                                'percentage' => round($onTimePercentage, 2)
                            ],
                            'late' => [
                                'count' => $assignment->late_submissions,
                                'percentage' => round($latePercentage, 2)
                            ]
                        ],
                        'grades' => [
                            'current_average' => round($currentAvgGrade, 2),
                            'trend' => [
                                'percentage' => round($gradeTrend, 2),
                                'direction' => $gradeTrend > 0 ? 'increase' : ($gradeTrend < 0 ? 'decrease' : 'stable')
                            ]
                        ]
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'assignments' => $assignments,
                    'filters' => [
                        'time_period' => $timePeriod,
                        'course_id' => $request->course_id,
                        'course_name' => $request->course_name,
                        'search' => $request->search,
                        'status' => $request->status
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching assignment statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    public function list(Request $request)
    {
        try {
            $query = Assignment::query();

            // Apply time period filter
            if ($request->has('time_period')) {
                $query->byTimePeriod($request->time_period);
            }

            // Apply course filter
            if ($request->has('course_name')) {
                $query->whereHas('course', function($q) use ($request) {
                    $q->where('name', $request->course_name);
                });
            }

            // Apply search filter
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            $assignments = $query->with(['course', 'statistics'])
                               ->orderBy('due_date', 'asc')
                               ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'assignments' => $assignments
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch assignments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get students with low grades and their assignment counts
     */
    public function getLowGradeStudents(Request $request)
    {
        try {
            $limit = $request->input('limit', 1);
            $page = $request->input('page', 1);
            
            $query = \App\Models\Student::with(['assignments' => function($query) {
                $query->select(
                    'student_assignments.*',
                    'a.title',
                    'a.due_date',
                    'courses.name as course_name'
                )
                ->join('assignments as a', 'student_assignments.assignment_id', '=', 'a.id')
                ->join('courses', 'a.course_id', '=', 'courses.id')
                ->orderBy('student_assignments.submission_date', 'desc');
            }])
            ->select('students.*')
            ->withCount(['assignments as low_grade_assignments' => function($query) {
                $query->where('student_assignments.avg_grade', '<', 70);
            }])
            ->withCount(['assignments as critical_grade_assignments' => function($query) {
                $query->where('student_assignments.avg_grade', '<', 50);
            }])
            ->withCount(['assignments as missing_assignments' => function($query) {
                $query->whereNull('student_assignments.submission_date')
                      ->where('a.due_date', '<', now())
                      ->join('assignments as a', 'student_assignments.assignment_id', '=', 'a.id');
            }])
            ->withCount(['assignments as late_submissions' => function($query) {
                $query->whereNotNull('student_assignments.submission_date')
                      ->whereColumn('student_assignments.submission_date', '>', 'a.due_date')
                      ->join('assignments as a', 'student_assignments.assignment_id', '=', 'a.id');
            }])
            ->withAvg('assignments as average_grade', 'student_assignments.avg_grade')
            ->having('average_grade', '<', 70);

            $total = $query->count();
            $students = $query->skip(($page - 1) * $limit)
                            ->take($limit)
                            ->get()
                            ->map(function($student) {
                                // Get the primary course (most recent assignment's course)
                                $primaryCourse = $student->assignments->first()?->course_name ?? 'No Course';
                                
                                // Get last submission date
                                $lastSubmission = $student->assignments
                                    ->whereNotNull('submission_date')
                                    ->sortByDesc('submission_date')
                                    ->first();
                                
                                // Get last 3 assignment grades
                                $lastThreeGrades = $student->assignments
                                    ->whereNotNull('avg_grade')
                                    ->sortByDesc('submission_date')
                                    ->take(3)
                                    ->map(fn($a) => $this->getGradeLetter($a->avg_grade))
                                    ->join(', ');

                                // Calculate average delay for late submissions
                                $lateSubmissions = $student->assignments
                                    ->filter(fn($a) => 
                                        $a->submission_date && 
                                        $a->due_date && 
                                        \Carbon\Carbon::parse($a->submission_date) > \Carbon\Carbon::parse($a->due_date)
                                    );
                                
                                $avgDelay = $lateSubmissions->isNotEmpty() 
                                    ? round($lateSubmissions->avg(fn($a) => 
                                        \Carbon\Carbon::parse($a->submission_date)->diffInDays(\Carbon\Carbon::parse($a->due_date))
                                    ))
                                    : 0;

                                // Determine primary issue
                                $primaryIssue = $student->missing_assignments > 0 ? 'missing_assignments' :
                                              ($student->critical_grade_assignments > 0 ? 'critical_grades' :
                                              ($student->late_submissions > 0 ? 'late_submissions' : 'low_grades'));

                                return [
                                    'id' => $student->id,
                                    'name' => $student->name,
                                    'email' => $student->email,
                                    'course' => $primaryCourse,
                                    'average_grade' => round($student->average_grade, 2),
                                    'last_submission_date' => $lastSubmission?->submission_date,
                                    'last_three_grades' => $lastThreeGrades,
                                    'late_submissions_count' => $student->late_submissions,
                                    'avg_delay_days' => $avgDelay,
                                    'missing_assignments_count' => $student->missing_assignments,
                                    'primary_issue' => $primaryIssue,
                                    'issues' => [
                                        'critical_grades' => $student->critical_grade_assignments > 0,
                                        'low_grades' => $student->low_grade_assignments > 0,
                                        'missing_assignments' => $student->missing_assignments > 0,
                                        'late_submissions' => $student->late_submissions > 0
                                    ]
                                ];
                            });

            return response()->json([
                'success' => true,
                'data' => [
                    'students' => $students,
                    'pagination' => [
                        'total' => $total,
                        'per_page' => $limit,
                        'current_page' => $page,
                        'last_page' => ceil($total / $limit),
                        'from' => ($page - 1) * $limit + 1,
                        'to' => min($page * $limit, $total)
                    ],
                    'summary' => [
                        'critical_students' => $students->where('primary_issue', 'critical_grades')->count(),
                        'low_grade_students' => $students->where('primary_issue', 'low_grades')->count(),
                        'missing_assignments_students' => $students->where('primary_issue', 'missing_assignments')->count(),
                        'late_submissions_students' => $students->where('primary_issue', 'late_submissions')->count()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching low grade students: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getGradeLetter($grade) {
        if ($grade >= 90) return 'A';
        if ($grade >= 80) return 'B';
        if ($grade >= 70) return 'C';
        if ($grade >= 60) return 'D';
        return 'F';
    }

    /**
     * Get assignment analytics with filters
     */
    public function getAssignmentAnalyticsNew(Request $request)
    {
        try {
            $totalStudents = DB::table('students')->count();

            // Prepare 4 week ranges (last 30 days)
            $weeks = [];
            $now = now();
            for ($i = 3; $i >= 0; $i--) {
                $start = $now->copy()->subDays(($i + 1) * 7 - 1)->startOfDay();
                $end = $now->copy()->subDays($i * 7)->endOfDay();
                $weeks[] = [
                    'label' => $start->format('M d') . ' - ' . $end->format('M d'),
                    'start' => $start,
                    'end' => $end,
                ];
            }

            $dates = [];
            $submissionData = [];
            $onTimeRateData = [];
            $avgGradeData = [];

            foreach ($weeks as $week) {
                // Get assignments created in this week
                $assignments = Assignment::whereBetween('created_at', [$week['start'], $week['end']])->pluck('id');
                $assignmentCount = $assignments->count();

                // Get all student assignments for these assignments
                $studentAssignments = DB::table('student_assignments')
                    ->whereIn('assignment_id', $assignments)
                    ->get();

                $totalSubmissions = $studentAssignments->count();
                $onTimeSubmissions = $studentAssignments->where('submission_date', '<=', 'due_date')->count();
                $avgGrade = $studentAssignments->whereNotNull('avg_grade')->avg('avg_grade');

                // Calculate percentages
                $submissionPercent = ($totalStudents > 0 && $assignmentCount > 0)
                    ? round(($totalSubmissions / ($totalStudents * $assignmentCount)) * 100)
                    : 0;
                $onTimePercent = ($totalSubmissions > 0)
                    ? round(($onTimeSubmissions / $totalSubmissions) * 100)
                    : 0;
                $avgGrade = $avgGrade ? round($avgGrade, 2) : 0;

                $dates[] = $week['label'];
                $submissionData[] = $submissionPercent;
                $onTimeRateData[] = $onTimePercent;
                $avgGradeData[] = $avgGrade;
            }

            $gradeDistribution = DB::table('student_assignments')
                ->select(
                    DB::raw('COUNT(*) as count'),
                    DB::raw('CASE 
                        WHEN avg_grade >= 90 THEN "A"
                        WHEN avg_grade >= 80 THEN "B"
                        WHEN avg_grade >= 70 THEN "C"
                        WHEN avg_grade >= 60 THEN "D"
                        ELSE "F"
                    END as grade_letter')
                )
                ->join('assignments', 'student_assignments.assignment_id', '=', 'assignments.id')
                ->whereNotNull('avg_grade')
                ->groupBy('grade_letter')
                ->get();

            $data = [
                'summary' => [
                    'total_assignments' => $assignmentCount,
                    'total_students' => $totalStudents,
                    'total_submissions' => $totalSubmissions,
                    'on_time_submissions' => $onTimeSubmissions,
                    'completion_rate' => $submissionPercent,
                    'average_grade' => $avgGrade,
                    'grade_letter' => $this->getGradeLetter($avgGrade)
                ],
                'chart_data' => [
                    'dates' => $dates,
                    'submissions' => $submissionData,
                    'on_time_rate' => $onTimeRateData,
                    'average_grade' => $avgGradeData
                ],
                'grade_distribution' => $gradeDistribution
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getAssignmentAnalytics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching assignment analytics: ' . $e->getMessage()
            ], 500);
        }
    }
} 