<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card card-soft shadow-sm">
            <h2 class="section-title h3 mb-4">Reactivate account</h2>
            <form method="POST" action="<?= base_url('reactivate-account') ?>" data-validate="true">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" data-required="true">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="reactivatePassword" class="form-control" data-required="true">
                        <button class="btn btn-outline-secondary" type="button" data-toggle-password="#reactivatePassword">Show</button>
                    </div>
                </div>
                <button class="btn btn-primary">Reactivate</button>
            </form>
        </div>
    </div>
</div>
