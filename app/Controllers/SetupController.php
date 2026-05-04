<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Services\InstallerService;

class SetupController extends Controller
{
    public function show(): void
    {
        $installer = new InstallerService();

        $this->view('setup/index', [
            'defaults' => $installer->defaults(),
            'installed' => $installer->isInstalled(),
        ]);
    }

    public function install(): void
    {
        $data = Request::all();
        $_SESSION['_old'] = $data;
        $errors = Validator::validate($data, [
            'app_name' => ['required', 'min:3'],
            'app_env' => ['required'],
            'app_debug' => ['required', 'in:true,false'],
            'app_timezone' => ['required'],
            'app_base_url' => ['required'],
            'support_email' => ['required', 'email'],
            'db_host' => ['required'],
            'db_port' => ['required', 'integer'],
            'db_database' => ['required'],
            'db_username' => ['required'],
            'db_charset' => ['required'],
            'reservation_limit_per_user' => ['required', 'integer'],
        ]);

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect('setup');
        }

        try {
            $migrations = (new InstallerService())->install($data);
        } catch (\RuntimeException $exception) {
            $_SESSION['_errors'] = ['setup' => $exception->getMessage()];
            $this->redirect('setup');
        }

        flash('success', 'Application installed successfully. Applied migrations: ' . ($migrations ? implode(', ', $migrations) : 'none'));
        $this->redirect('setup');
    }
}
