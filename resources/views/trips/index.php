<?php $pages = max(1, (int) ceil($trips['total'] / $trips['per_page'])); ?>
<div class="card card-soft shadow-sm mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h2 class="section-title h3 mb-1">Available trips for you</h2>
            <p class="text-muted mb-0">Choose your next destination and book in a few clicks.</p>
        </div>
        <a href="<?= base_url('reservations/create') ?>" class="btn btn-primary">Book now</a>
    </div>
    <form method="GET" class="row g-3 mt-1">
        <div class="col-md-4"><input type="text" class="form-control" name="search" placeholder="Where do you want to go?" value="<?= htmlspecialchars($filters['search'] ?? '') ?>"></div>
        <div class="col-md-3"><input type="text" class="form-control" name="origin" placeholder="Leaving from" value="<?= htmlspecialchars($filters['origin'] ?? '') ?>"></div>
        <div class="col-md-3"><input type="text" class="form-control" name="destination" placeholder="Going to" value="<?= htmlspecialchars($filters['destination'] ?? '') ?>"></div>
        <div class="col-md-2"><button class="btn btn-outline-primary w-100">Search</button></div>
    </form>
</div>
<div class="row g-4">
    <?php foreach ($trips['data'] as $trip): ?>
        <div class="col-md-6 col-xl-4">
            <div class="card trip-card shadow-sm h-100">
                <img src="<?= htmlspecialchars(trip_image_url($trip['image_path'] ?? null)) ?>" alt="<?= htmlspecialchars($trip['name']) ?>">
                <div class="card-body d-flex flex-column">
                    <div class="small text-uppercase text-muted mb-2"><?= htmlspecialchars($trip['vehicle']) ?></div>
                    <h3 class="h5"><?= htmlspecialchars($trip['name']) ?></h3>
                    <p class="text-muted"><?= htmlspecialchars($trip['origin']) ?> to <?= htmlspecialchars($trip['destination']) ?></p>
                    <p class="small mb-3"><?= date('d/m/Y H:i', strtotime($trip['departure_at'])) ?></p>
                    <div class="mt-auto d-flex justify-content-between align-items-center">
                        <span class="badge badge-soft"><?= max(0, (int) $trip['available_seats'] - (int) $trip['reserved_seats']) ?> available seats</span>
                        <a href="<?= base_url('trips/' . $trip['id']) ?>" class="btn btn-sm btn-primary">Book now</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (!$trips['data']): ?>
        <div class="col-12">
            <div class="card card-soft shadow-sm text-center text-muted py-5">
                No trips found. Try another destination.
            </div>
        </div>
    <?php endif; ?>
</div>
<?php if ($pages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?= $i === (int) $trips['page'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= base_url('trips?page=' . $i) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>
