<?php
$pages = max(1, (int) ceil($reservations['total'] / $reservations['per_page']));
$formReservation = $editingReservation ?? null;
?>
<div class="card card-soft shadow-sm mb-4">
    <h2 class="section-title h3 mb-3">Reservation management</h2>
    <form method="GET" class="row g-3">
        <div class="col-md-6"><input type="text" class="form-control" name="search" placeholder="Search reservations" value="<?= htmlspecialchars($filters['search'] ?? '') ?>"></div>
        <div class="col-md-3">
            <select class="form-select" name="status">
                <option value="">All statuses</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                <option value="finished" <?= ($filters['status'] ?? '') === 'finished' ? 'selected' : '' ?>>Finished</option>
            </select>
        </div>
        <div class="col-md-3"><button class="btn btn-outline-primary w-100">Filter</button></div>
    </form>
</div>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card card-soft shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>User</th><th>Trip</th><th>Seats</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($reservations['data'] as $reservation): ?>
                        <tr>
                            <td><?= htmlspecialchars($reservation['full_name']) ?></td>
                            <td><?= htmlspecialchars($reservation['trip_name']) ?></td>
                            <td><?= (int) $reservation['seats_reserved'] ?></td>
                            <td>
                                <span class="badge <?= $reservation['status'] === 'active' ? 'text-bg-success' : ($reservation['status'] === 'completed' ? 'text-bg-dark' : 'text-bg-secondary') ?>">
                                    <?= htmlspecialchars($reservation['status'] === 'completed' ? 'finished' : $reservation['status']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/reservations?edit=' . $reservation['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <?php if ($reservation['status'] === 'active'): ?>
                                    <form method="POST" action="<?= base_url('admin/reservations/' . $reservation['id'] . '/cancel') ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-outline-danger">Cancel</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$reservations['data']): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No reservations found for the current filters.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($pages > 1): ?>
                <ul class="pagination mb-0">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <li class="page-item <?= $i === (int) $reservations['page'] ? 'active' : '' ?>"><a class="page-link" href="<?= url_with_query('admin/reservations', ['page' => $i]) ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card card-soft shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="h5 mb-0"><?= $formReservation ? 'Edit reservation' : 'New reservation' ?></h3>
                    <?php if ($formReservation): ?><div class="small text-muted mt-1">Editing reservation #<?= (int) $formReservation['id'] ?></div><?php endif; ?>
                </div>
                <?php if ($formReservation): ?><a href="<?= base_url('admin/reservations') ?>" class="btn btn-sm btn-outline-secondary">Cancel edit</a><?php endif; ?>
            </div>
            <form method="POST" action="<?= base_url('admin/reservations/save') ?>" data-validate="true">
                <?= csrf_field() ?>
                <?php if ($formReservation): ?><input type="hidden" name="id" value="<?= (int) $formReservation['id'] ?>"><?php endif; ?>
                <select class="form-select mb-2" name="user_id" data-required="true">
                    <option value="">Select user</option>
                    <?php foreach ($users as $user): ?>
                        <?php if (!$user['deleted_at']): ?>
                            <option value="<?= (int) $user['id'] ?>" <?= (string) old('user_id', $formReservation['user_id'] ?? '') === (string) $user['id'] ? 'selected' : '' ?>><?= htmlspecialchars($user['full_name']) ?> (<?= htmlspecialchars($user['email']) ?>)</option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <select class="form-select mb-2" name="trip_id" data-required="true">
                    <option value="">Select trip</option>
                    <?php foreach ($tripOptions as $trip): ?>
                        <option value="<?= (int) $trip['id'] ?>" <?= (string) old('trip_id', $formReservation['trip_id'] ?? '') === (string) $trip['id'] ? 'selected' : '' ?>><?= htmlspecialchars($trip['name']) ?> | <?= date('d/m/Y H:i', strtotime($trip['departure_at'])) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="datetime-local" class="form-control mb-2" name="reservation_date" value="<?= htmlspecialchars(old('reservation_date', isset($formReservation['reservation_date']) ? date('Y-m-d\TH:i', strtotime($formReservation['reservation_date'])) : '')) ?>" data-required="true">
                <input type="number" min="1" class="form-control mb-2" name="seats_reserved" placeholder="Seats" value="<?= htmlspecialchars(old('seats_reserved', $formReservation['seats_reserved'] ?? '')) ?>" data-required="true">
                <select class="form-select mb-2" name="travel_role" data-required="true">
                    <option value="passenger" <?= old('travel_role', $formReservation['travel_role'] ?? 'passenger') === 'passenger' ? 'selected' : '' ?>>Passenger</option>
                    <option value="driver" <?= old('travel_role', $formReservation['travel_role'] ?? 'passenger') === 'driver' ? 'selected' : '' ?>>Driver</option>
                </select>
                <textarea class="form-control mb-2" name="notes" rows="3" placeholder="Notes"><?= htmlspecialchars(old('notes', $formReservation['notes'] ?? '')) ?></textarea>
                <select class="form-select mb-3" name="status" data-required="true">
                    <option value="active" <?= old('status', $formReservation['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="cancelled" <?= old('status', $formReservation['status'] ?? 'active') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    <option value="completed" <?= old('status', $formReservation['status'] ?? 'active') === 'completed' ? 'selected' : '' ?>>Finished</option>
                </select>
                <button class="btn btn-primary"><?= $formReservation ? 'Update reservation' : 'Save reservation' ?></button>
            </form>
        </div>
    </div>
</div>
