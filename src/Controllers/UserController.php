<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use PDOException;

class UserController extends Controller
{
    public function index(): void
    {
        $this->requireRole('admin');

        $users = new UserModel();

        if ($this->isPost()) {
            $this->handlePost($users);
        }

        try {
            $userList = $users->allOrdered();
        } catch (PDOException $e) {
            $userList = [];
            error_log('Users query error: ' . $e->getMessage());
        }

        $editUser = null;
        if (isset($_GET['edit'])) {
            try {
                $editUser = $users->findForEdit($_GET['edit']);
            } catch (PDOException $e) {
                $editUser = null;
            }
        }

        $this->render('users/index', [
            'page_title' => 'User Management',
            'users' => $userList,
            'edit_user' => $editUser,
        ]);
    }

    private function handlePost(UserModel $users): void
    {
        $action = $this->input('action');

        if ($action === 'add') {
            $username = trim($this->input('username', ''));
            $password = $this->input('password', '');
            $fullName = trim($this->input('full_name', ''));
            $role = $this->input('role');

            if ($users->usernameExists($username)) {
                $this->redirect('/dashboard/users?error=' . rawurlencode('Username already exists'));
            }

            $users->createUser($username, $password, $fullName, $role);
            $this->redirect('/dashboard/users?success=' . rawurlencode('User added successfully'));
        } elseif ($action === 'edit') {
            $id = $this->input('id');
            $username = trim($this->input('username', ''));
            $fullName = trim($this->input('full_name', ''));
            $role = $this->input('role');
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            if ($users->usernameExists($username, $id)) {
                $this->redirect('/dashboard/users?error=' . rawurlencode('Username already exists'));
            }

            $users->updateUser($id, $username, $fullName, $role, $isActive);
            $this->redirect('/dashboard/users?success=' . rawurlencode('User updated successfully'));
        } elseif ($action === 'change_password') {
            $id = $this->input('id');
            $password = $this->input('password', '');

            $users->changePassword($id, $password);
            $this->redirect('/dashboard/users?success=' . rawurlencode('Password changed successfully'));
        } elseif ($action === 'delete') {
            $id = $this->input('id');

            // Prevent deleting own account
            if ($id == ($_SESSION['user_id'] ?? null)) {
                $this->redirect('/dashboard/users?error=' . rawurlencode('Cannot delete your own account'));
            }

            $users->delete($id);
            $this->redirect('/dashboard/users?success=' . rawurlencode('User deleted successfully'));
        }
    }
}
