<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentAssignment extends Pivot
{
    use HasFactory;

    protected $table = 'student_assignments';

    protected $fillable = [
        'student_id',
        'assignment_id',
        'avg_grade',
        'submission_date'
    ];

    protected $casts = [
        'submission_date' => 'datetime',
        'avg_grade' => 'decimal:2'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
} 