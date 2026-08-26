<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Models\User;

class ProfileController {
    public function index(): void {
        $user = AuthMiddleware::check();

        $freshUser = User::findById($user['id']);

        $preferences = [
            'theme' => 'dark',
            'currency' => 'EUR',
            'report_format' => 'pdf',
        ];
        if (isset($_COOKIE['shopme_prefs'])) {
            $rawPrefs = base64_decode($_COOKIE['shopme_prefs']);
            $decoded = json_decode($rawPrefs, true);
            if (is_array($decoded)) {
                $preferences = array_merge($preferences, $decoded);
            }
        }

        require_read_view('views/profile/index.php', [
            'user' => $freshUser,
            'preferences' => $preferences
        ]);
    }

    public function updateProfile(): void {
        $user = AuthMiddleware::check();

        User::updateMassAssignment($user['id'], $_POST);

        $updatedUser = User::findById($user['id']);
        $_SESSION['user'] = $updatedUser;

        header("Location: /profile?msg=Perfil+atualizado+com+sucesso");
        exit;
    }

    public function uploadAvatar(): void {
        $user = AuthMiddleware::check();

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $targetDir = __DIR__ . '/../../../public/uploads/avatars/';

            // Sanitize: keep only extension, use user id + timestamp as filename
            $originalName = $_FILES['avatar']['name'];
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($ext, $allowedExtensions, true)) {
                header("Location: /profile?error=Formato+de+imagem+inválido.+Use+JPG,+PNG,+GIF+ou+WEBP.");
                exit;
            }

            $fileName = 'avatar_' . $user['id'] . '_' . time() . '.' . $ext;
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
                User::updateMassAssignment($user['id'], ['avatar' => $fileName]);
                $_SESSION['user']['avatar'] = $fileName;
                header("Location: /profile?msg=Avatar+atualizado+com+sucesso");
                exit;
            }
        }

        header("Location: /profile?error=Erro+ao+carregar+avatar");
        exit;
    }

    public function savePreferences(): void {
        $allowedThemes = ['dark', 'light', 'system'];
        $allowedCurrencies = ['EUR', 'USD', 'GBP'];
        $allowedFormats = ['pdf', 'csv', 'json'];

        $theme = $_POST['theme'] ?? 'dark';
        $currency = $_POST['currency'] ?? 'EUR';
        $reportFormat = $_POST['report_format'] ?? 'pdf';

        if (!in_array($theme, $allowedThemes, true)) {
            $theme = 'dark';
        }
        if (!in_array($currency, $allowedCurrencies, true)) {
            $currency = 'EUR';
        }
        if (!in_array($reportFormat, $allowedFormats, true)) {
            $reportFormat = 'pdf';
        }

        $prefs = [
            'theme' => $theme,
            'currency' => $currency,
            'report_format' => $reportFormat,
        ];

        $encoded = base64_encode(json_encode($prefs));
        setcookie('shopme_prefs', $encoded, time() + 86400, '/');

        header("Location: /profile?msg=Preferências+guardadas+com+sucesso");
        exit;
    }
}
