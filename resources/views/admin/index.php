<div class="row g-4">
    <div class="col-lg-4">
        <div class="card card-soft shadow-sm h-100">
            <h2 class="section-title h4">Administration</h2>
            <p class="text-muted">Manage users, trips and reservations.</p>
            <div class="d-grid gap-2">
                <a class="btn btn-primary" href="<?= base_url('admin/users') ?>">Manage users</a>
                <a class="btn btn-outline-primary" href="<?= base_url('admin/trips') ?>">Manage trips</a>
                <a class="btn btn-outline-primary" href="<?= base_url('admin/reservations') ?>">Manage reservations</a>
                <a class="btn btn-outline-secondary" href="<?= base_url('api/docs') ?>">API docs</a>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-soft shadow-sm h-100">
            <h3 class="h5">Most booked trips</h3>
            <ul class="list-group list-group-flush">
                <?php foreach ($topTrips as $trip): ?>
                    <li class="list-group-item px-0 d-flex justify-content-between"><span><?= htmlspecialchars($trip['name']) ?></span><span class="badge badge-soft"><?= (int) $trip['total_reservations'] ?></span></li>
                <?php endforeach; ?>
                <?php if (!$topTrips): ?><li class="list-group-item px-0 text-muted">No data available.</li><?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-soft shadow-sm h-100">
            <h3 class="h5">Users with more reservations</h3>
            <ul class="list-group list-group-flush">
                <?php foreach ($topUsers as $user): ?>
                    <li class="list-group-item px-0 d-flex justify-content-between"><span><?= htmlspecialchars($user['full_name']) ?></span><span class="badge badge-soft"><?= (int) $user['total_reservations'] ?></span></li>
                <?php endforeach; ?>
                <?php if (!$topUsers): ?><li class="list-group-item px-0 text-muted">No data available.</li><?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="col-12">
        <div class="card card-soft shadow-sm">
            <h3 class="h5">Reservation history</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Date</th><th>Total reservations</th></tr></thead>
                    <tbody>
                    <?php foreach ($history as $item): ?>
                        <tr><td><?= htmlspecialchars($item['reservation_day']) ?></td><td><?= (int) $item['total'] ?></td></tr>
                    <?php endforeach; ?>
                    <?php if (!$history): ?><tr><td colspan="2" class="text-muted">No reservation history available.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card card-soft shadow-sm">
            <h3 class="h5">Recent activity</h3>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>When</th><th>User</th><th>Action</th><th>Description</th><th>IP</th></tr></thead>
                    <tbody>
                    <?php foreach ($activities as $activity): ?>
                        <tr>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($activity['created_at']))) ?></td>
                            <td><?= htmlspecialchars($activity['full_name'] ?: 'Guest/System') ?></td>
                            <td><span class="badge badge-soft"><?= htmlspecialchars($activity['action']) ?></span></td>
                            <td><?= htmlspecialchars($activity['description']) ?></td>
                            <td><?= htmlspecialchars($activity['ip_address']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$activities): ?><tr><td colspan="5" class="text-muted">No activity logged yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
