<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Models\Reservation;
use App\Models\Trip;
use App\Models\User;
use App\Services\ReservationService;

class ApiController extends Controller
{
    public function docs(): never
    {
        $this->json([
            'name' => 'Travel Booking System API',
            'version' => '1.0.0',
            'endpoints' => [
                ['method' => 'GET', 'path' => '/api/trips', 'description' => 'List published trips'],
                ['method' => 'GET', 'path' => '/api/trips/{id}', 'description' => 'Show one trip with availability'],
                ['method' => 'POST', 'path' => '/api/trips', 'description' => 'Create trip (admin session required)'],
                ['method' => 'PUT', 'path' => '/api/trips/{id}', 'description' => 'Update trip (admin session required)'],
                ['method' => 'DELETE', 'path' => '/api/trips/{id}', 'description' => 'Delete trip (admin session required)'],
                ['method' => 'POST', 'path' => '/api/auth/login', 'description' => 'Return API token for valid user credentials'],
                ['method' => 'GET', 'path' => '/api/reservations', 'description' => 'List current authenticated user reservations'],
                ['method' => 'POST', 'path' => '/api/reservations', 'description' => 'Create reservation (authenticated session required)'],
                ['method' => 'PUT', 'path' => '/api/reservations/{id}', 'description' => 'Update own reservation (authenticated session required)'],
                ['method' => 'DELETE', 'path' => '/api/reservations/{id}', 'description' => 'Cancel own reservation (authenticated session required)'],
            ],
        ]);
    }

    public function login(): never
    {
        $data = Request::all();
        $errors = Validator::validate($data, [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ]);

        if ($errors) {
            $this->json(['message' => 'Validation failed', 'errors' => $errors], 422);
        }

        $user = (new User())->findByEmail((string) $data['email']);
        if (!$user || $user['deleted_at'] || !password_verify((string) $data['password'], (string) $user['password'])) {
            $this->json(['message' => 'Invalid credentials'], 401);
        }

        $token = (new User())->refreshApiToken((int) $user['id']);
        $this->json([
            'message' => 'Authenticated',
            'token' => $token,
            'user' => [
                'id' => (int) $user['id'],
                'full_name' => $user['full_name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ]);
    }

    public function trips(): never
    {
        $filters = [
            'search' => Request::input('search'),
            'origin' => Request::input('origin'),
            'destination' => Request::input('destination'),
            'upcoming' => Request::input('upcoming', 1),
        ];
        $page = max(1, (int) Request::input('page', 1));
        $this->json((new Trip())->paginate($filters, $page, 50));
    }

    public function trip(int $id): never
    {
        $trip = (new Trip())->findWithAvailability($id);
        if (!$trip) {
            $this->json(['message' => 'Trip not found'], 404);
        }

        $this->json($trip);
    }

    public function reservations(): never
    {
        $user = Auth::user();
        $filters = [
            'status' => Request::input('status'),
            'search' => Request::input('search'),
        ];
        $page = max(1, (int) Request::input('page', 1));
        $this->json((new Reservation())->paginate($filters, $page, 100, (int) $user['id']));
    }

    public function createTrip(): never
    {
        $data = Request::all();
        $errors = Validator::validate($data, [
            'name' => ['required', 'min:3'],
            'description' => ['required', 'min:10'],
            'origin' => ['required'],
            'destination' => ['required'],
            'departure_at' => ['required', 'datetime'],
            'vehicle' => ['required'],
            'available_seats' => ['required', 'integer'],
            'status' => ['required', 'in:draft,published'],
        ]);

        if ($errors) {
            $this->json(['message' => 'Validation failed', 'errors' => $errors], 422);
        }

        (new Trip())->save(null, [
            'name' => trim($data['name']),
            'description' => trim($data['description']),
            'origin' => trim($data['origin']),
            'destination' => trim($data['destination']),
            'departure_at' => $data['departure_at'],
            'vehicle' => trim($data['vehicle']),
            'available_seats' => (int) $data['available_seats'],
            'image_path' => trim((string) ($data['image_path'] ?? '')),
            'status' => $data['status'],
        ]);

        $this->json(['message' => 'Trip created'], 201);
    }

    public function updateTrip(int $id): never
    {
        $tripModel = new Trip();
        $trip = $tripModel->find($id);
        if (!$trip) {
            $this->json(['message' => 'Trip not found'], 404);
        }

        $data = Request::all();
        $errors = Validator::validate($data, [
            'name' => ['required', 'min:3'],
            'description' => ['required', 'min:10'],
            'origin' => ['required'],
            'destination' => ['required'],
            'departure_at' => ['required', 'datetime'],
            'vehicle' => ['required'],
            'available_seats' => ['required', 'integer'],
            'status' => ['required', 'in:draft,published'],
        ]);

        if ($errors) {
            $this->json(['message' => 'Validation failed', 'errors' => $errors], 422);
        }

        $tripModel->save($id, [
            'name' => trim($data['name']),
            'description' => trim($data['description']),
            'origin' => trim($data['origin']),
            'destination' => trim($data['destination']),
            'departure_at' => $data['departure_at'],
            'vehicle' => trim($data['vehicle']),
            'available_seats' => (int) $data['available_seats'],
            'image_path' => trim((string) ($data['image_path'] ?? $trip['image_path'])),
            'status' => $data['status'],
        ]);

        $this->json(['message' => 'Trip updated']);
    }

    public function deleteTrip(int $id): never
    {
        $tripModel = new Trip();
        if (!$tripModel->find($id)) {
            $this->json(['message' => 'Trip not found'], 404);
        }

        $tripModel->delete($id);
        $this->json(['message' => 'Trip deleted']);
    }

    public function createReservation(): never
    {
        $user = Auth::user();
        $data = Request::all();
        $errors = Validator::validate($data, [
            'trip_id' => ['required', 'integer'],
            'reservation_date' => ['required', 'datetime'],
            'seats_reserved' => ['required', 'integer'],
            'travel_role' => ['required', 'in:passenger,driver'],
            'notes' => ['max:255'],
        ]);
        $errors = array_merge($errors, (new ReservationService())->validateReservation($data, (int) $user['id']));

        if ($errors) {
            $this->json(['message' => 'Validation failed', 'errors' => $errors], 422);
        }

        $id = (new Reservation())->save(null, [
            'user_id' => (int) $user['id'],
            'trip_id' => (int) $data['trip_id'],
            'reservation_date' => $data['reservation_date'],
            'seats_reserved' => (int) $data['seats_reserved'],
            'travel_role' => $data['travel_role'],
            'notes' => trim((string) ($data['notes'] ?? '')),
            'status' => 'active',
        ]);

        $this->json(['message' => 'Reservation created', 'id' => $id], 201);
    }

    public function updateReservation(int $id): never
    {
        $user = Auth::user();
        $reservationModel = new Reservation();
        $reservation = $reservationModel->findDetailed($id);
        if (!$reservation || (int) $reservation['user_id'] !== (int) $user['id']) {
            $this->json(['message' => 'Reservation not found'], 404);
        }

        if (strtotime($reservation['departure_at']) < strtotime('+2 hours')) {
            $this->json(['message' => 'Reservation can only be modified at least 2 hours before departure'], 422);
        }

        $data = Request::all();
        $errors = Validator::validate($data, [
            'trip_id' => ['required', 'integer'],
            'reservation_date' => ['required', 'datetime'],
            'seats_reserved' => ['required', 'integer'],
            'travel_role' => ['required', 'in:passenger,driver'],
            'notes' => ['max:255'],
        ]);
        $errors = array_merge($errors, (new ReservationService())->validateReservation($data, (int) $user['id'], $id));

        if ($errors) {
            $this->json(['message' => 'Validation failed', 'errors' => $errors], 422);
        }

        $reservationModel->save($id, [
            'trip_id' => (int) $data['trip_id'],
            'reservation_date' => $data['reservation_date'],
            'seats_reserved' => (int) $data['seats_reserved'],
            'travel_role' => $data['travel_role'],
            'notes' => trim((string) ($data['notes'] ?? '')),
            'status' => 'active',
        ]);

        $this->json(['message' => 'Reservation updated']);
    }

    public function deleteReservation(int $id): never
    {
        $user = Auth::user();
        $reservationModel = new Reservation();
        $reservation = $reservationModel->findDetailed($id);
        if (!$reservation || (int) $reservation['user_id'] !== (int) $user['id']) {
            $this->json(['message' => 'Reservation not found'], 404);
        }

        if (strtotime($reservation['departure_at']) < strtotime('+2 hours')) {
            $this->json(['message' => 'Cancellation is blocked within 2 hours of departure'], 422);
        }

        $reservationModel->cancel($id);
        $this->json(['message' => 'Reservation cancelled']);
    }
}
