<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');

// Authentication Routes
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::register');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('profile', 'Auth::profile');
$routes->post('profile', 'Auth::profile');
$routes->get('dashboard', 'Auth::dashboard');
$routes->get('user', 'Auth::users');
$routes->get('user/(:num)', 'Auth::getUser/$1');
$routes->post('user/update', 'Auth::updateUser');
$routes->post('user/deactivate/(:num)', 'Auth::deactivateUser/$1');
$routes->post('user/activate/(:num)', 'Auth::activateUser/$1');
$routes->post('user/add', 'Auth::addUser');
$routes->get('/teacher/students', 'Auth::teacherStudents');



$routes->post('/course/enroll', 'Course::enroll');

// Materials routes
$routes->get('/student/materials', 'Materials::index');
$routes->get('/student/grades', 'Materials::grades');
$routes->match(['get', 'post'], '/student/materials/submit/(:num)', 'Materials::submit/$1');
$routes->get('/materials/manage', 'Materials::manage');
$routes->match(['get', 'post'], '/course/(:num)/upload', 'Materials::upload/$1');
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');
$routes->post('/assignments/grade/(:num)', 'Materials::gradeAssignment/$1');

// Assignments download route
$routes->get('/assignments/download/(:num)', 'Materials::downloadAssignment/$1');

// Notifications routes
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');
$routes->get('courses', 'Course::index');
$routes->get('manage_course', 'Course::manage');
$routes->get('course/manage', 'Materials::courseSelection');
$routes->get('course/search', 'Course::search');
$routes->post('course/search', 'Course::search');

// Enrollment routes
$routes->get('enrollment/student', 'Enrollment::studentIndex');
$routes->get('enrollment/teacher', 'Enrollment::teacherIndex');
$routes->post('enrollment/approve', 'Enrollment::approve');
$routes->post('enrollment/deny', 'Enrollment::deny');
$routes->get('enrollment/force', 'Enrollment::forceEnrollForm');
$routes->post('enrollment/force', 'Enrollment::forceEnroll');

// Course routes
$routes->post('course/updateSchedule', 'Course::updateSchedule');
$routes->post('course/updateCourse', 'Course::updateCourse');
$routes->post('course/createCourse', 'Course::createCourse');
$routes->get('course/materials/(:num)', 'Materials::courseMaterials/$1');
