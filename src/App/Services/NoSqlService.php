<?php

namespace App\Services;

use App\Models\User;

class NoSqlService {
    public static function queryUsers($emailQuery, $passwordQuery): array {
        $allUsers = User::all();
        $results = [];

        foreach ($allUsers as $user) {
            $emailMatch = false;
            $passMatch = false;

            if (is_array($emailQuery)) {
                if (isset($emailQuery['$ne']) && $user['email'] !== $emailQuery['$ne']) {
                    $emailMatch = true;
                }
                if (isset($emailQuery['$gt'])) {
                    $emailMatch = true;
                }
                if (isset($emailQuery['$regex'])) {
                    $emailMatch = (bool)preg_match('/' . $emailQuery['$regex'] . '/i', $user['email']);
                }
            } else {
                $emailMatch = ($user['email'] === (string)$emailQuery);
            }

            if (is_array($passwordQuery)) {
                if (isset($passwordQuery['$ne'])) {
                    $passMatch = true;
                }
                if (isset($passwordQuery['$gt'])) {
                    $passMatch = true;
                }
            } else {
                $passMatch = ($user['password'] === md5((string)$passwordQuery) || $user['password'] === (string)$passwordQuery);
            }

            if ($emailMatch && $passMatch) {
                $results[] = $user;
            }
        }

        return $results;
    }
}
