<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'course_id',
        'due_date',
        'due_time',
        'total_points',
        'instructions',
        'attachment',
        'allow_late_submissions',
        'enable_automatic_grading',
        'status'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'allow_late_submissions' => 'boolean',
        'enable_automatic_grading' => 'boolean',
        'status' => 'boolean'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_assignments')
            ->withPivot('avg_grade', 'submission_date')
            ->withTimestamps();
    }

    public function studentAssignments()
    {
        return $this->hasMany(StudentAssignment::class);
    }

    public function scopeByTimePeriod($query, $timePeriod)
    {
        $now = now();
        
        switch ($timePeriod) {
            case 'past':
                return $query->where('due_date', '<', $now);
            
            case 'future':
                return $query->where('due_date', '>', $now);
            
            case 'current_future':
                return $query->where('due_date', '>=', $now);
            
            default:
                return $query;
        }
    }
}
