<?php

namespace App\Enums;

enum ActivityStatus: string
{
    case Active = 'active';
    case Idle = 'idle';
    case Inactive = 'inactive';

    public static function fromDays(?int $days): self
    {
        return match (true) {
            $days === null, $days >= 14 => self::Inactive,
            $days >= 7 => self::Idle,
            default => self::Active,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Idle => '7+ days',
            self::Inactive => '14+ days',
        };
    }
}
