# Project & Task Management API

A RESTful API built with Laravel for managing projects and tasks with comprehensive analytics features.

## Features

-   ✅ Complete CRUD operations for Projects and Tasks
-   📊 Advanced analytics and reporting
-   🔍 Project progress tracking
-   📈 Task completion statistics
-   ⚠️ Problematic project detection
-   ✅ Comprehensive test coverage

## Requirements

-   PHP 8.2+
-   Composer
-   MySQL/MariaDB or SQLite
-   Laravel 11.x

## Installation

1. **Clone the repository**

```bash
git clone <repository-url>
cd technical-test
```

2. **Install dependencies**

```bash
composer install
```

3. **Configure environment**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=technical_test
DB_USERNAME=root
DB_PASSWORD=
```

5. **Run migrations**

```bash
php artisan migrate
```

6. **Start development server**

```bash
php artisan serve
```

API will be available at `http://localhost:8000`

## API Endpoints

### Projects

#### CRUD Operations

-   `GET /api/projects` - Get all projects
-   `POST /api/projects` - Create new project
-   `GET /api/projects/{id}` - Get project detail
-   `PUT/PATCH /api/projects/{id}` - Update project
-   `DELETE /api/projects/{id}` - Delete project

**Create Project Request Body:**

```json
{
    "name": "Project Name",
    "start_date": "2025-12-01",
    "end_date": "2025-12-31",
    "status": 1
}
```

**Status Values:**

-   `1` = Planning
-   `2` = In Progress
-   `3` = Completed

#### Analytics Endpoints

-   `GET /api/projects/count-tasks` - Count tasks per project
-   `GET /api/projects/recap-status` - Recap tasks by status per project
-   `GET /api/projects/{id}/progress` - Get project progress percentage
-   `GET /api/projects/problematic` - Get problematic projects (overdue + progress < 50%)

### Tasks

#### CRUD Operations

-   `GET /api/tasks` - Get all tasks
-   `POST /api/tasks` - Create new task
-   `GET /api/tasks/{id}` - Get task detail
-   `PUT/PATCH /api/tasks/{id}` - Update task
-   `DELETE /api/tasks/{id}` - Delete task

**Create Task Request Body:**

```json
{
    "project_id": 1,
    "title": "Task Title",
    "description": "Task description",
    "priority": 3,
    "deadline": "2025-12-15",
    "status": 2
}
```

**Priority Values:**

-   `1` = Low
-   `2` = Medium
-   `3` = High

**Status Values:**

-   `1` = To Do
-   `2` = In Progress
-   `3` = Review
-   `4` = Done

#### Analytics Endpoints

-   `GET /api/tasks/completed-per-month/{year?}` - Get completed tasks statistics per month

## Testing

Run all tests:

```bash
php artisan test
```

Run specific test suite:

```bash
php artisan test --filter=ProjectApiTest
php artisan test --filter=TaskApiTest
```

**Test Coverage:**

-   ✅ 19 tests with 49 assertions
-   ✅ Complete CRUD operations testing
-   ✅ Validation testing
-   ✅ Analytics endpoints testing

## Database Schema

### Projects Table

```
id: bigint (primary key)
name: string
start_date: date
end_date: date
status: integer (1-3)
created_at: timestamp
updated_at: timestamp
```

### Tasks Table

```
id: bigint (primary key)
project_id: bigint (foreign key)
title: string
description: text
priority: integer (1-3)
deadline: date
status: integer (1-4)
created_at: timestamp
updated_at: timestamp
```

## Example Usage with cURL

**Create a project:**

```bash
curl -X POST http://localhost:8000/api/projects \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "New Project",
    "start_date": "2025-12-01",
    "end_date": "2025-12-31",
    "status": 1
  }'
```

**Create a task:**

```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "project_id": 1,
    "title": "Bug Fixing",
    "description": "Resolve critical errors",
    "priority": 3,
    "deadline": "2025-12-15",
    "status": 2
  }'
```

**Get project progress:**

```bash
curl -X GET http://localhost:8000/api/projects/1/progress \
  -H "Accept: application/json"
```

## Example Usage with Postman

1. Import the collection or create requests manually
2. Set base URL: `http://localhost:8000`
3. Add header: `Accept: application/json`
4. Add header: `Content-Type: application/json` (for POST/PUT requests)

## Clearing Cache

If you encounter routing issues:

```bash
php artisan route:clear
php artisan cache:clear
php artisan config:clear
composer dump-autoload
```

## View Routes

List all available routes:

```bash
php artisan route:list
php artisan route:list --path=api
```

## Development Tips

-   Use `php artisan tinker` to interact with models
-   Run `php artisan migrate:fresh --seed` to reset database with sample data
-   Check logs at `storage/logs/laravel.log` for debugging

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
