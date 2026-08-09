<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\UserModel;
use PDOException;

class ProfileController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();

        $users = new UserModel();
        $currentUserId = Auth::user()['id'];

        if ($this->isPost()) {
            $this->handlePost($users, $currentUserId);
        }

        try {
            $userData = $users->find($currentUserId);
        } catch (PDOException $e) {
            $userData = null;
            error_log('Profile query error: ' . $e->getMessage());
        }

        $this->render('profile/index', [
            'page_title' => 'My Profile',
            'user_data' => $userData ?? [],
        ]);
    }

    private function handlePost(UserModel $users, $currentUserId): void
    {
        $action = $this->input('action');

        if ($action === 'update_profile') {
            $fullName = trim($this->input('full_name', ''));
            $username = trim($this->input('username', ''));

            if ($fullName === '' || $username === '') {
                $this->redirect('/dashboard/profile?error=' . rawurlencode('Full name and username are required'));
            }

            try {
                if ($users->usernameExists($username, $currentUserId)) {
                    $this->redirect('/dashboard/profile?error=' . rawurlencode('Username already taken by another user'));
                }

                if ($users->update($currentUserId, ['full_name' => $fullName, 'username' => $username])) {
                    $_SESSION['full_name'] = $fullName;
                    $_SESSION['username'] = $username;
                    $this->redirect('/dashboard/profile?success=' . rawurlencode('Profile updated successfully'));
                } else {
                    $this->redirect('/dashboard/profile?error=' . rawurlencode('Failed to update profile'));
                }
            } catch (PDOException $e) {
                $this->redirect('/dashboard/profile?error=' . rawurlencode('Error: ' . $e->getMessage()));
            }
        } elseif ($action === 'change_password') {
            $currentPassword = $this->input('current_password', '');
            $newPassword = $this->input('new_password', '');
            $confirmPassword = $this->input('confirm_password', '');

            if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
                $this->redirect('/dashboard/profile?error=' . rawurlencode('All password fields are required'));
            }

            if ($newPassword !== $confirmPassword) {
                $this->redirect('/dashboard/profile?error=' . rawurlencode('New passwords do not match'));
            }

            if (strlen($newPassword) < 6) {
                $this->redirect('/dashboard/profile?error=' . rawurlencode('New password must be at least 6 characters long'));
            }

            try {
                $user = $users->find($currentUserId);

                if (!$user || !password_verify($currentPassword, $user['password'])) {
                    $this->redirect('/dashboard/profile?error=' . rawurlencode('Current password is incorrect'));
                }

                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

                if ($users->update($currentUserId, ['password' => $newPasswordHash])) {
                    $this->redirect('/dashboard/profile?success=' . rawurlencode('Password changed successfully'));
                } else {
                    $this->redirect('/dashboard/profile?error=' . rawurlencode('Failed to change password'));
                }
            } catch (PDOException $e) {
                $this->redirect('/dashboard/profile?error=' . rawurlencode('Error: ' . $e->getMessage()));
            }
        }
    }
}
