<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard/index.php');
        }

        $error_message = '';

        if ($this->isPost()) {
            $username = trim((string) $this->input('username', ''));
            $password = (string) $this->input('password', '');

            if ($username === '' || $password === '') {
                $error_message = 'Please fill in all fields.';
            } else {
                $result = Auth::attempt($username, $password);

                if ($result === true) {
                    $this->redirect('/dashboard/index.php');
                } elseif ($result === 'inactive') {
                    $error_message = 'Your account has been deactivated. Please contact administrator.';
                } else {
                    $error_message = 'Invalid username or password.';
                }
            }
        }

        $this->renderBare('auth/login', ['error_message' => $error_message]);
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login.php');
    }
}
