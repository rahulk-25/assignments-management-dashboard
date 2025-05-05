<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'student_course', 'student_id', 'course_id')
            ->withTimestamps();
    }

    public function assignments()
    {
        return $this->belongsToMany(Assignment::class, 'student_assignments', 'student_id', 'assignment_id')
            ->withPivot(['status', 'submission_date', 'avg_grade', 'feedback'])
            ->withTimestamps();
    }

    public function studentAssignments()
    {
        return $this->hasMany(StudentAssignment::class);
    }
} 