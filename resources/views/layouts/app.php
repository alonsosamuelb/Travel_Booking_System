<?php

use App\Core\Auth;

$user = Auth::user();
$errors = $_SESSION['_errors'] ?? [];
$flashSuccess = $_SESSION['_flash']['success'] ?? null;
$flashError = $_SESSION['_flash']['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(config('app.name')) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark app-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="<?= base_url() ?>"><?= htmlspecialchars(config('app.name')) ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link <?= is_active('/trips') ?>" href="<?= base_url('trips') ?>">Trips</a></li>
                    <?php if ($user): ?>
                        <li class="nav-item"><a class="nav-link <?= is_active('/reservations') ?>" href="<?= base_url('reservations') ?>">Reservations</a></li>
                        <li class="nav-item"><a class="nav-link <?= is_active('/dashboard') ?>" href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <?php if ($user['role'] === 'admin'): ?>
                            <li class="nav-item"><a class="nav-link <?= is_active('/admin') ?>" href="<?= base_url('admin') ?>">Admin</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($user): ?>
                        <a class="btn btn-sm btn-outline-light" href="<?= base_url('profile') ?>"><?= htmlspecialchars($user['full_name']) ?></a>
                        <form action="<?= base_url('logout') ?>" method="POST" class="m-0">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-warning-subtle text-dark" type="submit">Logout</button>
                        </form>
                    <?php else: ?>
                        <a class="btn btn-sm btn-outline-light" href="<?= base_url('login') ?>">Login</a>
                        <a class="btn btn-sm btn-light" href="<?= base_url('register') ?>">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <header class="hero-banner">
        <div class="container py-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="badge text-bg-light mb-3">Booking platform</span>
                    <h1 class="display-6 fw-bold mb-3">Manage trips, users and reservations in one place.</h1>
                    <p class="lead text-white-50 mb-0">A complete booking platform with user and admin access.</p>
                </div>
                <div class="col-lg-5">
                    <div class="hero-card shadow-lg">
                        <div class="small text-uppercase text-muted mb-2">Modules</div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="chip">Auth + roles</span>
                            <span class="chip">Trips CRUD</span>
                            <span class="chip">Reservations</span>
                            <span class="chip">Administration</span>
                            <span class="chip">PDF / CSV</span>
                            <span class="chip">REST API</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container py-4">
        <?php if ($flashSuccess): ?><div class="alert alert-success shadow-sm"><?= htmlspecialchars($flashSuccess) ?></div><?php endif; ?>
        <?php if ($flashError): ?><div class="alert alert-danger shadow-sm"><?= htmlspecialchars($flashError) ?></div><?php endif; ?>
        <?php if ($errors): ?>
            <div class="alert alert-danger shadow-sm">
                <strong>Please review the form.</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?= $content ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('assets/js/app.js') ?>"></script>
</body>
</html>
