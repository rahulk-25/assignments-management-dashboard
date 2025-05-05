<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EduConnect - Assignments</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#10B981'
                    },
                    borderRadius: {
                        'none': '0px',
                        'sm': '4px',
                        DEFAULT: '8px',
                        'md': '12px',
                        'lg': '16px',
                        'xl': '20px',
                        '2xl': '24px',
                        '3xl': '32px',
                        'full': '9999px',
                        'button': '8px'
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
    <!-- Add jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Add SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Add our assignments.js file -->
    <script src="{{ asset('js/assignments.js') }}"></script>
    <style>
        :where([class^="ri-"])::before {
            content: "\f3c2";
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }

        input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid #d1d5db;
            border-radius: 4px;
            background-color: white;
            cursor: pointer;
            position: relative;
        }

        input[type="checkbox"]:checked {
            background-color: #4F46E5;
            border-color: #4F46E5;
        }

        input[type="checkbox"]:checked::after {
            content: "";
            position: absolute;
            left: 6px;
            top: 2px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 46px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e5e7eb;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #4F46E5;
        }

        input:checked+.slider:before {
            transform: translateX(22px);
        }

        .tab-active {
            color: #4F46E5;
            border-bottom: 2px solid #4F46E5;
        }

        .progress-ring {
            transform: rotate(-90deg);
        }

        .progress-ring-circle {
            stroke-dasharray: 283;
            transition: stroke-dashoffset 0.3s;
        }
    </style>
</head>

<body>
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="hidden md:flex md:flex-col w-64 bg-white border-r border-gray-200">
            <div class="p-4 flex items-center">
                <span class="text-2xl font-['Pacifico'] text-primary">logo</span>
            </div>
            <div class="flex flex-col flex-1 overflow-y-auto">
                <nav class="flex-1 px-2 py-4 space-y-1">
                    <a href="https://readdy.ai/home/b8e20487-1c5f-4382-bb15-ebd7a3c4d48a/c7194a38-0291-4ef5-acc3-195b70ab57d1" data-readdy="true" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50">
                        <div class="w-6 h-6 mr-3 flex items-center justify-center">
                            <i class="ri-dashboard-line"></i>
                        </div>
                        Dashboard
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50">
                        <div class="w-6 h-6 mr-3 flex items-center justify-center">
                            <i class="ri-user-line"></i>
                        </div>
                        Students
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50">
                        <div class="w-6 h-6 mr-3 flex items-center justify-center">
                            <i class="ri-calendar-line"></i>
                        </div>
                        Calendar
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50">
                        <div class="w-6 h-6 mr-3 flex items-center justify-center">
                            <i class="ri-message-2-line"></i>
                        </div>
                        Messages
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-white bg-primary">
                        <div class="w-6 h-6 mr-3 flex items-center justify-center">
                            <i class="ri-file-list-line"></i>
                        </div>
                        Assignments
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50">
                        <div class="w-6 h-6 mr-3 flex items-center justify-center">
                            <i class="ri-bar-chart-line"></i>
                        </div>
                        Analytics
                    </a>
                    <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50">
                        <div class="w-6 h-6 mr-3 flex items-center justify-center">
                            <i class="ri-settings-line"></i>
                        </div>
                        Settings
                    </a>
                </nav>
                <div class="p-4 border-t border-gray-200">
                    <div class="flex items-center">
                        <img class="h-10 w-10 rounded-full object-cover" src="https://readdy.ai/api/search-image?query=professional%2520portrait%2520of%2520a%2520female%2520teacher%2520with%2520glasses%252C%2520warm%2520smile%252C%2520business%2520casual%2520attire%252C%2520indoor%2520lighting%252C%2520professional%2520headshot&width=200&height=200&seq=1&orientation=squarish" alt="User avatar">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-700">Emily Johnson</p>
                            <p class="text-xs text-gray-500">Science Teacher</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        <!-- Mobile sidebar -->
        <div class="md:hidden fixed bottom-0 left-0 right-0 z-10 bg-white border-t border-gray-200">
            <div class="flex justify-around">
                <a href="https://readdy.ai/home/b8e20487-1c5f-4382-bb15-ebd7a3c4d48a/c7194a38-0291-4ef5-acc3-195b70ab57d1" data-readdy="true" class="flex flex-col items-center py-2 text-gray-500">
                    <div class="w-6 h-6 flex items-center justify-center">
                        <i class="ri-dashboard-line"></i>
                    </div>
                    <span class="text-xs">Dashboard</span>
                </a>
                <a href="#" class="flex flex-col items-center py-2 text-gray-500">
                    <div class="w-6 h-6 flex items-center justify-center">
                        <i class="ri-user-line"></i>
                    </div>
                    <span class="text-xs">Students</span>
                </a>
                <a href="#" class="flex flex-col items-center py-2 text-gray-500">
                    <div class="w-6 h-6 flex items-center justify-center">
                        <i class="ri-message-2-line"></i>
                    </div>
                    <span class="text-xs">Messages</span>
                </a>
                <a href="#" class="flex flex-col items-center py-2 text-primary">
                    <div class="w-6 h-6 flex items-center justify-center">
                        <i class="ri-file-list-line"></i>
                    </div>
                    <span class="text-xs">Assignments</span>
                </a>
                <a href="#" class="flex flex-col items-center py-2 text-gray-500">
                    <div class="w-6 h-6 flex items-center justify-center">
                        <i class="ri-menu-line"></i>
                    </div>
                    <span class="text-xs">More</span>
                </a>
            </div>
        </div>
        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top navigation -->
            <header class="bg-white border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <div class="flex items-center md:hidden">
                            <button type="button" class="text-gray-500 hover:text-gray-600 focus:outline-none">
                                <div class="w-6 h-6 flex items-center justify-center">
                                    <i class="ri-menu-line"></i>
                                </div>
                            </button>
                            <span class="ml-2 text-xl font-['Pacifico'] text-primary">logo</span>
                        </div>
                        <div class="flex-1 flex items-center justify-center md:justify-start">
                            <div class="max-w-lg w-full">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <div class="w-5 h-5 flex items-center justify-center text-gray-400">
                                            <i class="ri-search-line"></i>
                                        </div>
                                    </div>
                                    <input type="text" class="search-assignment-input block w-full pl-10 pr-3 py-2 border-none rounded-lg bg-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white text-sm" placeholder="Search assignments by title, class, or status...">
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="relative">
                                <button type="button" class="flex text-gray-500 hover:text-gray-600 focus:outline-none">
                                    <div class="w-6 h-6 flex items-center justify-center relative">
                                        <i class="ri-notification-3-line"></i>
                                        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500"></span>
                                    </div>
                                </button>
                            </div>
                            <div class="relative ml-4">
                                <button type="button" class="flex text-gray-500 hover:text-gray-600 focus:outline-none">
                                    <div class="w-6 h-6 flex items-center justify-center">
                                        <i class="ri-question-line"></i>
                                    </div>
                                </button>
                            </div>
                            <div class="hidden md:block ml-4">
                                <button type="button" id="create-assignment-btn" class="bg-primary text-white px-4 py-2 rounded-button text-sm font-medium whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 mr-2 flex items-center justify-center">
                                            <i class="ri-add-line"></i>
                                        </div>
                                        Create Assignment
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- Role selector -->
            <div class="bg-white px-4 sm:px-6 lg:px-8 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">Assignments</h1>
                        <p class="text-sm text-gray-500">{{date('l, F j, Y');}}</p>
                    </div>
                    <div class="mt-3 sm:mt-0 flex flex-wrap items-center gap-3">
                        <div class="relative">
                            <button type="button" id="class-filter-button" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-button bg-white text-gray-700 hover:bg-gray-50 focus:outline-none whitespace-nowrap">
                                <span id="selected-class">All Classes</span>
                                <div class="w-4 h-4 ml-2 flex items-center justify-center">
                                    <i class="ri-arrow-down-s-line"></i>
                                </div>
                            </button>
                            <div id="class-filter-menu" class="hidden absolute left-0 mt-1 w-48 rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer" data-class="All Classes">All Classes</button>
                                    @foreach ($courses as $course)
                                        <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer class-filter-button-list" data-class="{{ $course->name }}">{{ $course->name }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center bg-gray-100 rounded-full p-1">
                            <button id="list-view-btn" class="flex items-center justify-center w-8 h-8 rounded-full bg-white shadow-sm">
                                <div class="w-4 h-4 flex items-center justify-center text-gray-700">
                                    <i class="ri-list-check"></i>
                                </div>
                            </button>
                            <button id="grid-view-btn" class="flex items-center justify-center w-8 h-8 rounded-full text-gray-500">
                                <div class="w-4 h-4 flex items-center justify-center">
                                    <i class="ri-grid-line"></i>
                                </div>
                            </button>
                        </div>
                        <div class="relative">
                            <button type="button" id="sort-button" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-button bg-white text-gray-700 hover:bg-gray-50 focus:outline-none whitespace-nowrap class-filter-button-list">
                                <span>Sort: Due Date</span>
                                <div class="w-4 h-4 ml-2 flex items-center justify-center">
                                    <i class="ri-arrow-down-s-line"></i>
                                </div>
                            </button>
                            <div id="sort-menu" class="hidden absolute right-0 mt-1 w-48 rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer" data-sort="Due Date">Due Date</button>
                                    <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer" data-sort="Recently Created">Recently Created</button>
                                    <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer" data-sort="Completion Rate">Completion Rate</button>
                                    <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer" data-sort="Alphabetical">Alphabetical</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main content area -->
            <main class="flex-1 overflow-y-auto bg-gray-50 pb-16 md:pb-0">
                <div class="px-4 sm:px-6 lg:px-8 py-6">
                    <!-- Tabs -->
                    <div class="mb-6 border-b border-gray-200">
                        <div class="flex overflow-x-auto">
                            <button class="tab-active px-4 py-2 text-sm font-medium whitespace-nowrap class-filter-button-list">Current Assignments</button>
                            <button class="px-4 py-2 text-sm font-medium text-gray-500 whitespace-nowrap class-filter-button-list hover:text-gray-700">Upcoming Assignments</button>
                            <button class="px-4 py-2 text-sm font-medium text-gray-500 whitespace-nowrap class-filter-button-list hover:text-gray-700">Past Assignments</button>
                        </div>
                    </div>
                    <!-- Assignment List -->
                    <p class="text-center text-gray-500 font-semibold" id="assignment-list-alert"></p>
                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-6" id="assignment-list">
                        <!-- Assignment Card 1 -->
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <div class="p-5">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center">
                                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Science 101</span>
                                            <span class="ml-2 px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                                        </div>
                                        <h3 class="mt-2 text-lg font-semibold text-gray-900">Ecosystem Research Project</h3>
                                    </div>
                                    <div class="flex">
                                        <button class="text-gray-400 hover:text-gray-500">
                                            <div class="w-6 h-6 flex items-center justify-center">
                                                <i class="ri-more-2-fill"></i>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <div class="w-4 h-4 flex items-center justify-center mr-1">
                                            <i class="ri-calendar-line"></i>
                                        </div>
                                        <span>Due: April 28, 2025 at 11:59 PM</span>
                                    </div>
                                    <div class="flex items-center mt-1 text-sm text-gray-500">
                                        <div class="w-4 h-4 flex items-center justify-center mr-1">
                                            <i class="ri-user-line"></i>
                                        </div>
                                        <span>28 students assigned</span>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-xs font-medium text-gray-700">Submission Status</span>
                                        <span class="text-xs font-medium text-gray-700">18/28 submitted</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-primary h-2 rounded-full" style="width: 64%"></div>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center justify-between">
                                    <div>
                                        <span class="text-xs text-gray-500">Average Grade</span>
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-900">87%</span>
                                            <span class="ml-1 text-xs text-green-600">↑ 3%</span>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500">Needs Grading</span>
                                        <div class="text-sm font-medium text-gray-900">4</div>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500">Late</span>
                                        <div class="text-sm font-medium text-gray-900">2</div>
                                    </div>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between">
                                    <button class="view-details-btn text-sm text-primary font-medium hover:text-indigo-700 !rounded-button" 
                                            data-assignment-id="1">
                                        View Details
                                    </button>
                                    <div class="flex space-x-2">
                                        <button class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded !rounded-button">
                                            <div class="w-4 h-4 flex items-center justify-center">
                                                <i class="ri-edit-line"></i>
                                            </div>
                                        </button>
                                        <button class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded !rounded-button">
                                            <div class="w-4 h-4 flex items-center justify-center">
                                                <i class="ri-delete-bin-line"></i>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <!-- Assignment Analytics -->
                    <div class="bg-white rounded-lg shadow-sm mb-6">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold text-gray-900">Assignment Analytics</h2>
                                <div class="relative">
                                    <button type="button" id="analytics-period-button" class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-button bg-white text-gray-700 hover:bg-gray-50 focus:outline-none whitespace-nowrap">
                                        <span>Last 30 Days</span>
                                        <div class="w-4 h-4 ml-2 flex items-center justify-center">
                                            <i class="ri-arrow-down-s-line"></i>
                                        </div>
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="col-span-2">
                                    <div id="submissionChart" class="h-64"></div>
                                </div>
                                <div>
                                    <div class="space-y-4">
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm text-gray-500">Total Assignments</p>
                                                    <p class="text-xl font-semibold text-gray-900" id="total-assignments">12</p>
                                                </div>
                                                <div class="w-10 h-10 flex items-center justify-center bg-blue-100 rounded-full text-blue-600">
                                                    <i class="ri-file-list-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm text-gray-500">Avg. Completion Rate</p>
                                                    <p class="text-xl font-semibold text-gray-900" id="completion-rate"></p>
                                                </div>
                                                <div class="w-10 h-10 flex items-center justify-center bg-green-100 rounded-full text-green-600">
                                                    <i class="ri-check-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm text-gray-500">Avg. Grade</p>
                                                    <p class="text-xl font-semibold text-gray-900" id="average-grade">B+ (87%)</p>
                                                </div>
                                                <div class="w-10 h-10 flex items-center justify-center bg-yellow-100 rounded-full text-yellow-600">
                                                    <i class="ri-medal-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Students Needing Attention -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Students Requiring Attention</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200" id="low-grade-students">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Student
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Performance
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <!-- Data will be loaded dynamically via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Create Assignment Modal -->
    <div id="create-assignment-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Create New Assignment</h3>
                <button id="close-modal-btn" class="text-gray-400 hover:text-gray-500">
                    <div class="w-6 h-6 flex items-center justify-center">
                        <i class="ri-close-line"></i>
                    </div>
                </button>
            </div>
            <div class="px-6 py-4">
                <form id="assignment-form">
                    <div class="space-y-4">
                        <div>
                            <label for="assignment-title" class="block text-sm font-medium text-gray-700 mb-1">Assignment Title</label>
                            <input type="text" id="assignment-title" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-sm" placeholder="Enter assignment title">
                        </div>
                        <div>
                            <label for="assignment-class" class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                            <div class="relative">
                                <select id="assignment-class" class="block w-full px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-sm appearance-none bg-white">
                                    <!-- <option value="science101">Science 101</option>
                                    <option value="biology202">Biology 202</option>
                                    <option value="chemistry101">Chemistry 101</option> -->
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
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
                                <input type="date" id="assignment-due-date" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-sm">
                            </div>
                            <div>
                                <label for="assignment-due-time" class="block text-sm font-medium text-gray-700 mb-1">Due Time</label>
                                <input type="time" id="assignment-due-time" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-sm">
                            </div>
                        </div>
                        <div>
                            <label for="assignment-points" class="block text-sm font-medium text-gray-700 mb-1">Total Points</label>
                            <input type="number" id="assignment-points" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-sm" placeholder="Enter total points" min="0">
                        </div>
                        <div>
                            <label for="assignment-description" class="block text-sm font-medium text-gray-700 mb-1">Instructions</label>
                            <textarea id="assignment-description" rows="4" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-sm" placeholder="Enter assignment instructions and requirements"></textarea>
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
                                    <input id="file-upload" type="file" class="hidden">
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                <span class="text-sm text-gray-700">Allow late submissions</span>
                            </label>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                <span class="text-sm text-gray-700">Enable automatic grading</span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button id="cancel-btn" class="px-4 py-2 border border-gray-300 rounded-button text-sm font-medium text-gray-700 hover:bg-gray-50 whitespace-nowrap">Cancel</button>
                <button id="save-assignment-btn" class="px-4 py-2 bg-primary text-white rounded-button text-sm font-medium hover:bg-indigo-700 whitespace-nowrap">Create Assignment</button>
                <button id="update-assignment-btn" class="hidden px-4 py-2 bg-primary text-white rounded-button text-sm font-medium hover:bg-indigo-700 whitespace-nowrap">Update Assignment</button>
            </div>
        </div>
    </div>

    <!-- Mobile Create Assignment Button -->
    <div class="md:hidden fixed right-4 bottom-20 z-20">
        <button id="mobile-create-btn" class="w-14 h-14 rounded-full bg-primary text-white shadow-lg flex items-center justify-center">
            <div class="w-6 h-6 flex items-center justify-center">
                <i class="ri-add-line ri-lg"></i>
            </div>
        </button>
    </div>

    <!-- View Assignment Details Modal -->
    <div id="view-assignment-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900" id="view-title"></h3>
                        <button type="button" class="text-gray-400 hover:text-gray-500" id="close-view-modal">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- Basic Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Course</h4>
                                <p class="mt-1 text-sm text-gray-900" id="view-course"></p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Due Date</h4>
                                <p class="mt-1 text-sm text-gray-900" id="view-due-date"></p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Total Points</h4>
                                <p class="mt-1 text-sm text-gray-900" id="view-points"></p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Status</h4>
                                <p class="mt-1 text-sm text-gray-900" id="view-status"></p>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Instructions</h4>
                            <div class="mt-1 text-sm text-gray-900 bg-gray-50 p-4 rounded-lg" id="view-instructions"></div>
                        </div>

                        <!-- Statistics -->
                        <div class="border-t border-gray-200 pt-4">
                            <h4 class="text-sm font-medium text-gray-500 mb-4">Statistics</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h5 class="text-sm font-medium text-gray-500">Submissions</h5>
                                    <p class="mt-1 text-sm text-gray-900" id="view-submissions"></p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h5 class="text-sm font-medium text-gray-500">On Time Rate</h5>
                                    <p class="mt-1 text-sm text-gray-900" id="view-on-time-rate"></p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h5 class="text-sm font-medium text-gray-500">Average Grade</h5>
                                    <p class="mt-1 text-sm text-gray-900" id="view-avg-grade"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Attachment -->
                        <div id="view-attachment-section" class="hidden">
                            <h4 class="text-sm font-medium text-gray-500">Attachment</h4>
                            <div class="mt-1">
                                <a href="#" id="view-attachment-link" class="text-sm text-primary hover:text-indigo-700 flex items-center">
                                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-3.586 3.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    <span id="view-attachment-name"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown functionality
            function setupDropdown(buttonId, menuId, optionSelector, selectedId) {
                const button = document.getElementById(buttonId);
                const menu = document.getElementById(menuId);
                const selected = document.getElementById(selectedId);

                function toggleDropdown() {
                    menu.classList.toggle('hidden');
                }

                function closeDropdown() {
                    menu.classList.add('hidden');
                }

                button.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleDropdown();
                });

                document.addEventListener('click', (e) => {
                    if (!button.contains(e.target)) {
                        closeDropdown();
                    }
                });

                const options = menu.querySelectorAll(optionSelector);
                options.forEach(option => {
                    option.addEventListener('click', () => {
                        const value = option.getAttribute('data-' + buttonId.split('-')[0]);
                        selected.textContent = value;
                        closeDropdown();
                    });
                });
            }

            // Setup dropdowns
            setupDropdown('class-filter-button', 'class-filter-menu', '[data-class]', 'selected-class');
            setupDropdown('sort-button', 'sort-menu', '[data-sort]', null);

            // View toggle functionality
            const listViewBtn = document.getElementById('list-view-btn');
            const gridViewBtn = document.getElementById('grid-view-btn');

            listViewBtn.addEventListener('click', () => {
                listViewBtn.classList.add('bg-white', 'shadow-sm');
                listViewBtn.classList.remove('text-gray-500');
                gridViewBtn.classList.remove('bg-white', 'shadow-sm');
                gridViewBtn.classList.add('text-gray-500');
            });

            gridViewBtn.addEventListener('click', () => {
                gridViewBtn.classList.add('bg-white', 'shadow-sm');
                gridViewBtn.classList.remove('text-gray-500');
                listViewBtn.classList.remove('bg-white', 'shadow-sm');
                listViewBtn.classList.add('text-gray-500');
            });

            // Tab functionality
            const tabs = document.querySelectorAll('button.whitespace-nowrap');
            tabs.forEach(tab => {
                if (!tab.classList.contains('tab-active')) {
                    tab.addEventListener('click', () => {
                        // Remove active class from all tabs
                        tabs.forEach(t => {
                            t.classList.remove('tab-active');
                            t.classList.add('text-gray-500');
                        });
                        // Add active class to clicked tab
                        tab.classList.add('tab-active');
                        tab.classList.remove('text-gray-500');
                    });
                }
            });

            // Modal functionality
            const createBtn = document.getElementById('create-assignment-btn');
            const mobileCreateBtn = document.getElementById('mobile-create-btn');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const cancelBtn = document.getElementById('cancel-btn');
            const modal = document.getElementById('create-assignment-modal');
            const saveAssignmentBtn = document.getElementById('save-assignment-btn');

            function openModal() {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeModal() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            createBtn.addEventListener('click', openModal);
            mobileCreateBtn.addEventListener('click', openModal);
            closeModalBtn.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', closeModal);

            saveAssignmentBtn.addEventListener('click', () => {
                // Here you would normally save the form data
                // For demo purposes, we'll just close the modal and show a notification
                // closeModal();
                // showNotification('Assignment created successfully!', 'success');
            });

            // Notification function
            function showNotification(message, type) {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 ${type === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'} px-4 py-2 rounded-lg shadow-md z-50 animate-fade-in-out`;
                notification.textContent = message;
                document.body.appendChild(notification);
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            // Add animation styles
            const style = document.createElement('style');
            style.textContent = `
@keyframes fadeInOut {
0% { opacity: 0; transform: translateY(-10px); }
10% { opacity: 1; transform: translateY(0); }
90% { opacity: 1; transform: translateY(0); }
100% { opacity: 0; transform: translateY(-10px); }
}
.animate-fade-in-out {
animation: fadeInOut 3s ease-in-out forwards;
}
`;
            // document.head.appendChild(style);

            // // Submission Chart
            
        });
    </script>
</body>

</html>