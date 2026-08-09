<?php

namespace App\Models;

use App\Core\Model;

class UserModel extends Model
{
    protected string $table = 'users';

    public function allOrdered(): array
    {
        return $this->query(
            'SELECT id, username, full_name, role, is_active, created_at FROM users ORDER BY created_at DESC'
        )->fetchAll();
    }

    /**
     * Fetch a user for the edit form, excluding the password hash.
     */
    public function findForEdit($id): ?array
    {
        $stmt = $this->query(
            'SELECT id, username, full_name, role, is_active FROM users WHERE id = ?',
            [$id]
        );
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function usernameExists(string $username, $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = $this->query('SELECT id FROM users WHERE username = ? AND id != ?', [$username, $excludeId]);
        } else {
            $stmt = $this->query('SELECT id FROM users WHERE username = ?', [$username]);
        }

        return (bool) $stmt->fetch();
    }

    public function createUser(string $username, string $password, string $fullName, string $role)
    {
        return $this->insert([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'full_name' => $fullName,
            'role' => $role,
        ]);
    }

    public function updateUser($id, string $username, string $fullName, string $role, int $isActive): bool
    {
        return $this->update($id, [
            'username' => $username,
            'full_name' => $fullName,
            'role' => $role,
            'is_active' => $isActive,
        ]);
    }

    public function changePassword($id, string $password): bool
    {
        return $this->update($id, [
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }
}
