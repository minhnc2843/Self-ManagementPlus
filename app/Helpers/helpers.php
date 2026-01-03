<?php

use App\Settings\GeneralSettings;

function getSettings($key)
{
    try {
        return app(GeneralSettings::class)->$key ?? null;
    } catch (\Exception $e) {
        // Nếu lỗi (do chưa migrate, chưa setup settings), trả về null để không sập web
        return null; 
    }
}

function getSelected(): string
{
    if (request()->routeIs('users.*')) {
        return 'tab_two';
    } elseif (request()->routeIs('permissions.*')) {
        return 'tab_three';
    } elseif (request()->routeIs('roles.*')) {
        return 'tab_three';
    } elseif (request()->routeIs('database-backups.*')) {
        return 'tab_four';
    } elseif (request()->routeIs('general-settings.*')) {
        return 'tab_five';
    } elseif (request()->routeIs('dashboards.*')) {
        return 'tab_one';
    } else {
        return 'tab_one';
    }
}