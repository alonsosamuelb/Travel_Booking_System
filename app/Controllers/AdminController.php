<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Models\Reservation;
use App\Models\Trip;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\ReservationService;
use App\Services\UploadService;

class AdminController extends Controller
{
    public function index(): void
    {
        $this->view('admin/index', [
            'topTrips' => (new Trip())->mostBooked(),
            'topUsers' => (new User())->topBookers(),
            'history' => (new Reservation())->history(),
            'activities' => (new \App\Models\ActivityLog())->latest(),
        ]);
    }

    public function users(): void
    {
        $page = max(1, (int) Request::input('page', 1));
        $filters = ['search' => Request::input('search'), 'role' => Request::input('role')];
        $editingUser = Request::input('edit') ? (new User())->find((int) Request::input('edit')) : null;

        $this->view('admin/users', [
            'users' => (new User())->paginate($filters, $page),
            'filters' => $filters,
            'editingUser' => $editingUser,
        ]);
    }

    public function saveUser(): void
    {
        $id = Request::input('id') ? (int) Request::input('id') : null;
        $data = Request::all();

        $errors = Validator::validate($data, [
            'full_name' => ['required', 'min:3'],
            'email' => ['required', 'email'],
            'phone' => ['max:30'],
            'role' => ['required', 'in:user,admin'],
        ]);

        if (!$id) {
            $errors = array_merge($errors, Validator::validate($data, ['password' => ['required', 'min:8']]));
        }

        $existing = (new User())->findByEmail(trim((string) ($data['email'] ?? '')));
        if ($existing && (!$id || (int) $existing['id'] !== $id)) {
            $errors['email'] = 'That email is already registered.';
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            flash('error', 'User could not be saved.');
            $this->redirect($id ? 'admin/users?edit=' . $id : 'admin/users');
        }

        (new User())->saveByAdmin($id, [
            'full_name' => trim($data['full_name']),
            'email' => trim($data['email']),
            'phone' => trim($data['phone'] ?? ''),
            'role' => $data['role'],
            'password' => $data['password'] ?? '',
            'deleted_at' => ($data['account_status'] ?? 'active') === 'inactive' ? date('Y-m-d H:i:s') : null,
        ]);

        $savedUser = (new User())->findByEmail(trim($data['email']));
        (new ActivityLogService())->log($id ? 'admin_user_updated' : 'admin_user_created', 'user', (int) ($savedUser['id'] ?? 0), 'Admin saved user record.');
        flash('success', 'User saved.');
        $this->redirect('admin/users');
    }

    public function deleteUser(int $id): void
    {
        (new User())->softDelete($id);
        (new ActivityLogService())->log('admin_user_deactivated', 'user', $id, 'Admin deactivated user.');
        flash('success', 'User deactivated.');
        $this->redirect('admin/users');
    }

    public function trips(): void
    {
        $page = max(1, (int) Request::input('page', 1));
        $filters = [
            'search' => Request::input('search'),
            'origin' => Request::input('origin'),
            'destination' => Request::input('destination'),
        ];
        $editingTrip = Request::input('edit') ? (new Trip())->find((int) Request::input('edit')) : null;

        $this->view('admin/trips', [
            'trips' => (new Trip())->paginate($filters, $page, 8, true),
            'filters' => $filters,
            'editingTrip' => $editingTrip,
        ]);
    }

    public function saveTrip(): void
    {
        $id = Request::input('id') ? (int) Request::input('id') : null;
        $data = Request::all();
        $currentTrip = $id ? (new Trip())->find($id) : null;
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
            $_SESSION['_errors'] = $errors;
            flash('error', 'Trip could not be saved.');
            $this->redirect($id ? 'admin/trips?edit=' . $id : 'admin/trips');
        }

        try {
            $imagePath = (new UploadService())->storeTripImage(
                Request::file('image_file'),
                trim((string) ($data['image_path'] ?? ($currentTrip['image_path'] ?? '')))
            );
        } catch (\RuntimeException $exception) {
            $_SESSION['_errors'] = ['image_file' => $exception->getMessage()];
            flash('error', 'Trip image could not be uploaded.');
            $this->redirect($id ? 'admin/trips?edit=' . $id : 'admin/trips');
        }

        (new Trip())->save($id, [
            'name' => trim($data['name']),
            'description' => trim($data['description']),
            'origin' => trim($data['origin']),
            'destination' => trim($data['destination']),
            'departure_at' => $data['departure_at'],
            'vehicle' => trim($data['vehicle']),
            'available_seats' => (int) $data['available_seats'],
            'image_path' => $imagePath ?: (string) config('app.default_trip_image'),
            'status' => $data['status'],
        ]);

        $savedTrip = $id ? (new Trip())->find($id) : null;
        if (!$savedTrip) {
            $tripList = (new Trip())->paginate(['search' => trim($data['name'])], 1, 1, true);
            $savedTrip = $tripList['data'][0] ?? null;
        }
        (new ActivityLogService())->log($id ? 'admin_trip_updated' : 'admin_trip_created', 'trip', (int) ($savedTrip['id'] ?? 0), 'Admin saved trip record.');
        flash('success', 'Trip saved.');
        $this->redirect('admin/trips');
    }

    public function deleteTrip(int $id): void
    {
        (new Trip())->delete($id);
        (new ActivityLogService())->log('admin_trip_deleted', 'trip', $id, 'Admin deleted trip.');
        flash('success', 'Trip deleted.');
        $this->redirect('admin/trips');
    }

    public function reservations(): void
    {
        $page = max(1, (int) Request::input('page', 1));
        $filters = ['status' => Request::input('status'), 'search' => Request::input('search')];
        $editingReservation = Request::input('edit') ? (new Reservation())->findDetailed((int) Request::input('edit')) : null;

        $this->view('admin/reservations', [
            'reservations' => (new Reservation())->paginate($filters, $page, 12),
            'filters' => $filters,
            'tripOptions' => (new Trip())->allForSelect(),
            'users' => (new User())->paginate([], 1, 100)['data'],
            'editingReservation' => $editingReservation,
        ]);
    }

    public function saveReservation(): void
    {
        $id = Request::input('id') ? (int) Request::input('id') : null;
        $data = Request::all();
        $errors = Validator::validate($data, [
            'user_id' => ['required', 'integer'],
            'trip_id' => ['required', 'integer'],
            'reservation_date' => ['required', 'datetime'],
            'seats_reserved' => ['required', 'integer'],
            'travel_role' => ['required', 'in:passenger,driver'],
            'status' => ['required', 'in:active,cancelled,completed'],
        ]);
        $errors = array_merge($errors, (new ReservationService())->validateReservation($data, (int) $data['user_id'], $id, (string) $data['status']));

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            flash('error', 'Reservation could not be saved.');
            $this->redirect($id ? 'admin/reservations?edit=' . $id : 'admin/reservations');
        }

        $savedReservationId = (new Reservation())->save($id, [
            'user_id' => (int) $data['user_id'],
            'trip_id' => (int) $data['trip_id'],
            'reservation_date' => $data['reservation_date'],
            'seats_reserved' => (int) $data['seats_reserved'],
            'travel_role' => $data['travel_role'],
            'notes' => trim($data['notes'] ?? ''),
            'status' => $data['status'],
        ]);

        (new ActivityLogService())->log($id ? 'admin_reservation_updated' : 'admin_reservation_created', 'reservation', $savedReservationId, 'Admin saved reservation record.');
        flash('success', 'Reservation saved.');
        $this->redirect('admin/reservations');
    }

    public function cancelReservation(int $id): void
    {
        (new Reservation())->cancel($id);
        (new ActivityLogService())->log('admin_reservation_cancelled', 'reservation', $id, 'Admin cancelled reservation.');
        flash('success', 'Reservation cancelled.');
        $this->redirect('admin/reservations');
    }
}
