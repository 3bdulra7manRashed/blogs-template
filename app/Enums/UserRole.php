<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case EDITOR = 'editor';

    public static function labels(): array
    {
        return [
            self::ADMIN->value => 'Administrator',
            self::EDITOR->value => 'Editor',
        ];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

