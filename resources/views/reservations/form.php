<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-soft shadow-sm">
            <h2 class="section-title h3 mb-4"><?= htmlspecialchars($title) ?></h2>
            <form method="POST" action="<?= htmlspecialchars($action) ?>" data-validate="true">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Trip</label>
                        <select class="form-select" name="trip_id" data-required="true">
                            <option value="">Select a trip</option>
                            <?php foreach ($tripOptions as $trip): ?>
                                <?php $selected = (string) old('trip_id', $reservation['trip_id'] ?? '') === (string) $trip['id']; ?>
                                <option value="<?= (int) $trip['id'] ?>" <?= $selected ? 'selected' : '' ?>><?= htmlspecialchars($trip['name']) ?> | <?= date('d/m/Y H:i', strtotime($trip['departure_at'])) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Reservation date</label>
                        <input type="datetime-local" class="form-control" name="reservation_date" value="<?= htmlspecialchars(old('reservation_date', isset($reservation['reservation_date']) ? date('Y-m-d\TH:i', strtotime($reservation['reservation_date'])) : date('Y-m-d\TH:i'))) ?>" data-required="true">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Seats reserved</label>
                        <input type="number" class="form-control" min="1" name="seats_reserved" value="<?= htmlspecialchars(old('seats_reserved', $reservation['seats_reserved'] ?? 1)) ?>" data-required="true">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Passenger / Driver</label>
                        <select class="form-select" name="travel_role" data-required="true">
                            <option value="passenger" <?= old('travel_role', $reservation['travel_role'] ?? '') === 'passenger' ? 'selected' : '' ?>>Passenger</option>
                            <option value="driver" <?= old('travel_role', $reservation['travel_role'] ?? '') === 'driver' ? 'selected' : '' ?>>Driver</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="4"><?= htmlspecialchars(old('notes', $reservation['notes'] ?? '')) ?></textarea>
                    </div>
                </div>
                <button class="btn btn-primary mt-4"><?= $reservation ? 'Update' : 'Create' ?> reservation</button>
            </form>
        </div>
    </div>
</div>
