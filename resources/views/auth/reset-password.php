<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-soft shadow-sm">
            <h2 class="section-title h3 mb-4">Reset password</h2>
            <form method="POST" action="<?= base_url('reset-password') ?>" data-validate="true">
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? old('token')) ?>">
                <div class="mb-3">
                    <label class="form-label">New password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="newPassword" class="form-control" data-required="true" data-password>
                        <button class="btn btn-outline-secondary" type="button" data-toggle-password="#newPassword">Show</button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm password</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="newPasswordConfirm" class="form-control" data-required="true" data-password>
                        <button class="btn btn-outline-secondary" type="button" data-toggle-password="#newPasswordConfirm">Show</button>
                    </div>
                </div>
                <button class="btn btn-primary">Update password</button>
            </form>
        </div>
    </div>
</div>
