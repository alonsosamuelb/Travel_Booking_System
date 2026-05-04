<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Models\PasswordReset;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\RateLimiterService;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        $this->view('auth/login');
    }

    public function login(): void
    {
        $data = Request::all();
        $_SESSION['_old'] = $data;
        $errors = Validator::validate($data, [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ]);

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect('login');
        }

        if (!Auth::attempt($data['email'], $data['password'])) {
            (new ActivityLogService())->log('login_failed', 'auth', null, 'Failed login attempt for ' . trim((string) $data['email']), null);
            $_SESSION['_errors'] = ['email' => 'Invalid credentials or inactive account.'];
            $this->redirect('login');
        }

        (new RateLimiterService())->clear('login|' . ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'));
        (new ActivityLogService())->log('login', 'auth', null, 'User signed in successfully.');
        flash('success', 'Welcome back.');
        $this->redirect('dashboard');
    }

    public function registerForm(): void
    {
        $this->view('auth/register');
    }

    public function register(): void
    {
        $data = Request::all();
        $_SESSION['_old'] = $data;
        $errors = Validator::validate($data, [
            'full_name' => ['required', 'min:3', 'max:120'],
            'email' => ['required', 'email'],
            'phone' => ['max:30'],
            'password' => ['required', 'min:8'],
            'password_confirmation' => ['required', 'min:8'],
        ]);

        if (($data['password'] ?? '') !== ($data['password_confirmation'] ?? '')) {
            $errors['password_confirmation'] = 'Passwords do not match.';
        }

        $userModel = new User();
        if ($userModel->findByEmail($data['email'] ?? '')) {
            $errors['email'] = 'That email is already registered.';
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect('register');
        }

        $userModel->create([
            'full_name' => trim($data['full_name']),
            'email' => trim($data['email']),
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'phone' => trim($data['phone'] ?? ''),
            'role' => 'user',
        ]);

        $newUser = $userModel->findByEmail(trim($data['email']));
        (new ActivityLogService())->log('register', 'user', $newUser['id'] ?? null, 'New user account registered.', $newUser['id'] ?? null);
        flash('success', 'Account created successfully. Please sign in.');
        $this->redirect('login');
    }

    public function logout(): void
    {
        (new ActivityLogService())->log('logout', 'auth', null, 'User signed out.');
        Auth::logout();
        flash('success', 'Session closed.');
        $this->redirect('login');
    }

    public function forgotPasswordForm(): void
    {
        $this->view('auth/forgot-password');
    }

    public function sendResetLink(): void
    {
        $email = trim((string) Request::input('email'));
        $user = (new User())->findByEmail($email);

        if (!$user) {
            $_SESSION['_errors'] = ['email' => 'No account found with that email address.'];
            $this->redirect('forgot-password');
        }

        $token = bin2hex(random_bytes(24));
        $resetModel = new PasswordReset();
        $resetModel->deleteByEmail($email);
        $resetModel->create($email, $token);

        (new ActivityLogService())->log('password_reset_requested', 'user', (int) $user['id'], 'Password reset token generated.', (int) $user['id']);
        flash('success', 'Password reset token created. Demo link: ' . base_url('reset-password?token=' . $token));
        $this->redirect('forgot-password');
    }

    public function resetPasswordForm(): void
    {
        $this->view('auth/reset-password', ['token' => Request::input('token')]);
    }

    public function resetPassword(): void
    {
        $data = Request::all();
        $errors = Validator::validate($data, [
            'token' => ['required'],
            'password' => ['required', 'min:8'],
            'password_confirmation' => ['required', 'min:8'],
        ]);

        if (($data['password'] ?? '') !== ($data['password_confirmation'] ?? '')) {
            $errors['password_confirmation'] = 'Passwords do not match.';
        }

        $reset = (new PasswordReset())->findByToken($data['token'] ?? '');
        if (!$reset) {
            $errors['token'] = 'Reset token is invalid or expired.';
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $_SESSION['_old'] = $data;
            $this->redirect('reset-password?token=' . urlencode((string) ($data['token'] ?? '')));
        }

        $user = (new User())->findByEmail($reset['email']);
        if ($user) {
            (new User())->updatePassword((int) $user['id'], password_hash($data['password'], PASSWORD_DEFAULT));
            (new ActivityLogService())->log('password_reset_completed', 'user', (int) $user['id'], 'Password reset completed.', (int) $user['id']);
        }

        (new PasswordReset())->deleteByEmail($reset['email']);
        flash('success', 'Password updated. You can now sign in.');
        $this->redirect('login');
    }

    public function reactivateForm(): void
    {
        $this->view('auth/reactivate');
    }

    public function reactivate(): void
    {
        $data = Request::all();
        $userModel = new User();
        $user = $userModel->findByEmail(trim((string) ($data['email'] ?? '')));

        if (!$user || !$user['deleted_at'] || !password_verify((string) ($data['password'] ?? ''), $user['password'])) {
            $_SESSION['_errors'] = ['email' => 'We could not validate the deactivated account credentials.'];
            $this->redirect('reactivate-account');
        }

        $userModel->reactivate($user['email']);
        (new ActivityLogService())->log('reactivate', 'user', (int) $user['id'], 'User account reactivated.', (int) $user['id']);
        flash('success', 'Account reactivated. Please sign in.');
        $this->redirect('login');
    }
}
