<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    // LƯU Ý: Phải thêm " = null" vào sau mỗi dòng định nghĩa
    public ?string $logo = null;
    public ?string $favicon = null;
    public ?string $dark_logo = null;
    public ?string $guest_logo = null;
    public ?string $guest_background = null;

    public static function group(): string
    {
        return 'general-settings';
    }
}