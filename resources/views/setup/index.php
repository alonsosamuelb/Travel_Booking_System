<?php
$values = array_merge($defaults, $_SESSION['_old'] ?? []);
?>
<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="card card-soft shadow-sm mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="section-title h3 mb-1">Project setup</h2>
                    <p class="text-muted mb-0">Configure the application and initialize the database.</p>
                </div>
                <span class="badge <?= $installed ? 'text-bg-success' : 'text-bg-warning' ?>">
                    <?= $installed ? 'Installed' : 'Pending setup' ?>
                </span>
            </div>
        </div>

        <div class="card card-soft shadow-sm">
            <form method="POST" action="<?= base_url('setup') ?>" data-validate="true">
                <?= csrf_field() ?>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h3 class="h5 mb-3">Application</h3>
                        <div class="mb-3">
                            <label class="form-label">App name</label>
                            <input class="form-control" name="app_name" value="<?= htmlspecialchars($values['app_name']) ?>" data-required="true">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Environment</label>
                                <select class="form-select" name="app_env">
                                    <option value="local" <?= $values['app_env'] === 'local' ? 'selected' : '' ?>>local</option>
                                    <option value="production" <?= $values['app_env'] === 'production' ? 'selected' : '' ?>>production</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Debug</label>
                                <select class="form-select" name="app_debug">
                                    <option value="true" <?= $values['app_debug'] === 'true' ? 'selected' : '' ?>>true</option>
                                    <option value="false" <?= $values['app_debug'] === 'false' ? 'selected' : '' ?>>false</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mt-0">
                            <div class="col-md-6">
                                <label class="form-label">Timezone</label>
                                <input class="form-control" name="app_timezone" value="<?= htmlspecialchars($values['app_timezone']) ?>" data-required="true">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reservation limit</label>
                                <input class="form-control" name="reservation_limit_per_user" value="<?= htmlspecialchars($values['reservation_limit_per_user']) ?>" data-required="true">
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">Base URL</label>
                            <input class="form-control" name="app_base_url" value="<?= htmlspecialchars($values['app_base_url']) ?>" data-required="true">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Support email</label>
                            <input type="email" class="form-control" name="support_email" value="<?= htmlspecialchars($values['support_email']) ?>" data-required="true">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h3 class="h5 mb-3">Database</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Host</label>
                                <input class="form-control" name="db_host" value="<?= htmlspecialchars($values['db_host']) ?>" data-required="true">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Port</label>
                                <input class="form-control" name="db_port" value="<?= htmlspecialchars($values['db_port']) ?>" data-required="true">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Database name</label>
                                <input class="form-control" name="db_database" value="<?= htmlspecialchars($values['db_database']) ?>" data-required="true">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Username</label>
                                <input class="form-control" name="db_username" value="<?= htmlspecialchars($values['db_username']) ?>" data-required="true">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="db_password" value="<?= htmlspecialchars($values['db_password']) ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Charset</label>
                                <input class="form-control" name="db_charset" value="<?= htmlspecialchars($values['db_charset']) ?>" data-required="true">
                            </div>
                        </div>
                        <div class="alert alert-info mt-4 mb-0">
                            This setup will create or update `.env`, create the database if permissions allow it, run migrations and seed demo data.
                        </div>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <small class="text-muted">After installation you can sign in with the seeded demo accounts from the README.</small>
                    <button class="btn btn-primary">Run setup</button>
                </div>
            </form>
        </div>
    </div>
</div>
