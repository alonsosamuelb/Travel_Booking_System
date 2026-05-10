<?php

use App\Core\Auth;

$pages = max(1, (int) ceil($reservations['total'] / $reservations['per_page']));
$isAdmin = Auth::isAdmin();
?>
<div class="card card-soft shadow-sm">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h2 class="section-title h3 mb-1">My bookings</h2>
            <p class="text-muted mb-0">View and manage your bookings.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('exports/reservations.csv') ?>" class="btn btn-outline-secondary">Export CSV</a>
            <a href="<?= base_url('exports/reservations.pdf') ?>" class="btn btn-outline-secondary">Export PDF</a>
            <a href="<?= base_url('reservations/create') ?>" class="btn btn-primary">Book a trip</a>
        </div>
    </div>
    <?php if ($isAdmin): ?>
        <div class="alert alert-info">
            This section shows only reservations linked to your current admin account. To manage all system reservations, go to
            <a href="<?= base_url('admin/reservations') ?>" class="alert-link">Admin &gt; Manage reservations</a>.
        </div>
    <?php endif; ?>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-5"><input type="text" class="form-control" name="search" placeholder="Search your bookings" value="<?= htmlspecialchars($filters['search'] ?? '') ?>"></div>
        <div class="col-md-4">
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                <option value="finished" <?= ($filters['status'] ?? '') === 'finished' ? 'selected' : '' ?>>Finished</option>
            </select>
        </div>
        <div class="col-md-3"><button class="btn btn-outline-primary w-100">Search</button></div>
    </form>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>ID</th><th>Trip</th><th>Departure</th><th>Seats</th><th>Role</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($reservations['data'] as $reservation): ?>
                <tr>
                    <td>#<?= (int) $reservation['id'] ?></td>
                    <td><?= htmlspecialchars($reservation['trip_name']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($reservation['departure_at'])) ?></td>
                    <td><?= (int) $reservation['seats_reserved'] ?></td>
                    <td><?= htmlspecialchars($reservation['travel_role']) ?></td>
                    <td>
                        <span class="badge <?= $reservation['status'] === 'active' ? 'text-bg-success' : ($reservation['status'] === 'completed' ? 'text-bg-dark' : 'text-bg-secondary') ?>">
                            <?= htmlspecialchars($reservation['status'] === 'completed' ? 'finished' : $reservation['status']) ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="<?= base_url('reservations/' . $reservation['id']) ?>" class="btn btn-sm btn-outline-primary">View</a>
                        <?php if ($reservation['status'] === 'active'): ?>
                            <a href="<?= base_url('reservations/' . $reservation['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form action="<?= base_url('reservations/' . $reservation['id'] . '/cancel') ?>" method="POST" class="d-inline">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this reservation?')">Cancel</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$reservations['data']): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No bookings found. Try another search.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pages > 1): ?>
        <nav>
            <ul class="pagination mb-0">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?= $i === (int) $reservations['page'] ? 'active' : '' ?>"><a class="page-link" href="<?= url_with_query('reservations', ['page' => $i]) ?>"><?= $i ?></a></li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
