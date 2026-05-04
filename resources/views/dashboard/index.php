<div class="row g-4">
    <div class="col-lg-8">
        <div class="card card-soft shadow-sm mb-4">
            <h2 class="section-title h4">Dashboard</h2>
            <p class="text-muted mb-0">Overview of your reservations and upcoming trips.</p>
        </div>
        <div class="card card-soft shadow-sm mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h5 mb-0">Active reservations</h3>
                <a href="<?= base_url('reservations') ?>" class="btn btn-sm btn-outline-primary">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Trip</th><th>Departure</th><th>Seats</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($activeReservations as $reservation): ?>
                        <tr>
                            <td><?= htmlspecialchars($reservation['trip_name']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($reservation['departure_at'])) ?></td>
                            <td><?= (int) $reservation['seats_reserved'] ?></td>
                            <td><span class="badge text-bg-success"><?= htmlspecialchars($reservation['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$activeReservations): ?><tr><td colspan="4" class="text-muted">No active reservations yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card card-soft shadow-sm">
            <h3 class="h5 mb-3">Upcoming trips</h3>
            <div class="row g-3">
                <?php foreach ($availableTrips as $trip): ?>
                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 h-100">
                            <div class="small text-muted"><?= htmlspecialchars($trip['origin']) ?> to <?= htmlspecialchars($trip['destination']) ?></div>
                            <div class="fw-semibold"><?= htmlspecialchars($trip['name']) ?></div>
                            <div class="small mb-3"><?= date('d/m/Y H:i', strtotime($trip['departure_at'])) ?></div>
                            <a href="<?= base_url('trips/' . $trip['id']) ?>" class="btn btn-sm btn-primary">View trip</a>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (!$availableTrips): ?>
                    <div class="col-12 text-muted">No upcoming trips available.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-soft shadow-sm mb-4">
            <h3 class="h5">Most booked trips</h3>
            <ul class="list-group list-group-flush">
                <?php foreach ($topTrips as $trip): ?>
                    <li class="list-group-item px-0 d-flex justify-content-between"><span><?= htmlspecialchars($trip['name']) ?></span><span class="badge badge-soft"><?= (int) $trip['total_reservations'] ?></span></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php if ($user['role'] === 'admin'): ?>
            <div class="card card-soft shadow-sm">
                <h3 class="h5">Top users</h3>
                <ul class="list-group list-group-flush">
                    <?php foreach ($topUsers as $topUser): ?>
                        <li class="list-group-item px-0 d-flex justify-content-between"><span><?= htmlspecialchars($topUser['full_name']) ?></span><span class="badge badge-soft"><?= (int) $topUser['total_reservations'] ?></span></li>
                    <?php endforeach; ?>
                    <?php if (!$topUsers): ?><li class="list-group-item px-0 text-muted">No data available.</li><?php endif; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="card card-soft shadow-sm">
                <h3 class="h5">Past reservations</h3>
                <ul class="list-group list-group-flush">
                    <?php foreach ($pastReservations as $reservation): ?>
                        <li class="list-group-item px-0"><?= htmlspecialchars($reservation['trip_name']) ?> <span class="text-muted small d-block"><?= date('d/m/Y H:i', strtotime($reservation['departure_at'])) ?></span></li>
                    <?php endforeach; ?>
                    <?php if (!$pastReservations): ?><li class="list-group-item px-0 text-muted">No completed trips yet.</li><?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>
