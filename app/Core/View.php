<?php

namespace App\Core;

class View
{
    public static function render(string $view, array $data = [], string $layout = 'layouts/app'): void
    {
        $viewPath = __DIR__ . '/../../resources/views/' . $view . '.php';
        $layoutPath = __DIR__ . '/../../resources/views/' . $layout . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(404);
            exit('View not found: ' . $view);
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require $layoutPath;

        unset($_SESSION['_old']);
        unset($_SESSION['_errors']);
        unset($_SESSION['_flash']);
    }
}
