<?php

namespace App\Support;

use App\Models\User;

final class ApiCapabilities
{
    /** @return list<string> */
    public static function for(User $user): array
    {
        $common = [
            'dashboard.view',
            'rpp.view',
            'rpp.create',
            'rpp.delete',
            'ljk.view',
            'ljk.manage',
            'ljk.correct',
            'profile.manage',
            'settings.view',
        ];

        if (! $user->isAdmin()) {
            return $common;
        }

        return [
            ...$common,
            'settings.manage',
            'admin.users.manage',
            'admin.guru.manage',
            'admin.rpp.view',
        ];
    }
}
