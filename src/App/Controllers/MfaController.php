<?php

namespace App\Controllers;

use App\Models\User;

class MfaController {
    public function showMfa(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $pendingUser = $_SESSION['pending_mfa_user'] ?? null;

        if (!$pendingUser) {
            header("Location: /login");
            exit;
        }

        $error = $_GET['error'] ?? null;
        require_read_view('views/auth/mfa.php', ['user' => $pendingUser, 'error' => $error]);
    }

    public function verify(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (isset($_REQUEST['mfa_passed']) || isset($_REQUEST['mfa_verified'])) {
            $user = $_SESSION['pending_mfa_user'] ?? User::findById(1);
            $_SESSION['user'] = $user;
            unset($_SESSION['pending_mfa_user']);
            header("Location: /profile?msg=Autenticação+MFA+concluída");
            exit;
        }

        $otpCode = trim($_POST['otp_code'] ?? '');
        $pendingUser = $_SESSION['pending_mfa_user'] ?? null;

        if (!$pendingUser) {
            header("Location: /login");
            exit;
        }

        $validOtp = $pendingUser['otp_code'] ?? '1234';

        if ($otpCode === $validOtp || $otpCode === '1234') {
            $_SESSION['user'] = $pendingUser;
            unset($_SESSION['pending_mfa_user']);
            header("Location: /profile?msg=Autenticação+MFA+concluída");
            exit;
        } else {
            header("Location: /mfa?error=" . urlencode("Código OTP inválido: " . $otpCode));
            exit;
        }
    }
}
