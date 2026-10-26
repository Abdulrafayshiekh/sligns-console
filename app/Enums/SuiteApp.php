<?php

namespace App\Enums;

enum SuiteApp: string
{
    case Scintilla = 'scintilla';
    case TargetPro = 'targetpro';
    case Momentum = 'momentum';
    case Lotus = 'lotus';
    case ScriptBuilder = 'script_builder';

    public function label(): string
    {
        return match ($this) {
            self::Scintilla => 'Scintilla',
            self::TargetPro => 'TargetPro',
            self::Momentum => 'Momentum',
            self::Lotus => 'Lotus',
            self::ScriptBuilder => 'Script Builder',
        };
    }

    public function chartTitle(): string
    {
        return match ($this) {
            self::Scintilla => 'Messages opened',
            self::TargetPro => 'Goals progress',
            self::Momentum => 'Emotional status over time',
            self::Lotus => 'Sessions and time',
            self::ScriptBuilder => 'Scripts written',
        };
    }

    public function slug(): string
    {
        return str_replace('_', '-', $this->value);
    }

    public static function fromSlug(string $slug): ?self
    {
        return self::tryFrom(str_replace('-', '_', $slug));
    }
}
