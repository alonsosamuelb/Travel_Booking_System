<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Trip;

class ReservationService
{
    public function validateReservation(array $data, int $userId, ?int $reservationId = null, string $status = 'active'): array
    {
        $errors = [];
        $tripModel = new Trip();
        $reservationModel = new Reservation();
        $trip = $tripModel->findWithAvailability((int) $data['trip_id']);
        $reservationDate = isset($data['reservation_date']) ? strtotime((string) $data['reservation_date']) : false;

        if (!$trip) {
            return ['trip_id' => 'Selected trip was not found.'];
        }

        if ($reservationDate !== false && $reservationDate < time()) {
            $errors['reservation_date'] = 'Reservation date cannot be earlier than the current date and time.';
        }

        if (strtotime($trip['departure_at']) <= time()) {
            $errors['trip_id'] = 'You can only reserve upcoming trips.';
        }

        if ((int) ($trip['creator_user_id'] ?? 0) === $userId) {
            $errors['trip_id'] = 'You cannot reserve a trip you published as a driver.';
        }

        $requestedSeats = (int) $data['seats_reserved'];
        if ($requestedSeats < 1) {
            $errors['seats_reserved'] = 'At least one seat is required.';
        }

        if ($status !== 'active') {
            return $errors;
        }

        $alreadyReserved = $reservationModel->activeSeatsForTrip((int) $trip['id']);
        if ($reservationId) {
            $existing = $reservationModel->findDetailed($reservationId);
            if ($existing && $existing['status'] === 'active') {
                $alreadyReserved -= (int) $existing['seats_reserved'];
            }
        }

        if (($alreadyReserved + $requestedSeats) > (int) $trip['available_seats']) {
            $errors['seats_reserved'] = 'This reservation exceeds the available seats.';
        }

        if ($reservationModel->hasDuplicate($userId, (int) $trip['id'], $reservationId)) {
            $errors['trip_id'] = 'You already have an active reservation for this trip.';
        }

        if ($reservationModel->hasUserTripConflict($userId, $trip['departure_at'], $reservationId)) {
            $errors['trip_id'] = 'You already have another reservation at the same departure time.';
        }

        if (!$reservationId && $reservationModel->userActiveReservationCount($userId) >= (int) config('app.reservation_limit_per_user', 3)) {
            $errors['trip_id'] = 'You reached the active reservation limit.';
        }

        return $errors;
    }
}
