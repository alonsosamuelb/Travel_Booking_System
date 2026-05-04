<div class="row g-4">
    <div class="col-lg-5">
        <div class="card card-soft shadow-sm">
            <h2 class="section-title h4 mb-3">Profile</h2>
            <form method="POST" action="<?= base_url('profile') ?>" data-validate="true">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Full name</label>
                    <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars(old('full_name', $user['full_name'])) ?>" data-required="true">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars(old('email', $user['email'])) ?>" data-required="true">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars(old('phone', $user['phone'])) ?>">
                </div>
                <button class="btn btn-primary">Save profile</button>
            </form>
            <hr>
            <form method="POST" action="<?= base_url('profile/password') ?>" data-validate="true">
                <?= csrf_field() ?>
                <h3 class="h6">Password</h3>
                <input type="password" name="current_password" class="form-control mb-2" placeholder="Current password" data-required="true">
                <input type="password" name="new_password" class="form-control mb-2" placeholder="New password" data-required="true" data-password>
                <input type="password" name="new_password_confirmation" class="form-control mb-3" placeholder="Confirm new password" data-required="true" data-password>
                <button class="btn btn-outline-primary">Update password</button>
            </form>
            <hr>
            <form method="POST" action="<?= base_url('profile/delete') ?>" onsubmit="return confirm('Deactivate your account?')">
                <?= csrf_field() ?>
                <button class="btn btn-outline-danger">Deactivate account</button>
            </form>
            <hr>
            <div>
                <h3 class="h6">API access</h3>
                <p class="small text-muted">Use this token with the Authorization header for API requests.</p>
                <?php if (!empty($_SESSION['_flash']['api_token'])): ?>
                    <div class="alert alert-warning small overflow-auto"><?= htmlspecialchars($_SESSION['_flash']['api_token']) ?></div>
                <?php endif; ?>
                <form method="POST" action="<?= base_url('profile/api-token') ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-secondary">Generate token</button>
                </form>
                <form method="POST" action="<?= base_url('profile/api-token/revoke') ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-danger">Revoke token</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card card-soft shadow-sm">
            <h2 class="section-title h4 mb-3">My reservations</h2>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Trip</th><th>Departure</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?= htmlspecialchars($reservation['trip_name']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($reservation['departure_at'])) ?></td>
                            <td><span class="badge <?= $reservation['status'] === 'active' ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= htmlspecialchars($reservation['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
