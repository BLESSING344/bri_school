<?php

namespace App\Core;

class Auth
{
    private static ?array $userCache = null;

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @return true|string true on success, 'inactive' if the account is deactivated, 'invalid' otherwise
     */
    public static function attempt(string $username, string $password)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, username, password, full_name, role, is_active FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return 'invalid';
        }

        if (!$user['is_active']) {
            return 'inactive';
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];

        try {
            $pdo->prepare('UPDATE users SET created_at = NOW() WHERE id = ?')->execute([$user['id']]);
        } catch (\PDOException $e) {
            // best-effort "last login" touch, ignore failures
        }

        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        if (self::$userCache === null) {
            $stmt = Database::connection()->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            self::$userCache = $stmt->fetch() ?: null;
        }

        return self::$userCache;
    }

    public static function role(): ?string
    {
        return $_SESSION['role'] ?? null;
    }

    public static function is(string $role): bool
    {
        return self::role() === $role;
    }

    public static function isAdmin(): bool
    {
        return self::is('admin');
    }

    public static function isTeacher(): bool
    {
        return self::is('teacher');
    }

    public static function isBursar(): bool
    {
        return self::is('bursar');
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: /login');
            exit();
        }
    }

    /**
     * @param string|array $roles
     */
    public static function requireRole($roles): void
    {
        self::requireLogin();
        $roles = is_array($roles) ? $roles : [$roles];
        if (!in_array(self::role(), $roles, true)) {
            header('Location: /dashboard?error=access_denied');
            exit();
        }
    }
}
