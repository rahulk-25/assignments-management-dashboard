// Handle form submission and assignments list
$(document).ready(function() {
    // Create/Update form submission
    $('#save-assignment-btn').on('click', function(e) {
        e.preventDefault();

        // Validate required fields
        const title = $('#assignment-title').val();
        const courseId = $('#assignment-class').val();
        const dueDate = $('#assignment-due-date').val();
        const dueTime = $('#assignment-due-time').val();
        const totalPoints = $('#assignment-points').val();
        const description = $('#assignment-description').val();
        if (!title || !courseId || !dueDate || !dueTime || !totalPoints || !description) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all required fields'
            });
            return;
        }
        // Show loading spinner
        Swal.fire({
            title: 'Creating assignment...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Get form data
        const formData = new FormData();
        formData.append('title', title);
        formData.append('course_id', courseId);
        formData.append('due_date', `${dueDate}`);
        formData.append('due_time', `${dueTime}`);
        formData.append('total_points', totalPoints);
        formData.append('instructions', description);
        formData.append('allow_late_submissions', $('#assignment-form input[name="allow_late_submissions"]').is(':checked') ? 1 : 0);
        formData.append('enable_auto_grading', $('#assignment-form input[name="enable_auto_grading"]').is(':checked') ? 1 : 0);
        
        // Handle file upload if present
        const fileInput = $('#file-upload')[0];
        if (fileInput.files.length > 0) {
            formData.append('attachment', fileInput.files[0]);
        }

        $.ajax({
            url: '/api/assignments', // Updated URL to match your API endpoint
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Assignment created successfully',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    // Close the modal
                    $('#create-assignment-modal').addClass('hidden');
                    // Reset form
                    $('#assignment-form')[0].reset();
                    // Scroll to top
                    $('html, body').animate({ scrollTop: 0 }, 'fast');
                    // Refresh assignments list
                    fetchAssignments(getCurrentFilters());
                });
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || 'Failed to create assignment';
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage
                });
            }
        });
    });

    // Reset form when modal is closed
    $('#close-modal-btn, #cancel-btn').on('click', function() {
        $('#assignment-form')[0].reset();
    });

    // Create a function to get all current filters
    function getCurrentFilters() {
        return {
            time_period: getActiveTimePeriod(),
            course_name: $('#selected-class').text() !== 'All Classes' ? $('#selected-class').text() : null,
            search: $('.search-assignment-input').val() || null
        };
    }

    // Function to fetch and render assignments with filters
    function fetchAssignments(filters = {}) {
        // Remove null/undefined values from filters
        Object.keys(filters).forEach(key => {
            if (filters[key] === null || filters[key] === undefined) {
                delete filters[key];
            }
        });

        // Show loading spinner
        Swal.fire({
            title: 'Loading assignments...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '/api/assignments-list',
            type: 'GET',
            data: filters,
            success: function(response) {
                Swal.close();
                if (response.success) {
                    renderAssignments(response.data.assignments);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to fetch assignments'
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                const errorMessage = xhr.responseJSON?.message || 'An error occurred';
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage
                });
            }
        });
    }

    // Function to render assignments
    function renderAssignments(assignments) {
        const container = $('#assignment-list');
        container.empty();
        console.log(container);
        if(assignments.length === 0){
            $('#assignment-list-alert').text('No assignments found.');
            return;
        }
        $('#assignment-list-alert').text('');
        assignments.forEach(assignment => {
            // Calculate status color and trend indicators
            console.log(assignment.status);
            const statusColor = assignment.status === 'active' ? 'green' : 'gray';
            const trendColor = getTrendColor(assignment.statistics.grades.trend.direction);
            const trendIcon = getTrendIcon(assignment.statistics.grades.trend.direction);
            const trendPercentage = Math.abs(assignment.statistics.grades.trend.percentage).toFixed(1);

            // Format date
            const dueDate = new Date(assignment.due_date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            // Calculate submission percentages
            const totalSubmissions = assignment.statistics.submissions.on_time.count + 
                                   assignment.statistics.submissions.late.count;
            const submissionPercentage = ((totalSubmissions / assignment.statistics.total_students) * 100).toFixed(0);

            const card = `
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="flex items-center">
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                        ${assignment.course.name}
                                    </span>
                                    <span class="ml-2 px-2 py-1 text-xs font-medium bg-${statusColor}-100 text-${statusColor}-800 rounded-full">
                                        ${assignment.status}
                                    </span>
                                </div>
                                <h3 class="mt-2 text-lg font-semibold text-gray-900">${assignment.title}</h3>
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
                                <span>Due: ${dueDate}</span>
                            </div>
                            <div class="flex items-center mt-1 text-sm text-gray-500">
                                <div class="w-4 h-4 flex items-center justify-center mr-1">
                                    <i class="ri-user-line"></i>
                                </div>
                                <span>${assignment.statistics.total_students} students assigned</span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-medium text-gray-700">Submission Status</span>
                                <span class="text-xs font-medium text-gray-700">
                                    ${totalSubmissions}/${assignment.statistics.total_students} submitted
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full" style="width: ${submissionPercentage}%"></div>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-gray-500">Average Grade</span>
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-900">
                                        ${assignment.statistics.grades.current_average}%
                                    </span>
                                    <span class="ml-1 text-xs ${trendColor}">
                                        ${trendIcon} ${trendPercentage}%
                                    </span>
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">On Time</span>
                                <div class="text-sm font-medium text-gray-900">
                                    ${assignment.statistics.submissions.on_time.count}
                                </div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Late</span>
                                <div class="text-sm font-medium text-gray-900">
                                    ${assignment.statistics.submissions.late.count}
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between">
                            <button onclick="viewAssignmentDetails(${assignment.id})"
                                    class="text-sm text-primary font-medium hover:text-indigo-700 !rounded-button">
                                View Details
                            </button>
                            <div class="flex space-x-2">
                                <button data-assignment-id="${assignment.id}" 
                                        class="edit-assignment-btn p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded !rounded-button">
                                    <div class="w-4 h-4 flex items-center justify-center">
                                        <i class="ri-edit-line"></i>
                                    </div>
                                </button>
                                <button onclick="deleteAssignment(${assignment.id})" 
                                        class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded !rounded-button">
                                    <div class="w-4 h-4 flex items-center justify-center">
                                        <i class="ri-delete-bin-line"></i>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    // Helper function to get trend color
    function getTrendColor(direction) {
        switch (direction) {
            case 'increase':
                return 'text-green-600';
            case 'decrease':
                return 'text-red-600';
            default:
                return 'text-gray-600';
        }
    }

    // Helper function to get trend icon
    function getTrendIcon(direction) {
        switch (direction) {
            case 'increase':
                return '↑';
            case 'decrease':
                return '↓';
            default:
                return '→';
        }
    }

    // Handle time period tab clicks
    $('.class-filter-button-list').on('click', function() {
        // Remove active class from all tabs
        $('.class-filter-button-list').removeClass('tab-active text-primary').addClass('text-gray-500');
        // Add active class to clicked tab
        $(this).addClass('tab-active text-primary').removeClass('text-gray-500');
        
        // Get the time period based on the button text
        let timePeriod;
        switch($(this).text().trim()) {
            case 'Current Assignments':
                timePeriod = 'current_future';
                break;
            case 'Upcoming Assignments':
                timePeriod = 'future';
                break;
            case 'Past Assignments':
                timePeriod = 'past';
                break;
            default:
                timePeriod = 'current_future';
        }

        // Fetch assignments with the selected time period
        fetchAssignments({
            ...getCurrentFilters(),
            time_period: timePeriod
        });
    });

    // Handle course filter clicks
    $('.class-filter-button-list').on('click', function() {
        const courseName = $(this).data('class');
        const currentClass = $('#selected-class').text();
        
        // Only proceed if selecting a different course
        if (currentClass !== courseName) {
            $('#selected-class').text(courseName);
            fetchAssignments(getCurrentFilters());
        }
    });

    // Helper function to get active time period
    function getActiveTimePeriod() {
        const activeTab = $('.tab-active').text().trim();
        switch(activeTab) {
            case 'Current Assignments':
                return 'current_future';
            case 'Upcoming Assignments':
                return 'future';
            case 'Past Assignments':
                return 'past';
            default:
                return 'current_future';
        }
    }

    // Handle search with debounce
    let searchTimeout;
    $('.search-assignment-input').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchAssignments(getCurrentFilters());
        }, 300);
    });

    // Initial load with current assignments only
    fetchAssignments({ time_period: 'current_future' });

    // Delete assignment function
    window.deleteAssignment = function(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/assignments/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            fetchAssignments(getCurrentFilters());
                            fetchAssignmentAnalytics();
                        });
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON?.message || 'An error occurred';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage
                        });
                    }
                });
            }
        });
    };

    // Handle edit button clicks
    $(document).on('click', '.edit-assignment-btn', function(e) {
        e.preventDefault();
        const assignmentId = $(this).data('assignment-id');
        
        // Show loading spinner
        Swal.fire({
            title: 'Loading assignment...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fetch assignment data
        $.ajax({
            url: `/api/assignments/${assignmentId}`,
            type: 'GET',
            success: function(response) {
                Swal.close();
                populateAndShowModal(response.data);
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to load assignment details'
                });
            }
        });
    });

    // Function to populate modal with assignment data
    function populateAndShowModal(assignment) {
        // Reset form first
        $('#assignment-form')[0].reset();
        
        // Set form to edit mode
        $('#assignment-form').attr('data-mode', 'edit');
        $("#modal-title").text("Edit Assignment");
        $('#save-assignment-btn').text("Update Assignment");
        $('#save-assignment-btn').hide();
        $('#update-assignment-btn').show();
        $('#assignment-form').attr('data-id', assignment.id);
        
        // Populate form fields
        $('#assignment-title').val(assignment.title);
        $('#assignment-class').val(assignment.course_id);
        
        // Split datetime into date and time
        const dueDateTime = new Date(assignment.due_date);
        const dueDate = dueDateTime.toISOString().split('T')[0];
        const dueTime = dueDateTime.toLocaleTimeString('en-US', { 
            hour12: true, 
            hour: '2-digit', 
            minute: '2-digit'
        });
        
        $('#assignment-due-date').val(dueDate);
        $('#assignment-due-time').val(assignment.due_time);
        $('#assignment-points').val(assignment.total_points);
        $('#assignment-description').val(assignment.instructions);
        
        // Set checkboxes
        $('input[name="allow_late_submissions"]').prop('checked', assignment.allow_late_submissions);
        $('input[name="enable_auto_grading"]').prop('checked', assignment.enable_auto_grading);

        // Update modal title and button text
        $('.modal-title').text('Edit Assignment');
        $('#submit-btn').text('Update Assignment');

        // Show modal
        $('#create-assignment-modal').removeClass('hidden');
    }

    // Modify form submission to handle both create and edit
    $('#update-assignment-btn').on('click', function(e) {
        e.preventDefault();

        // Get form data
        const title = $('#assignment-title').val();
        const courseId = $('#assignment-class').val();
        const dueDate = $('#assignment-due-date').val();
        const dueTime = $('#assignment-due-time').val();
        const totalPoints = $('#assignment-points').val();
        const description = $('#assignment-description').val();
        if (!title || !courseId || !dueDate || !dueTime || !totalPoints || !description) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all required fields'
            });
            return;
        }
        // Show loading spinner
        Swal.fire({
            title: 'updating assignment...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Get form data
        const formData = new FormData();
        formData.append('title', title);
        formData.append('course_id', courseId);
        formData.append('due_date', `${dueDate}`);
        formData.append('due_time', `${dueTime}`);
        formData.append('total_points', totalPoints);
        formData.append('instructions', description);
        formData.append('allow_late_submissions', $('#assignment-form input[name="allow_late_submissions"]').is(':checked') ? 1 : 0);
        formData.append('enable_auto_grading', $('#assignment-form input[name="enable_auto_grading"]').is(':checked') ? 1 : 0);
        
        // Handle file upload if present
        const fileInput = $('#file-upload')[0];
        if (fileInput.files.length > 0) {
            formData.append('attachment', fileInput.files[0]);
        }

        const isEdit = $('#assignment-form').attr('data-mode') === 'edit';
        const assignmentId = $('#assignment-form').attr('data-id');
        console.log(assignmentId);   
        
        alert(isEdit);

        // Set up AJAX request parameters
        const ajaxParams = {
            url: isEdit ? `/api/assignments/${assignmentId}` : '/api/assignments',
            type: isEdit ? 'POST' : 'POST',
            data: formData,
             processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        };

        // Show loading spinner
        Swal.fire({
            title: isEdit ? 'Updating assignment...' : 'Creating assignment...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Submit form
        $.ajax({
            ...ajaxParams,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: isEdit ? 'Assignment updated successfully' : 'Assignment created successfully',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    // Close modal and reset form
                    $('#create-assignment-modal').addClass('hidden');
                    $('#assignment-form')[0].reset();
                    $('#assignment-form').removeAttr('data-mode data-id');
                    $("#modal-title").text("Create Assignment");
                    $('#save-assignment-btn').text("Create Assignment");
                    $('#save-assignment-btn').show();
                    $('#update-assignment-btn').hide();
                    // Scroll to top
                    $('html, body').animate({ scrollTop: 0 }, 'fast');
                    // Refresh assignments list
                    fetchAssignments(getCurrentFilters());
                });
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || 'An error occurred';
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage
                });
            }
        });
    });

    // Reset modal state when closing
    $('#close-modal-btn, #cancel-btn').on('click', function() {
        $('#assignment-form')[0].reset();
        $('#assignment-form').removeAttr('data-mode data-id');
        $('.modal-title').text('Create Assignment');
        $('#submit-btn').text('Create Assignment');
    });

    // Function to fetch and display low-grade students
    function fetchLowGradeStudents(page = 1, limit = 10) {
        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '/api/students/low-grades',
            method: 'GET',
            data: { page, limit },
            success: function(response) {
                Swal.close();
                if (response.success) {
                    renderLowGradeStudents(response.data.students);
                    renderPagination(response.data.pagination);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to fetch students'
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to fetch students. Please try again.'
                });
            }
        });
    }

    function renderPagination(pagination) {
        const paginationContainer = $('#pagination-container');
        paginationContainer.empty();

        if (pagination.last_page <= 1) return;

        const { current_page, last_page } = pagination;
        let html = '<div class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6">';
        
        // Previous button
        html += `
            <div class="flex-1 flex justify-between sm:hidden">
                <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 ${current_page === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                        ${current_page === 1 ? 'disabled' : ''}
                        onclick="fetchLowGradeStudents(${current_page - 1})">
                    Previous
                </button>
                <button class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 ${current_page === last_page ? 'opacity-50 cursor-not-allowed' : ''}"
                        ${current_page === last_page ? 'disabled' : ''}
                        onclick="fetchLowGradeStudents(${current_page + 1})">
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">${pagination.from}</span> to <span class="font-medium">${pagination.to}</span> of <span class="font-medium">${pagination.total}</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        `;

        // Previous page button
        html += `
            <button class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 ${current_page === 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                    ${current_page === 1 ? 'disabled' : ''}
                    onclick="fetchLowGradeStudents(${current_page - 1})">
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
        `;

        // Page numbers
        for (let i = 1; i <= last_page; i++) {
            if (i === 1 || i === last_page || (i >= current_page - 2 && i <= current_page + 2)) {
                html += `
                    <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium ${current_page === i ? 'text-primary bg-primary-50' : 'text-gray-700 hover:bg-gray-50'}"
                            onclick="fetchLowGradeStudents(${i})">
                        ${i}
                    </button>
                `;
            } else if (i === current_page - 3 || i === current_page + 3) {
                html += `
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                        ...
                    </span>
                `;
            }
        }

        // Next page button
        html += `
            <button class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 ${current_page === last_page ? 'opacity-50 cursor-not-allowed' : ''}"
                    ${current_page === last_page ? 'disabled' : ''}
                    onclick="fetchLowGradeStudents(${current_page + 1})">
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
        `;

        html += `
                    </nav>
                </div>
            </div>
        </div>
        `;

        paginationContainer.html(html);
    }

    // Function to render low-grade students in the table
    function renderLowGradeStudents(students) {
        const tbody = document.querySelector('#low-grade-students tbody');
        if (!tbody) {
            console.error('Table body not found. Make sure the table has id="low-grade-students"');
            return;
        }
        
        tbody.innerHTML = '';

        if (students.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                        No students requiring attention found
                    </td>
                </tr>
            `;
            return;
        }

        // Function to generate initials from name
        function getInitials(name) {
            return name
                .split(' ')
                .map(word => word[0])
                .join('')
                .toUpperCase()
                .slice(0, 2);
        }

        // Function to generate a color based on name
        function getColorFromName(name) {
            const colors = [
                'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-red-500',
                'bg-purple-500', 'bg-pink-500', 'bg-indigo-500', 'bg-teal-500'
            ];
            const hash = name.split('').reduce((acc, char) => char.charCodeAt(0) + acc, 0);
            return colors[hash % colors.length];
        }

        students.forEach(student => {
            const row = document.createElement('tr');
            const initials = getInitials(student.name);
            const colorClass = getColorFromName(student.name);

            // Determine status badge and text based on primary issue
            let statusBadge = '';
            let statusText = '';
            let secondaryText = '';
            
            switch(student.primary_issue) {
                case 'missing_assignments':
                    statusBadge = '<span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Missing Assignments</span>';
                    statusText = `${student.missing_assignments_count} missing assignments`;
                    secondaryText = student.last_submission_date 
                        ? `Last submission: ${moment(student.last_submission_date).fromNow()}`
                        : 'No submissions yet';
                    break;
                case 'critical_grades':
                    statusBadge = '<span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Critical Grades</span>';
                    statusText = `Avg. grade: ${student.average_grade}%`;
                    secondaryText = `Last 3 assignments: ${student.last_three_grades}`;
                    break;
                case 'low_grades':
                    statusBadge = '<span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Low Grades</span>';
                    statusText = `Avg. grade: ${student.average_grade}%`;
                    secondaryText = `Last 3 assignments: ${student.last_three_grades}`;
                    break;
                case 'late_submissions':
                    statusBadge = '<span class="px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 rounded-full">Late Submissions</span>';
                    statusText = `${student.late_submissions_count} late submissions`;
                    secondaryText = `Avg. delay: ${student.avg_delay_days} days`;
                    break;
            }

            // Determine action buttons based on issues
            let actionButtons = `
                <button class="text-primary hover:text-indigo-700 !rounded-button" 
                        data-student-id="${student.id}"
                        data-student-name="${student.name}"
                        data-action="message">Message</button>
            `;

            if (student.issues.critical_grades) {
                actionButtons += `
                    <button class="text-gray-500 hover:text-gray-700 !rounded-button"
                            data-student-id="${student.id}"
                            data-student-name="${student.name}"
                            data-action="schedule_meeting">Schedule Meeting</button>
                `;
            } else {
                actionButtons += `
                    <button class="text-gray-500 hover:text-gray-700 !rounded-button"
                            data-student-id="${student.id}"
                            data-student-name="${student.name}"
                            data-action="remind">Remind</button>
                `;
            }

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full ${colorClass} flex items-center justify-center text-white font-semibold">
                                ${initials}
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">${student.name}</div>
                            <div class="text-sm text-gray-500">${student.course}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${statusBadge}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${statusText}</div>
                    <div class="text-xs text-gray-500">${secondaryText}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        ${actionButtons}
                    </div>
                </td>
            `;

            tbody.appendChild(row);
        });

        // Add event listeners for action buttons
        document.querySelectorAll('#low-grade-students button').forEach(button => {
            button.addEventListener('click', function() {
                const studentId = this.dataset.studentId;
                const studentName = this.dataset.studentName;
                const action = this.dataset.action;

                if (action === 'message') {
                    handleMessageStudent(studentId, studentName);
                } else if (action === 'schedule_meeting') {
                    handleScheduleMeeting(studentId, studentName);
                } else if (action === 'remind') {
                    handleRemindStudent(studentId, studentName);
                }
            });
        });
    }

    // Initialize the table when the page loads
    $(document).ready(function() {
        fetchLowGradeStudents();
    });

    // Function to fetch assignment analytics
    function fetchAssignmentAnalytics() {
        // Show loading state
        $('.bg-gray-50 p-4').addClass('animate-pulse');

        $.ajax({
            url: '/api/assignments-analytics',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    renderAssignmentAnalytics(response.data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to fetch analytics'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to fetch analytics. Please try again.'
                });
            },
            complete: function() {
                // Remove loading state
                $('.bg-gray-50 p-4').removeClass('animate-pulse');
            }
        });
    }

    // Function to render assignment analytics
    function renderAssignmentAnalytics(data) {
        // Update summary cards
        $('#total-assignments').text(data.summary.total_assignments);
        $('#completion-rate').text(data.summary.completion_rate + '%');
        $('#average-grade').text(
            data.summary.grade_letter + ' (' + data.summary.average_grade + '%)'
        );

        const submissionChart = echarts.init(document.getElementById('submissionChart'));
            const submissionOption = {
                animation: false,
                tooltip: {
                    trigger: 'axis',
                    backgroundColor: 'rgba(255, 255, 255, 0.8)',
                    textStyle: {
                        color: '#1f2937'
                    }
                },
                legend: {
                    data: ['Submissions', 'On-time Rate', 'Average Grade'],
                    textStyle: {
                        color: '#1f2937'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: data.chart_data.dates,
                    axisLine: {
                        lineStyle: {
                            color: '#d1d5db'
                        }
                    },
                    axisLabel: {
                        color: '#1f2937'
                    }
                },
                yAxis: {
                    type: 'value',
                    axisLine: {
                        lineStyle: {
                            color: '#d1d5db'
                        }
                    },
                    axisLabel: {
                        color: '#1f2937',
                        formatter: '{value}%'
                    },
                    max: 100
                },
                series: [{
                        name: 'Submissions',
                        type: 'line',
                        smooth: true,
                        lineStyle: {
                            width: 3,
                            color: 'rgba(87, 181, 231, 1)'
                        },
                        symbol: 'none',
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                    offset: 0,
                                    color: 'rgba(87, 181, 231, 0.3)'
                                },
                                {
                                    offset: 1,
                                    color: 'rgba(87, 181, 231, 0.1)'
                                }
                            ])
                        },
                        data: [65, 70, 75, 82, 78, 80]
                    },
                    {
                        name: 'On-time Rate',
                        type: 'line',
                        smooth: true,
                        lineStyle: {
                            width: 3,
                            color: 'rgba(141, 211, 199, 1)'
                        },
                        symbol: 'none',
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                    offset: 0,
                                    color: 'rgba(141, 211, 199, 0.3)'
                                },
                                {
                                    offset: 1,
                                    color: 'rgba(141, 211, 199, 0.1)'
                                }
                            ])
                        },
                        data: data.chart_data.on_time_rate
                    },
                    {
                        name: 'Average Grade',
                        type: 'line',
                        smooth: true,
                        lineStyle: {
                            width: 3,
                            color: 'rgba(251, 191, 114, 1)'
                        },
                        symbol: 'none',
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                    offset: 0,
                                    color: 'rgba(251, 191, 114, 0.3)'
                                },
                                {
                                    offset: 1,
                                    color: 'rgba(251, 191, 114, 0.1)'
                                }
                            ])
                        },
                        data: data.chart_data.average_grade
                    }
                ]
            };
            submissionChart.setOption(submissionOption);

            // Handle window resize
            window.addEventListener('resize', function() {
                submissionChart.resize();
            });
    }

    // Initialize analytics when page loads
    $(document).ready(function() {
        fetchAssignmentAnalytics();
    });

    // Function to show assignment details in modal
    function showAssignmentDetails(assignment) {
        // Show the modal
        console.log(assignment)
        const modal = document.getElementById('view-assignment-modal');
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        // Populate the modal with assignment data
        document.getElementById('view-title').textContent = assignment.title;
        document.getElementById('view-course').textContent = assignment.course_name;
        document.getElementById('view-due-date').textContent = formatDate(assignment.due_date);
        document.getElementById('view-points').textContent = `${assignment.total_points} points`;
        document.getElementById('view-status').textContent = assignment.status;
        document.getElementById('view-instructions').textContent = assignment.instructions;
        document.getElementById('view-submissions').textContent = `${assignment.submissions_count}/${assignment.total_students} submitted`;
        document.getElementById('view-on-time-rate').textContent = `${assignment.on_time_rate}%`;
        document.getElementById('view-avg-grade').textContent = `${assignment.avg_grade}%`;

        // Handle attachment if exists
        const attachmentSection = document.getElementById('view-attachment-section');
        if (assignment.attachment) {
            attachmentSection.classList.remove('hidden');
            document.getElementById('view-attachment-name').textContent = assignment.attachment.name;
            document.getElementById('view-attachment-link').href = assignment.attachment.url;
        } else {
            attachmentSection.classList.add('hidden');
        }
    }

    // Function to format date
    function formatDate(dateString) {
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return new Date(dateString).toLocaleDateString('en-US', options);
    }

    // Make the function available globally
    window.viewAssignmentDetails = function(assignmentId) {
        // Show loading spinner
        Swal.fire({
            title: 'Loading assignment details...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fetch assignment data
        fetch(`/api/assignments/${assignmentId}`)
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    showAssignmentDetails(data.data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load assignment details'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load assignment details'
                });
            });
    };

    // Event listener for closing the modal
    document.getElementById('close-view-modal').addEventListener('click', function() {
        const modal = document.getElementById('view-assignment-modal');
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    });

    // Close modal when clicking outside
    document.getElementById('view-assignment-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    });
}); 
