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
    <link rel="stylesheet" href="<?= asset('assets/css/app.css') . '?v=' . filemtime(__DIR__ . '/../../../public/assets/css/app.css') ?>">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark app-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand app-brand" href="<?= base_url() ?>"><?= htmlspecialchars(config('app.name')) ?></a>
            <button class="navbar-toggler app-navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse app-navbar-collapse" id="mainNav">
                <ul class="navbar-nav app-nav-links me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link app-nav-link <?= is_active('/trips') ?>" href="<?= base_url('trips') ?>">Explore</a></li>
                    <?php if ($user): ?>
                        <li class="nav-item"><a class="nav-link app-nav-link <?= is_active('/reservations') ?>" href="<?= base_url('reservations') ?>">My bookings</a></li>
                        <li class="nav-item"><a class="nav-link app-nav-link <?= is_active('/dashboard') ?>" href="<?= base_url('dashboard') ?>">My account</a></li>
                        <?php if ($user['role'] === 'admin'): ?>
                            <li class="nav-item"><a class="nav-link app-nav-link <?= is_active('/admin') ?>" href="<?= base_url('admin') ?>">Admin</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <div class="app-user-actions d-flex align-items-center gap-2">
                    <?php if ($user): ?>
                        <a class="btn btn-sm app-user-chip" href="<?= base_url('profile') ?>"><?= htmlspecialchars($user['full_name']) ?></a>
                        <form action="<?= base_url('logout') ?>" method="POST" class="m-0">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm app-logout-btn" type="submit">Logout</button>
                        </form>
                    <?php else: ?>
                        <a class="btn btn-sm app-login-btn" href="<?= base_url('login') ?>">Login</a>
                        <a class="btn btn-sm app-register-btn" href="<?= base_url('register') ?>">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <header class="hero-banner">
        <div class="container hero-inner">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge text-bg-light mb-3">Travel booking</span>
                    <h1 class="display-6 fw-bold mb-3">Plan and book your trips easily.</h1>
                    <p class="lead text-white-50 mb-0">Find, book and manage your trips in seconds.</p>
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

    <footer class="site-footer">
        <div class="footer-top">
            <div class="container">
                <div class="footer-top-inner">
                    <span class="footer-eyebrow">Travelly Europe</span>
                    <div class="footer-kicker">Online booking platform</div>
                    <h2 class="footer-heading">Travel planning made simple.</h2>
                    <p class="footer-lead mb-0">Explore routes, book seats and manage your trips with a cleaner and faster experience.</p>
                </div>
            </div>
        </div>
        <div class="footer-main">
            <div class="container">
                <div class="row g-5">
                    <div class="col-lg-4">
                        <div class="footer-brand">Travel Booking System</div>
                        <p class="footer-text">A modern booking platform for local and regional trips, designed to make route discovery and reservation management easier.</p>
                    </div>
                    <div class="col-lg-3">
                        <div class="footer-title">Company</div>
                        <ul class="footer-list">
                            <li>Travelly Europe</li>
                            <li>Online booking services</li>
                            <li>Gran Via 128, Madrid</li>
                            <li>Mon - Fri, 09:00 - 18:00</li>
                        </ul>
                    </div>
                    <div class="col-lg-3">
                        <div class="footer-title">Contact</div>
                        <ul class="footer-list">
                            <li>support@travellyeurope.com</li>
                            <li>+34 900 123 456</li>
                            <li>help.travellyeurope.com</li>
                            <li>Press and partnerships</li>
                        </ul>
                    </div>
                    <div class="col-lg-2">
                        <div class="footer-title">Project author</div>
                        <p class="footer-text mb-0">Samuel Buitrago Alonso</p>
                    </div>
                </div>
                <div class="footer-divider"></div>
                <div class="footer-bottom">
                    <span>© <?= date('Y') ?> Travelly Europe. All rights reserved.</span>
                    <div class="footer-bottom-links">
                        <span>Privacy</span>
                        <span>Terms</span>
                        <span>Site map</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('assets/js/app.js') ?>"></script>
</body>
</html>
