<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

class Reassignment extends Model
{
    public const PENDING = 'pending';

    public const GIVEN = 'given';

    public const COMPLETED = 'completed';

    protected $fillable = [
        'company_id',
        'client_id',
        'from_coach_id',
        'to_coach_id',
        'status',
        'token',
        'consented_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'consented_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function fromCoach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_coach_id')->withTrashed();
    }

    public function toCoach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_coach_id');
    }

    public function consentUrl(): string
    {
        return URL::temporarySignedRoute('consent.show', now()->addDays(14), ['reassignment' => $this->token]);
    }
}
