<div class="row g-4">
    <div class="col-lg-7">
        <img src="<?= htmlspecialchars($trip['image_path']) ?>" alt="<?= htmlspecialchars($trip['name']) ?>" class="img-fluid rounded-4 shadow-sm w-100">
    </div>
    <div class="col-lg-5">
        <div class="card card-soft shadow-sm h-100">
            <div class="small text-uppercase text-muted"><?= htmlspecialchars($trip['vehicle']) ?></div>
            <h2 class="section-title h3"><?= htmlspecialchars($trip['name']) ?></h2>
            <p><?= nl2br(htmlspecialchars($trip['description'])) ?></p>
            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item px-0"><strong>Route:</strong> <?= htmlspecialchars($trip['origin']) ?> to <?= htmlspecialchars($trip['destination']) ?></li>
                <li class="list-group-item px-0"><strong>Departure:</strong> <?= date('d/m/Y H:i', strtotime($trip['departure_at'])) ?></li>
                <li class="list-group-item px-0"><strong>Seats available:</strong> <?= max(0, (int) $trip['available_seats'] - (int) $trip['reserved_seats']) ?></li>
            </ul>
            <a href="<?= base_url('reservations/create') ?>" class="btn btn-primary">Book this trip</a>
        </div>
    </div>
</div>
