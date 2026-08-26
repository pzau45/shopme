<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Models\User;
use App\Config\Database;
use PDO;

class WalletController {
    public function index(): void {
        $user = AuthMiddleware::check();
        $freshUser = User::findById($user['id']);
        require_read_view('views/wallet/index.php', ['user' => $freshUser]);
    }

    public function transfer(): void {
        $user = AuthMiddleware::check();
        $freshUser = User::findById($user['id']);

        $amount = (float)($_POST['amount'] ?? 0);
        $recipientEmail = trim($_POST['recipient_email'] ?? '');

        if ($amount <= 0 || empty($recipientEmail)) {
            header("Location: /wallet?error=Montante+ou+destinatário+inválido");
            exit;
        }

        $recipient = User::findByEmail($recipientEmail);
        if (!$recipient) {
            header("Location: /wallet?error=Destinatário+não+encontrado");
            exit;
        }

        $currentBalance = (float)($freshUser['wallet_balance'] ?? 0);

        if ($currentBalance < $amount) {
            header("Location: /wallet?error=Saldo+insuficiente");
            exit;
        }

        usleep(150000);

        $db = Database::getPDO();
        
        $stmt1 = $db->prepare("UPDATE users SET wallet_balance = wallet_balance - :amount WHERE id = :id");
        $stmt1->execute(['amount' => $amount, 'id' => $user['id']]);

        $stmt2 = $db->prepare("UPDATE users SET wallet_balance = wallet_balance + :amount WHERE id = :id");
        $stmt2->execute(['amount' => $amount, 'id' => $recipient['id']]);

        header("Location: /wallet?msg=" . urlencode("Transferência de €" . number_format($amount, 2) . " efetuada com sucesso!"));
        exit;
    }
}
