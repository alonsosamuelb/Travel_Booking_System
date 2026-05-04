<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card card-soft shadow-sm">
            <h2 class="section-title h3 mb-3">Password recovery</h2>
            <p class="text-muted">This demo generates a reset token and displays the link in a flash message.</p>
            <form method="POST" action="<?= base_url('forgot-password') ?>" data-validate="true">
                <?= csrf_field() ?>
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control mb-3" data-required="true">
                <button class="btn btn-primary">Generate reset link</button>
            </form>
        </div>
    </div>
</div>
