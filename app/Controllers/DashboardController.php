<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Reservation;
use App\Models\Trip;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();
        $reservationModel = new Reservation();

        $this->view('dashboard/index', [
            'user' => $user,
            'activeReservations' => $reservationModel->paginate(['status' => 'active'], 1, 5, (int) $user['id'])['data'],
            'pastReservations' => $reservationModel->paginate(['status' => 'finished'], 1, 5, (int) $user['id'])['data'],
            'availableTrips' => (new Trip())->paginate(['upcoming' => 1], 1, 3)['data'],
            'topTrips' => (new Trip())->mostBooked(),
            'topUsers' => (new User())->topBookers(),
        ]);
    }
}
