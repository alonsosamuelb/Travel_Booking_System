<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-soft shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="section-title h3 mb-0">Reservation receipt #<?= (int) $reservation['id'] ?></h2>
                <span class="badge <?= $reservation['status'] === 'active' ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= htmlspecialchars($reservation['status']) ?></span>
            </div>
            <div class="row g-3">
                <div class="col-md-6"><strong>User</strong><div><?= htmlspecialchars($reservation['full_name']) ?> (<?= htmlspecialchars($reservation['email']) ?>)</div></div>
                <div class="col-md-6"><strong>Trip</strong><div><?= htmlspecialchars($reservation['trip_name']) ?></div></div>
                <div class="col-md-6"><strong>Route</strong><div><?= htmlspecialchars($reservation['origin']) ?> to <?= htmlspecialchars($reservation['destination']) ?></div></div>
                <div class="col-md-6"><strong>Departure</strong><div><?= date('d/m/Y H:i', strtotime($reservation['departure_at'])) ?></div></div>
                <div class="col-md-6"><strong>Vehicle</strong><div><?= htmlspecialchars($reservation['vehicle']) ?></div></div>
                <div class="col-md-6"><strong>Seats</strong><div><?= (int) $reservation['seats_reserved'] ?></div></div>
                <div class="col-12"><strong>Notes</strong><div><?= nl2br(htmlspecialchars($reservation['notes'] ?: 'No notes')) ?></div></div>
            </div>
        </div>
    </div>
</div>
