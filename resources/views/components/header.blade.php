<header class="bg-white shadow-sm">
    <div class="flex items-center justify-between px-6 py-4">
        <!-- Search Bar -->
        <div class="flex-1 max-w-xl">
            <div class="relative">
                <input type="text" placeholder="Search..." class="w-full px-4 py-2 pl-10 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                <i class="ri-search-line absolute left-3 top-2.5 text-gray-400"></i>
            </div>
        </div>

        <!-- Right Side Actions -->
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <button class="relative p-2 text-gray-600 rounded-full hover:bg-gray-100">
                <i class="ri-notification-3-line text-xl"></i>
                <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- Messages -->
            <button class="relative p-2 text-gray-600 rounded-full hover:bg-gray-100">
                <i class="ri-message-3-line text-xl"></i>
                <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- User Menu -->
            <div class="relative">
                <button class="flex items-center space-x-2 focus:outline-none">
                    <img src="https://ui-avatars.com/api/?name=John+Doe" alt="User" class="w-8 h-8 rounded-full">
                    <i class="ri-arrow-down-s-line"></i>
                </button>
            </div>
        </div>
    </div>
</header> 