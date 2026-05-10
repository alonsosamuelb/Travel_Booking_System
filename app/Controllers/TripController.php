<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Core\View;
use App\Models\Trip;
use App\Services\ActivityLogService;
use App\Services\UploadService;

class TripController extends Controller
{
    public function index(): void
    {
        $page = max(1, (int) Request::input('page', 1));
        $filters = [
            'search' => Request::input('search'),
            'origin' => Request::input('origin'),
            'destination' => Request::input('destination'),
            'upcoming' => 1,
        ];
        $trips = (new Trip())->paginate($filters, $page, 6);

        $this->view('trips/index', [
            'trips' => $trips,
            'filters' => $filters,
        ]);
    }

    public function show(int $id): void
    {
        $trip = (new Trip())->findWithAvailability($id);

        if (!$trip) {
            http_response_code(404);
            $this->view('errors/404', [], 'layouts/minimal');
            return;
        }

        $this->view('trips/show', ['trip' => $trip]);
    }

    public function manage(): void
    {
        $user = Auth::user();
        $page = max(1, (int) Request::input('page', 1));
        $filters = [
            'search' => Request::input('search'),
            'status' => Request::input('status'),
        ];
        $tripModel = new Trip();
        if (!$tripModel->supportsCreatorTrips()) {
            flash('error', 'The database needs the latest migration before driver trip publishing can be used.');
            $this->redirect('dashboard');
        }

        $editingTrip = Request::input('edit') ? $tripModel->find((int) Request::input('edit')) : null;

        if ($editingTrip && (int) ($editingTrip['creator_user_id'] ?? 0) !== (int) $user['id']) {
            flash('error', 'You can only edit trips you created.');
            $this->redirect('my-trips');
        }

        $this->view('trips/manage', [
            'userTrips' => $tripModel->paginateByOwner((int) $user['id'], $filters, $page, 8),
            'filters' => $filters,
            'editingTrip' => $editingTrip,
        ]);
    }

    public function saveOwnTrip(): void
    {
        $user = Auth::user();
        $id = Request::input('id') ? (int) Request::input('id') : null;
        $data = Request::all();
        $tripModel = new Trip();
        if (!$tripModel->supportsCreatorTrips()) {
            flash('error', 'The database needs the latest migration before driver trip publishing can be used.');
            $this->redirect('dashboard');
        }

        $currentTrip = $id ? $tripModel->find($id) : null;

        if ($currentTrip && (int) ($currentTrip['creator_user_id'] ?? 0) !== (int) $user['id']) {
            http_response_code(403);
            View::render('errors/403', [], 'layouts/minimal');
            exit;
        }

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
            $this->redirect($id ? 'my-trips?edit=' . $id : 'my-trips');
        }

        try {
            $imagePath = (new UploadService())->storeTripImage(
                Request::file('image_file'),
                trim((string) ($data['image_path'] ?? ($currentTrip['image_path'] ?? '')))
            );
        } catch (\RuntimeException $exception) {
            $_SESSION['_errors'] = ['image_file' => $exception->getMessage()];
            flash('error', 'Trip image could not be uploaded.');
            $this->redirect($id ? 'my-trips?edit=' . $id : 'my-trips');
        }

        $tripModel->save($id, [
            'name' => trim($data['name']),
            'description' => trim($data['description']),
            'origin' => trim($data['origin']),
            'destination' => trim($data['destination']),
            'departure_at' => $data['departure_at'],
            'vehicle' => trim($data['vehicle']),
            'available_seats' => (int) $data['available_seats'],
            'image_path' => $imagePath ?: (string) config('app.default_trip_image'),
            'status' => $data['status'],
            'creator_user_id' => (int) $user['id'],
        ]);

        $savedTrip = $id ? $tripModel->find($id) : null;
        if (!$savedTrip) {
            $list = $tripModel->paginateByOwner((int) $user['id'], ['search' => trim($data['name'])], 1, 1);
            $savedTrip = $list['data'][0] ?? null;
        }

        (new ActivityLogService())->log($id ? 'user_trip_updated' : 'user_trip_created', 'trip', (int) ($savedTrip['id'] ?? 0), 'User published trip as driver.');
        flash('success', $id ? 'Trip updated.' : 'Trip published successfully.');
        $this->redirect('my-trips');
    }

    public function deleteOwnTrip(int $id): void
    {
        $user = Auth::user();
        $trip = (new Trip())->find($id);

        if (!$trip || (int) ($trip['creator_user_id'] ?? 0) !== (int) $user['id']) {
            http_response_code(403);
            View::render('errors/403', [], 'layouts/minimal');
            exit;
        }

        $tripModel = new Trip();
        if ($tripModel->hasReservations($id)) {
            flash('error', 'This trip cannot be deleted because it already has linked reservations.');
            $this->redirect('my-trips');
        }

        $tripModel->delete($id);
        (new ActivityLogService())->log('user_trip_deleted', 'trip', $id, 'User deleted own driver trip.');
        flash('success', 'Trip deleted.');
        $this->redirect('my-trips');
    }
}
