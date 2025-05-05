<aside class="w-64 bg-white shadow-lg">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 border-b">
            <span class="text-2xl font-pacifico text-primary">EduConnect</span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-4 space-y-2">
            <a href="{{ route('assignments.index') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="ri-book-open-line text-xl mr-3"></i>
                <span>Assignments</span>
            </a>
            <a href="#" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="ri-calendar-line text-xl mr-3"></i>
                <span>Calendar</span>
            </a>
            <a href="#" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="ri-chat-1-line text-xl mr-3"></i>
                <span>Messages</span>
            </a>
            <a href="#" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                <i class="ri-settings-3-line text-xl mr-3"></i>
                <span>Settings</span>
            </a>
        </nav>

        <!-- User Profile -->
        <div class="p-4 border-t">
            <div class="flex items-center">
                <img src="https://ui-avatars.com/api/?name=John+Doe" alt="User" class="w-8 h-8 rounded-full">
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-700">John Doe</p>
                    <p class="text-xs text-gray-500">Student</p>
                </div>
            </div>
        </div>
    </div>
</aside> 