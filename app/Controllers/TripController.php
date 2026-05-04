<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Trip;

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
}
