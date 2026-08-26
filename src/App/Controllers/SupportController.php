<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Models\Ticket;

class SupportController {
    public function index(): void {
        $user = AuthMiddleware::check();
        $tickets = Ticket::findByUserId($user['id']);
        require_read_view('views/support/index.php', ['tickets' => $tickets]);
    }

    public function detail(string $id): void {
        $user = AuthMiddleware::check();
        $ticket = Ticket::findById((int)$id);

        if (!$ticket) {
            http_response_code(404);
            echo "<h1>404 Ticket Not Found</h1>";
            return;
        }

        require_read_view('views/support/detail.php', ['ticket' => $ticket]);
    }

    public function create(): void {
        $user = AuthMiddleware::check();
        $subject = $_POST['subject'] ?? 'Suporte Técnico';
        $message = $_POST['message'] ?? '';

        if (!empty($message)) {
            $ticketId = Ticket::create($user['id'], $subject, $message);
            header("Location: /support/" . $ticketId);
            exit;
        }

        header("Location: /support?error=Mensagem+vazia");
        exit;
    }

    public function reply(): void {
        $user = AuthMiddleware::check();
        $ticketId = (int)($_POST['ticket_id'] ?? 0);
        $message  = $_POST['message'] ?? '';

        if ($ticketId > 0 && !empty($message)) {
            Ticket::addMessage($ticketId, $user['id'], $message);
        }

        header("Location: /support/" . $ticketId);
        exit;
    }
}
