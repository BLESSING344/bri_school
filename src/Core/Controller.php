<?php

namespace App\Core;

abstract class Controller
{
    protected function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    protected function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit();
    }

    protected function requireLogin(): void
    {
        Auth::requireLogin();
    }

    /**
     * @param string|array $roles
     */
    protected function requireRole($roles): void
    {
        Auth::requireRole($roles);
    }

    /**
     * Renders a view wrapped in the dashboard layout (header/sidebar/footer).
     */
    protected function render(string $view, array $data = []): void
    {
        $data['current_user'] = Auth::user();
        $data['is_admin'] = Auth::isAdmin();
        $data['is_teacher'] = Auth::isTeacher();
        $data['is_bursar'] = Auth::isBursar();
        $data['page_title'] = $data['page_title'] ?? 'Dashboard';

        extract($data);
        require __DIR__ . '/../Views/layout/header.php';
        require __DIR__ . "/../Views/{$view}.php";
        require __DIR__ . '/../Views/layout/footer.php';
    }

    /**
     * Renders a standalone view with no layout (e.g. the login page).
     */
    protected function renderBare(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . "/../Views/{$view}.php";
    }

    protected function json(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
