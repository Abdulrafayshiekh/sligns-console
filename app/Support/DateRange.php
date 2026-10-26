<?php

namespace App\Support;

use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Throwable;

class DateRange
{
    public const PRESETS = [7, 15, 30, 90];

    public function __construct(public Carbon $from, public Carbon $to, public ?int $preset = null) {}

    public static function fromRequest(Request $request): self
    {
        $preset = (int) $request->query('range');

        if (in_array($preset, self::PRESETS, true)) {
            return self::lastDays($preset);
        }

        if ($request->filled(['from', 'to'])) {
            try {
                $from = Carbon::parse($request->query('from'))->startOfDay();
                $to = Carbon::parse($request->query('to'))->startOfDay();

                if ($from->lte($to) && $from->diffInDays($to) <= 366) {
                    return new self($from, $to);
                }
            } catch (Throwable) {
            }
        }

        return self::lastDays(15);
    }

    public static function lastDays(int $days): self
    {
        return new self(today()->subDays($days - 1), today(), $days);
    }

    public function days(): CarbonPeriod
    {
        return CarbonPeriod::create($this->from, $this->to);
    }

    public function query(): array
    {
        return $this->preset
            ? ['range' => $this->preset]
            : ['from' => $this->from->toDateString(), 'to' => $this->to->toDateString()];
    }

    public function label(): string
    {
        return $this->preset
            ? __('Past :days days', ['days' => $this->preset])
            : $this->from->translatedFormat('j M Y').' – '.$this->to->translatedFormat('j M Y');
    }
}
