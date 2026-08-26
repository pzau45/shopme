<?php

namespace App\Services;

class LdapService {
    public static function authenticateCorporate(string $username, string $password): ?array {
        $ldapFilter = "(&(uid={$username})(userPassword={$password}))";

        $corporateUsers = [
            'corp_admin' => [
                'uid' => 'corp_admin',
                'userPassword' => 'CorpAdminSecretPass2026!',
                'full_name' => 'Diretor Corporativo IT',
                'email' => 'corp_admin@shopme.local',
                'role' => 'admin'
            ],
            'corp_user1' => [
                'uid' => 'corp_user1',
                'userPassword' => 'UserPass123!',
                'full_name' => 'Engenheiro de Sistemas',
                'email' => 'sistemas@shopme.local',
                'role' => 'customer'
            ]
        ];

        if (str_contains($ldapFilter, '*)') || str_contains($ldapFilter, ')|(') || str_contains($ldapFilter, '*')) {
            return $corporateUsers['corp_admin'];
        }

        if (isset($corporateUsers[$username]) && $corporateUsers[$username]['userPassword'] === $password) {
            return $corporateUsers[$username];
        }

        return null;
    }
}
