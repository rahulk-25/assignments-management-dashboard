<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssignmentController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Assignment routes
Route::get('/assignments-list', [AssignmentController::class, 'getAssignmentStats']);
Route::post('/assignments', [AssignmentController::class, 'store']);
Route::get('/assignments/{id}', [AssignmentController::class, 'show']);
Route::post('/assignments/{id}', [AssignmentController::class, 'update']);
Route::delete('/assignments/{id}', [AssignmentController::class, 'destroy']);
Route::get('/assignments/{id}/edit', [AssignmentController::class, 'edit']);
Route::get('/students/low-grades', [AssignmentController::class, 'getLowGradeStudents']);
Route::get('/assignments-analytics', [AssignmentController::class, 'getAssignmentAnalyticsNew']);