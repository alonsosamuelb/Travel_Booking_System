<?php
$pages = max(1, (int) ceil($trips['total'] / $trips['per_page']));
$formTrip = $editingTrip ?? null;
?>
<div class="card card-soft shadow-sm mb-4">
    <h2 class="section-title h3 mb-3">Trip management</h2>
    <form method="GET" class="row g-3">
        <div class="col-md-4"><input type="text" class="form-control" name="search" placeholder="Search trips" value="<?= htmlspecialchars($filters['search'] ?? '') ?>"></div>
        <div class="col-md-3"><input type="text" class="form-control" name="origin" placeholder="Origin" value="<?= htmlspecialchars($filters['origin'] ?? '') ?>"></div>
        <div class="col-md-3"><input type="text" class="form-control" name="destination" placeholder="Destination" value="<?= htmlspecialchars($filters['destination'] ?? '') ?>"></div>
        <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
    </form>
</div>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card card-soft shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Name</th><th>Route</th><th>Departure</th><th>Seats</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($trips['data'] as $trip): ?>
                        <tr>
                            <td><?= htmlspecialchars($trip['name']) ?></td>
                            <td><?= htmlspecialchars($trip['origin']) ?> to <?= htmlspecialchars($trip['destination']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($trip['departure_at'])) ?></td>
                            <td><?= (int) $trip['available_seats'] ?></td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/trips?edit=' . $trip['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="<?= base_url('admin/trips/' . $trip['id'] . '/delete') ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <ul class="pagination mb-0">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?= $i === (int) $trips['page'] ? 'active' : '' ?>"><a class="page-link" href="<?= base_url('admin/trips?page=' . $i) ?>"><?= $i ?></a></li>
                <?php endfor; ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card card-soft shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h5 mb-0"><?= $formTrip ? 'Edit trip' : 'Create trip' ?></h3>
                <?php if ($formTrip): ?><a href="<?= base_url('admin/trips') ?>" class="btn btn-sm btn-outline-secondary">Cancel edit</a><?php endif; ?>
            </div>
            <form method="POST" action="<?= base_url('admin/trips/save') ?>" enctype="multipart/form-data" data-validate="true">
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
                <?php if (!empty($formTrip['image_path'])): ?>
                    <img src="<?= htmlspecialchars($formTrip['image_path']) ?>" alt="<?= htmlspecialchars($formTrip['name']) ?>" class="img-fluid rounded-3 mb-2" style="max-height: 180px; object-fit: cover;">
                <?php endif; ?>
                <select class="form-select mb-3" name="status" data-required="true">
                    <option value="published" <?= old('status', $formTrip['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= old('status', $formTrip['status'] ?? 'published') === 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
                <button class="btn btn-primary"><?= $formTrip ? 'Update trip' : 'Save trip' ?></button>
            </form>
        </div>
    </div>
</div>
