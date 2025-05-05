@extends('layouts.app')

@section('title', 'Create Assignment')

@section('content')
<div class="px-6 py-4">
    <form id="assignment-form" action="{{ route('assignments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="assignment-title" class="block text-sm font-medium text-gray-700 mb-1">Assignment Title</label>
                <input type="text" id="assignment-title" name="title" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-sm" placeholder="Enter assignment title" required>
            </div>
            <div>
                <label for="assignment-class" class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                <div class="relative">
                    <select id="assignment-class" name="course_id" class="block w-full px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-sm appearance-none bg-white" required>
                        <option value="">Select a class</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }} ({{ $course->code }})</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <div class="w-4 h-4 flex items-center justify-center text-gray-400">
                            <i class="ri-arrow-down-s-line"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="assignment-due-date" class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="date" id="assignment-due-date" name="due_date" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-sm" required>
                </div>
                <div>
                    <label for="assignment-due-time" class="block text-sm font-medium text-gray-700 mb-1">Due Time</label>
                    <input type="time" id="assignment-due-time" name="due_time" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-sm" required>
                </div>
            </div>
            <div>
                <label for="assignment-points" class="block text-sm font-medium text-gray-700 mb-1">Total Points</label>
                <input type="number" id="assignment-points" name="total_points" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-sm" placeholder="Enter total points" min="0" required>
            </div>
            <div>
                <label for="assignment-description" class="block text-sm font-medium text-gray-700 mb-1">Instructions</label>
                <textarea id="assignment-description" name="instructions" rows="4" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-sm" placeholder="Enter assignment instructions and requirements" required></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Attachment</label>
                <div class="flex items-center justify-center w-full">
                    <label for="file-upload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <div class="w-10 h-10 mb-3 flex items-center justify-center text-gray-400">
                                <i class="ri-upload-cloud-line ri-2x"></i>
                            </div>
                            <p class="mb-2 text-sm text-gray-500">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-500">PDF, DOCX, or images (max. 10MB)</p>
                        </div>
                        <input id="file-upload" name="attachment" type="file" class="hidden">
                    </label>
                </div>
            </div>
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="allow_late_submissions" class="mr-2">
                    <span class="text-sm text-gray-700">Allow late submissions</span>
                </label>
            </div>
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="enable_automatic_grading" class="mr-2">
                    <span class="text-sm text-gray-700">Enable automatic grading</span>
                </label>
            </div>
        </div>
        <div class="mt-6 flex justify-end space-x-3">
            <button type="button" onclick="window.location.href='{{ route('assignments.index') }}'" class="px-4 py-2 border border-gray-300 rounded-button text-sm font-medium text-gray-700 hover:bg-gray-50 whitespace-nowrap">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-button text-sm font-medium hover:bg-indigo-700 whitespace-nowrap">Create Assignment</button>
        </div>
    </form>
</div>
@endsection 