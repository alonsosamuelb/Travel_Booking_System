<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card card-soft shadow-sm">
            <h2 class="section-title h3 mb-4">Register</h2>
            <form method="POST" action="<?= base_url('register') ?>" data-validate="true">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full name</label>
                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars(old('full_name')) ?>" data-required="true">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars(old('phone')) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars(old('email')) ?>" data-required="true">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="registerPassword" class="form-control" data-required="true" data-password>
                            <button class="btn btn-outline-secondary" type="button" data-toggle-password="#registerPassword">Show</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm password</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="registerPasswordConfirm" class="form-control" data-required="true" data-password>
                            <button class="btn btn-outline-secondary" type="button" data-toggle-password="#registerPasswordConfirm">Show</button>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary mt-4">Create account</button>
            </form>
        </div>
    </div>
</div>
