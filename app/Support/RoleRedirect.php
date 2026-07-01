<?php

namespace App\Support;

class RoleRedirect
{
    public static function path(string $role): string
    {
        return match ($role) {
            'admin'      => '/admin/dashboard',
            'branch_mgr' => '/branch/dashboard',
            'rider'      => '/rider/dashboard',
            default      => '/customer/dashboard',
        };
    }
}
