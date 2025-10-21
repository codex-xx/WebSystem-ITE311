# TODO: Implement Materials Upload/Download Functionality

## Step 1: Create Database Migration for Materials Table
- Create migration file: app/Database/Migrations/CreateMaterialsTable.php
- Define table with id (PK, auto-inc), course_id (FK to courses), file_name (VARCHAR 255), file_path (VARCHAR 255), created_at (DATETIME)
- Run migration: php spark migrate

## Step 2: Create MaterialModel
- Create app/Models/MaterialModel.php
- Add insertMaterial($data) method
- Add getMaterialsByCourse($course_id) method

## Step 3: Create Materials Controller
- Create app/Controllers/Materials.php
- Add upload($course_id) method with role check (admin/teacher only), file upload logic
- Add delete($material_id) method with role check (admin/teacher only)
- Add download($material_id) method with enrollment check for students

## Step 4: Create Upload View
- Create app/Views/materials/upload.php
- Form with enctype multipart/form-data, file input, Bootstrap styling

## Step 5: Update Routes
- Update app/Config/Routes.php
- Add routes: GET/POST /admin/course/(:num)/upload -> Materials::upload/$1
- Add GET /materials/delete/(:num) -> Materials::delete/$1
- Add GET /materials/download/(:num) -> Materials::download/$1

## Step 6: Update Student Dashboard
- Update app/Views/auth/dashboard.php
- Add section to display downloadable materials for enrolled courses

## Step 7: Test Functionality
- Run migration
- Test upload as admin/teacher
- Test download as student
- Test access restrictions
