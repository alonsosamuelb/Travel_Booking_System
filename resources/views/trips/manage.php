<?php
$pages = max(1, (int) ceil($userTrips['total'] / $userTrips['per_page']));
$formTrip = $editingTrip ?? null;
?>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card card-soft shadow-sm mb-4">
            <div class="d-flex justify-content-between align-items-center gap-3">
                <div>
                    <h2 class="section-title h3 mb-1">Trips you host</h2>
                    <p class="text-muted mb-0">Publish and manage your trips as a driver.</p>
                </div>
            </div>
            <form method="GET" class="row g-3 mt-1">
                <div class="col-md-8"><input type="text" class="form-control" name="search" placeholder="Search your trips" value="<?= htmlspecialchars($filters['search'] ?? '') ?>"></div>
                <div class="col-md-4">
                    <select class="form-select" name="status">
                        <option value="">All statuses</option>
                        <option value="published" <?= ($filters['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="card card-soft shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Name</th><th>Route</th><th>Departure</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($userTrips['data'] as $trip): ?>
                        <tr>
                            <td><?= htmlspecialchars($trip['name']) ?></td>
                            <td><?= htmlspecialchars($trip['origin']) ?> to <?= htmlspecialchars($trip['destination']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($trip['departure_at'])) ?></td>
                            <td><span class="badge <?= $trip['status'] === 'published' ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= htmlspecialchars($trip['status']) ?></span></td>
                            <td class="text-end">
                                <a href="<?= base_url('my-trips?edit=' . $trip['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="<?= base_url('my-trips/' . $trip['id'] . '/delete') ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this trip?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$userTrips['data']): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">You have not published any trips yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($pages > 1): ?>
                <ul class="pagination mb-0">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <li class="page-item <?= $i === (int) $userTrips['page'] ? 'active' : '' ?>"><a class="page-link" href="<?= url_with_query('my-trips', ['page' => $i]) ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card card-soft shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="h5 mb-0"><?= $formTrip ? 'Edit hosted trip' : 'Publish a trip' ?></h3>
                    <div class="small text-muted mt-1">Create a trip as a driver and make it available to other users.</div>
                </div>
                <?php if ($formTrip): ?><a href="<?= base_url('my-trips') ?>" class="btn btn-sm btn-outline-secondary">Cancel edit</a><?php endif; ?>
            </div>
            <form method="POST" action="<?= base_url('my-trips/save') ?>" enctype="multipart/form-data" data-validate="true">
                <?= csrf_field() ?>
                <?php if ($formTrip): ?><input type="hidden" name="id" value="<?= (int) $formTrip['id'] ?>"><?php endif; ?>
                <input type="text" class="form-control mb-2" name="name" placeholder="Trip name" value="<?= htmlspecialchars(old('name', $formTrip['name'] ?? '')) ?>" data-required="true">
                <textarea class="form-control mb-2" name="description" rows="3" placeholder="Description" data-required="true"><?= htmlspecialchars(old('description', $formTrip['description'] ?? '')) ?></textarea>
                <input type="text" class="form-control mb-2" name="origin" placeholder="Origin" value="<?= htmlspecialchars(old('origin', $formTrip['origin'] ?? '')) ?>" data-required="true">
                <input type="text" class="form-control mb-2" name="destination" placeholder="Destination" value="<?= htmlspecialchars(old('destination', $formTrip['destination'] ?? '')) ?>" data-required="true">
                <input type="datetime-local" class="form-control mb-2" name="departure_at" value="<?= htmlspecialchars(old('departure_at', isset($formTrip['departure_at']) ? date('Y-m-d\TH:i', strtotime($formTrip['departure_at'])) : '')) ?>" data-required="true">
                <input type="text" class="form-control mb-2" name="vehicle" placeholder="Vehicle" value="<?= htmlspecialchars(old('vehicle', $formTrip['vehicle'] ?? '')) ?>" data-required="true">
                <input type="number" min="1" class="form-control mb-2" name="available_seats" placeholder="Available seats" value="<?= htmlspecialchars(old('available_seats', $formTrip['available_seats'] ?? '')) ?>" data-required="true">
                <input type="text" class="form-control mb-2" name="image_path" placeholder="Image URL (optional)" value="<?= htmlspecialchars(old('image_path', $formTrip['image_path'] ?? '')) ?>">
                <input type="file" class="form-control mb-2" name="image_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                <div class="form-text mb-2">Upload an image or use a URL. Max 2 MB.</div>
                <?php if ($formTrip): ?>
                    <img src="<?= htmlspecialchars(trip_image_url($formTrip['image_path'] ?? null)) ?>" alt="<?= htmlspecialchars($formTrip['name']) ?>" class="img-fluid rounded-3 mb-2" style="max-height: 180px; object-fit: cover;">
                <?php endif; ?>
                <select class="form-select mb-3" name="status" data-required="true">
                    <option value="published" <?= old('status', $formTrip['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= old('status', $formTrip['status'] ?? 'published') === 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
                <button class="btn btn-primary"><?= $formTrip ? 'Update trip' : 'Publish trip' ?></button>
            </form>
        </div>
    </div>
</div>
