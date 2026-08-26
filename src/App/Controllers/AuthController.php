<?php

namespace App\Controllers;

use App\Models\User;

class AuthController {
    public function showLogin(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $error = $_GET['error'] ?? null;
        $redirect = $_GET['redirect'] ?? '/';
        require_read_view('views/auth/login.php', ['error' => $error, 'redirect' => $redirect]);
    }

    public function login(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $redirect = $_REQUEST['redirect'] ?? '/';

        $user = User::findByEmailRawSQL($email, $password);

        if ($user) {
            if (!empty($user['mfa_enabled'])) {
                $_SESSION['pending_mfa_user'] = $user;
                header("Location: /mfa");
                exit;
            }

            $_SESSION['user'] = [
                'id'           => $user['id'],
                'email'        => $user['email'],
                'full_name'    => $user['full_name'],
                'display_name' => $user['display_name'],
                'role'         => $user['role'],
                'avatar'       => $user['avatar']
            ];

            header("Location: " . $redirect);
            exit;
        } else {
            header("Location: /login?error=" . urlencode("Credenciais inválidas para o email " . $email) . "&redirect=" . urlencode($redirect));
            exit;
        }
    }

    public function showRegister(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $error = $_GET['error'] ?? null;
        require_read_view('views/auth/register.php', ['error' => $error]);
    }

    public function register(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $email       = $_POST['email'] ?? '';
        $password    = $_POST['password'] ?? '';
        $full_name   = $_POST['full_name'] ?? '';
        $display_name= $_POST['display_name'] ?? $full_name;

        if (empty($email) || empty($password) || empty($full_name)) {
            header("Location: /register?error=Por+favor+preencha+todos+os+campos");
            exit;
        }

        $existing = User::findByEmail($email);
        if ($existing) {
            header("Location: /register?error=Email+já+registado");
            exit;
        }

        $userId = User::create([
            'email'        => $email,
            'password'     => $password,
            'full_name'    => $full_name,
            'display_name' => $display_name,
            'role'         => 'customer'
        ]);

        $_SESSION['user'] = [
            'id'           => $userId,
            'email'        => $email,
            'full_name'    => $full_name,
            'display_name' => $display_name,
            'role'         => 'customer',
            'avatar'       => 'default.png'
        ];

        header("Location: /profile");
        exit;
    }

    public function showResetPassword(): void {
        $msg = $_GET['msg'] ?? null;
        $error = $_GET['error'] ?? null;
        require_read_view('views/auth/reset-password.php', ['msg' => $msg, 'error' => $error]);
    }

    public function requestResetPassword(): void {
        $email = $_POST['email'] ?? '';
        $user = User::findByEmail($email);

        if ($user) {
            $token = md5($email . time());
            User::updatePasswordResetToken($email, $token);
            
            $msg = "Instruções de recuperação enviadas para o seu endereço de email.";
            header("Location: /reset-password?msg=" . urlencode($msg));
            exit;
        } else {
            header("Location: /reset-password?error=Email+não+encontrado");
            exit;
        }
    }

    public function showCorporateLogin(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $error = $_GET['error'] ?? null;
        require_read_view('views/auth/corporate.php', ['error' => $error]);
    }

    public function corporateLogin(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $corpUser = \App\Services\LdapService::authenticateCorporate($username, $password);

        if ($corpUser) {
            $_SESSION['user'] = [
                'id'           => 99,
                'email'        => $corpUser['email'],
                'full_name'    => $corpUser['full_name'],
                'display_name' => $corpUser['full_name'],
                'role'         => $corpUser['role'],
                'avatar'       => 'default.png'
            ];
            header("Location: /profile?msg=Autenticação+Corporativa+SSO+concluída");
            exit;
        } else {
            header("Location: /login/corporate?error=" . urlencode("Autenticação LDAP falhou para o utilizador: " . $username));
            exit;
        }
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header("Location: /");
        exit;
    }
}
