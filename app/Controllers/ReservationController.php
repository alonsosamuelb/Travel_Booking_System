<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Models\Reservation;
use App\Models\Trip;
use App\Services\ActivityLogService;
use App\Services\ExportService;
use App\Services\ReservationService;

class ReservationController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();
        $page = max(1, (int) Request::input('page', 1));
        $filters = [
            'status' => Request::input('status'),
            'search' => Request::input('search'),
        ];

        $reservations = (new Reservation())->paginate($filters, $page, 10, (int) $user['id']);

        $this->view('reservations/index', [
            'reservations' => $reservations,
            'filters' => $filters,
        ]);
    }

    public function create(): void
    {
        $this->view('reservations/form', [
            'reservation' => null,
            'tripOptions' => (new Trip())->allForSelect(),
            'action' => base_url('reservations/create'),
            'title' => 'Create reservation',
        ]);
    }

    public function store(): void
    {
        $user = Auth::user();
        $data = Request::all();
        $_SESSION['_old'] = $data;

        $errors = Validator::validate($data, [
            'trip_id' => ['required', 'integer'],
            'reservation_date' => ['required', 'datetime'],
            'seats_reserved' => ['required', 'integer'],
            'travel_role' => ['required', 'in:passenger,driver'],
            'notes' => ['max:255'],
        ]);

        $errors = array_merge($errors, (new ReservationService())->validateReservation($data, (int) $user['id']));

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect('reservations/create');
        }

        $reservationId = (new Reservation())->save(null, [
            'user_id' => (int) $user['id'],
            'trip_id' => (int) $data['trip_id'],
            'reservation_date' => $data['reservation_date'],
            'seats_reserved' => (int) $data['seats_reserved'],
            'travel_role' => $data['travel_role'],
            'notes' => trim($data['notes'] ?? ''),
            'status' => 'active',
        ]);

        (new ActivityLogService())->log('reservation_created', 'reservation', $reservationId, 'Reservation created by user.');
        flash('success', 'Reservation created successfully.');
        $this->redirect('reservations');
    }

    public function show(int $id): void
    {
        $user = Auth::user();
        $reservation = (new Reservation())->findDetailed($id);

        if (!$reservation || (!Auth::isAdmin() && (int) $reservation['user_id'] !== (int) $user['id'])) {
            http_response_code(403);
            exit('Forbidden');
        }

        $this->view('reservations/show', ['reservation' => $reservation]);
    }

    public function edit(int $id): void
    {
        $user = Auth::user();
        $reservation = (new Reservation())->findDetailed($id);

        if (!$reservation || (int) $reservation['user_id'] !== (int) $user['id'] || $reservation['status'] !== 'active') {
            flash('error', 'Reservation cannot be edited.');
            $this->redirect('reservations');
        }

        $this->view('reservations/form', [
            'reservation' => $reservation,
            'tripOptions' => (new Trip())->allForSelect(),
            'action' => base_url("reservations/{$id}/edit"),
            'title' => 'Edit reservation',
        ]);
    }

    public function update(int $id): void
    {
        $user = Auth::user();
        $reservation = (new Reservation())->findDetailed($id);

        if (!$reservation || (int) $reservation['user_id'] !== (int) $user['id']) {
            http_response_code(403);
            exit('Forbidden');
        }

        if (strtotime($reservation['departure_at']) < strtotime('+2 hours')) {
            flash('error', 'Reservations can only be modified at least 2 hours before departure.');
            $this->redirect('reservations');
        }

        $data = Request::all();
        $_SESSION['_old'] = $data;
        $errors = Validator::validate($data, [
            'trip_id' => ['required', 'integer'],
            'reservation_date' => ['required', 'datetime'],
            'seats_reserved' => ['required', 'integer'],
            'travel_role' => ['required', 'in:passenger,driver'],
            'notes' => ['max:255'],
        ]);
        $errors = array_merge($errors, (new ReservationService())->validateReservation($data, (int) $user['id'], $id));

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect("reservations/{$id}/edit");
        }

        (new Reservation())->save($id, [
            'trip_id' => (int) $data['trip_id'],
            'reservation_date' => $data['reservation_date'],
            'seats_reserved' => (int) $data['seats_reserved'],
            'travel_role' => $data['travel_role'],
            'notes' => trim($data['notes'] ?? ''),
            'status' => 'active',
        ]);

        (new ActivityLogService())->log('reservation_updated', 'reservation', $id, 'Reservation updated by user.');
        flash('success', 'Reservation updated.');
        $this->redirect('reservations');
    }

    public function cancel(int $id): void
    {
        $user = Auth::user();
        $reservation = (new Reservation())->findDetailed($id);

        if (!$reservation || (int) $reservation['user_id'] !== (int) $user['id']) {
            http_response_code(403);
            exit('Forbidden');
        }

        if (strtotime($reservation['departure_at']) < strtotime('+2 hours')) {
            flash('error', 'Cancellation is blocked within 2 hours of departure.');
            $this->redirect('reservations');
        }

        (new Reservation())->cancel($id);
        (new ActivityLogService())->log('reservation_cancelled', 'reservation', $id, 'Reservation cancelled by user.');
        flash('success', 'Reservation cancelled.');
        $this->redirect('reservations');
    }

    public function exportCsv(): never
    {
        $user = Auth::user();
        $rows = (new Reservation())->paginate([], 1, 200, (int) $user['id'])['data'];
        $csvRows = array_map(fn (array $row) => [
            $row['id'],
            $row['trip_name'],
            $row['origin'],
            $row['destination'],
            $row['departure_at'],
            $row['seats_reserved'],
            $row['travel_role'],
            $row['status'],
        ], $rows);

        (new ExportService())->exportCsv('my-reservations.csv', ['ID', 'Trip', 'Origin', 'Destination', 'Departure', 'Seats', 'Role', 'Status'], $csvRows);
    }

    public function exportPdf(): never
    {
        $user = Auth::user();
        $rows = (new Reservation())->paginate([], 1, 50, (int) $user['id'])['data'];
        $lines = array_map(fn (array $row) => sprintf(
            '#%d %s | %s to %s | %s | %s seat(s) | %s',
            $row['id'],
            $row['trip_name'],
            $row['origin'],
            $row['destination'],
            date('d/m/Y H:i', strtotime($row['departure_at'])),
            $row['seats_reserved'],
            strtoupper($row['status'])
        ), $rows);

        (new ExportService())->exportSimplePdf('Reservation Report - ' . $user['full_name'], $lines);
    }
}
