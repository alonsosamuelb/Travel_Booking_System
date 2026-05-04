<?php

use App\Controllers\AdminController;
use App\Controllers\ApiController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProfileController;
use App\Controllers\ReservationController;
use App\Controllers\SetupController;
use App\Controllers\TripController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\ThrottleMiddleware;

$guest = [GuestMiddleware::class => []];
$auth = [AuthMiddleware::class => []];
$admin = [AuthMiddleware::class => [], RoleMiddleware::class => ['admin']];
$loginThrottle = [GuestMiddleware::class => [], ThrottleMiddleware::class => ['login', '5', '300']];
$registerThrottle = [GuestMiddleware::class => [], ThrottleMiddleware::class => ['register', '5', '600']];
$recoveryThrottle = [GuestMiddleware::class => [], ThrottleMiddleware::class => ['recovery', '5', '600']];

$router->get('/setup', [SetupController::class, 'show']);
$router->post('/setup', [SetupController::class, 'install']);

$router->get('/', [TripController::class, 'index']);
$router->get('/login', [AuthController::class, 'loginForm'], $guest);
$router->post('/login', [AuthController::class, 'login'], $loginThrottle);
$router->get('/register', [AuthController::class, 'registerForm'], $guest);
$router->post('/register', [AuthController::class, 'register'], $registerThrottle);
$router->post('/logout', [AuthController::class, 'logout'], $auth);
$router->get('/forgot-password', [AuthController::class, 'forgotPasswordForm'], $guest);
$router->post('/forgot-password', [AuthController::class, 'sendResetLink'], $recoveryThrottle);
$router->get('/reset-password', [AuthController::class, 'resetPasswordForm'], $guest);
$router->post('/reset-password', [AuthController::class, 'resetPassword'], $recoveryThrottle);
$router->get('/reactivate-account', [AuthController::class, 'reactivateForm'], $guest);
$router->post('/reactivate-account', [AuthController::class, 'reactivate'], $recoveryThrottle);

$router->get('/dashboard', [DashboardController::class, 'index'], $auth);
$router->get('/profile', [ProfileController::class, 'show'], $auth);
$router->post('/profile', [ProfileController::class, 'update'], $auth);
$router->post('/profile/password', [ProfileController::class, 'updatePassword'], $auth);
$router->post('/profile/delete', [ProfileController::class, 'deleteAccount'], $auth);
$router->post('/profile/api-token', [ProfileController::class, 'generateApiToken'], $auth);
$router->post('/profile/api-token/revoke', [ProfileController::class, 'revokeApiToken'], $auth);

$router->get('/trips', [TripController::class, 'index']);
$router->get('/trips/{id}', [TripController::class, 'show']);

$router->get('/reservations', [ReservationController::class, 'index'], $auth);
$router->get('/reservations/create', [ReservationController::class, 'create'], $auth);
$router->post('/reservations/create', [ReservationController::class, 'store'], $auth);
$router->get('/reservations/{id}', [ReservationController::class, 'show'], $auth);
$router->get('/reservations/{id}/edit', [ReservationController::class, 'edit'], $auth);
$router->post('/reservations/{id}/edit', [ReservationController::class, 'update'], $auth);
$router->post('/reservations/{id}/cancel', [ReservationController::class, 'cancel'], $auth);
$router->get('/exports/reservations.csv', [ReservationController::class, 'exportCsv'], $auth);
$router->get('/exports/reservations.pdf', [ReservationController::class, 'exportPdf'], $auth);

$router->get('/admin', [AdminController::class, 'index'], $admin);
$router->get('/admin/users', [AdminController::class, 'users'], $admin);
$router->post('/admin/users/save', [AdminController::class, 'saveUser'], $admin);
$router->post('/admin/users/{id}/delete', [AdminController::class, 'deleteUser'], $admin);
$router->get('/admin/trips', [AdminController::class, 'trips'], $admin);
$router->post('/admin/trips/save', [AdminController::class, 'saveTrip'], $admin);
$router->post('/admin/trips/{id}/delete', [AdminController::class, 'deleteTrip'], $admin);
$router->get('/admin/reservations', [AdminController::class, 'reservations'], $admin);
$router->post('/admin/reservations/save', [AdminController::class, 'saveReservation'], $admin);
$router->post('/admin/reservations/{id}/cancel', [AdminController::class, 'cancelReservation'], $admin);

$router->get('/api/docs', [ApiController::class, 'docs']);
$router->post('/api/auth/login', [ApiController::class, 'login'], [ThrottleMiddleware::class => ['api-login', '10', '300']]);
$router->get('/api/trips', [ApiController::class, 'trips']);
$router->get('/api/trips/{id}', [ApiController::class, 'trip']);
$router->post('/api/trips', [ApiController::class, 'createTrip'], $admin);
$router->put('/api/trips/{id}', [ApiController::class, 'updateTrip'], $admin);
$router->delete('/api/trips/{id}', [ApiController::class, 'deleteTrip'], $admin);
$router->get('/api/reservations', [ApiController::class, 'reservations'], $auth);
$router->post('/api/reservations', [ApiController::class, 'createReservation'], $auth);
$router->put('/api/reservations/{id}', [ApiController::class, 'updateReservation'], $auth);
$router->delete('/api/reservations/{id}', [ApiController::class, 'deleteReservation'], $auth);
