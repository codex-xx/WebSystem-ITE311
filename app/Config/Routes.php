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
$routes->post('/logout', 'Auth::logout');
$routes->get('dashboard', 'Auth::dashboard');  

$routes->post('/course/enroll', 'Course::enroll');  
$routes->get('announcements', 'Announcement::index');  // Unprotected route, accessible to students

// Apply RoleAuth filter to /admin/* routes
$routes->group('admin', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');  
    // Add more /admin routes here if needed
});

// Apply RoleAuth filter to /teacher/* routes
$routes->group('teacher', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Teacher::dashboard'); 
    // Add more /teacher routes here if needed
});
