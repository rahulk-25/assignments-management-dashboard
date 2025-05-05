Student Assignment Management System
===================================

This is a Laravel-based Student Assignment Management System that allows for efficient management of student assignments, courses, and student records.

System Requirements
------------------
- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js and NPM
- Web Server (Apache/Nginx)

Installation Steps
-----------------
1. Clone the repository:
   git clone [repository-url]

2. Install PHP dependencies:
   composer install

3. Install JavaScript dependencies:
   npm install

4. Create a copy of the .env file:
   cp .env.example .env

5. Generate application key:
   php artisan key:generate

6. Configure your database in .env file:
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password

7. Run database migrations:
   php artisan migrate

8. Seed the database with initial data:
   php artisan db:seed

Database Seeding Commands
------------------------
The system comes with several seeders to populate the database with initial data:

1. To seed all data:
   php artisan db:seed

2. To seed specific tables:
   - Courses: php artisan db:seed --class=CourseSeeder
   - Students: php artisan db:seed --class=StudentSeeder
   - Assignments: php artisan db:seed --class=AssignmentSeeder

Running the Application
----------------------
1. Start the Laravel development server:
   php artisan serve

2. In a separate terminal, start the Vite development server:
   npm run dev

3. Access the application at:
   http://localhost:8000

Project Structure
----------------
- app/ - Contains the core code of the application
- config/ - Contains all configuration files
- database/ - Contains database migrations and seeders
- public/ - Contains the entry point for the application
- resources/ - Contains views, raw assets, and language files
- routes/ - Contains all route definitions
- storage/ - Contains compiled Blade templates, file caches, and logs
- tests/ - Contains test files

Features
--------
- Student Management
- Course Management
- Assignment Management
- Student Assignment Tracking
- User Authentication
- Role-based Access Control

Maintenance
-----------
1. Clear application cache:
   php artisan cache:clear

2. Clear configuration cache:
   php artisan config:clear

3. Clear route cache:
   php artisan route:clear

4. Clear view cache:
   php artisan view:clear

Troubleshooting
--------------
If you encounter any issues:

1. Check the Laravel log file:
   tail -f storage/logs/laravel.log

2. Verify database connection:
   php artisan migrate:status

3. Check PHP version:
   php -v

4. Verify Composer installation:
   composer -V

Support
-------
For any issues or questions, please contact the development team.

Note: This is a review assignment for office use. Please ensure all data is properly backed up before making any changes to the production environment. 