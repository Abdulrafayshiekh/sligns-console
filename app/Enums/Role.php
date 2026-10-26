<?php

namespace App\Enums;

enum Role: string
{
    case SuperAdmin = 'super_admin';
    case Company = 'company';
    case Coach = 'coach';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Company => 'Coaching Company',
            self::Coach => 'Coach',
            self::Client => 'Client',
        };
    }
}
