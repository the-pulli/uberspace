<?php

namespace App\Enums;

enum MessageType
{
    case Success;
    case Error;
    case Warning;

    public function css(): string
    {
        return match ($this) {
            self::Success => 'bg-green-600',
            self::Error => 'bg-red-600',
            self::Warning => 'bg-yellow-600 text-gray-900',
        };
    }
}
