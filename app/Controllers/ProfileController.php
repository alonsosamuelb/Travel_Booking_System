<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Models\Reservation;
use App\Models\User;

class ProfileController extends Controller
{
    public function show(): void
    {
        $user = Auth::user();
        $this->view('profile/show', [
            'user' => $user,
            'reservations' => (new Reservation())->paginate([], 1, 10, (int) $user['id'])['data'],
        ]);
    }

    public function update(): void
    {
        $user = Auth::user();
        $data = Request::all();
        $errors = Validator::validate($data, [
            'full_name' => ['required', 'min:3', 'max:120'],
            'email' => ['required', 'email'],
            'phone' => ['max:30'],
        ]);

        $existing = (new User())->findByEmail($data['email'] ?? '');
        if ($existing && (int) $existing['id'] !== (int) $user['id']) {
            $errors['email'] = 'This email is already used by another account.';
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $_SESSION['_old'] = $data;
            $this->redirect('profile');
        }

        (new User())->updateProfile((int) $user['id'], [
            'full_name' => trim($data['full_name']),
            'email' => trim($data['email']),
            'phone' => trim($data['phone'] ?? ''),
        ]);

        flash('success', 'Profile updated.');
        $this->redirect('profile');
    }

    public function updatePassword(): void
    {
        $user = Auth::user();
        $data = Request::all();

        if (!password_verify((string) ($data['current_password'] ?? ''), $user['password'])) {
            $_SESSION['_errors'] = ['current_password' => 'Current password is incorrect.'];
            $this->redirect('profile');
        }

        if (($data['new_password'] ?? '') !== ($data['new_password_confirmation'] ?? '')) {
            $_SESSION['_errors'] = ['new_password_confirmation' => 'Passwords do not match.'];
            $this->redirect('profile');
        }

        (new User())->updatePassword((int) $user['id'], password_hash($data['new_password'], PASSWORD_DEFAULT));
        flash('success', 'Password updated.');
        $this->redirect('profile');
    }

    public function deleteAccount(): void
    {
        $user = Auth::user();
        (new User())->softDelete((int) $user['id']);
        Auth::logout();
        flash('success', 'Your account has been deactivated.');
        $this->redirect('reactivate-account');
    }

    public function generateApiToken(): void
    {
        $user = Auth::user();
        $token = (new User())->refreshApiToken((int) $user['id']);
        $_SESSION['_flash']['api_token'] = $token;
        flash('success', 'New API token generated. Copy it now because it will not be shown again.');
        $this->redirect('profile');
    }

    public function revokeApiToken(): void
    {
        $user = Auth::user();
        (new User())->revokeApiToken((int) $user['id']);
        flash('success', 'API token revoked.');
        $this->redirect('profile');
    }
}
