<?php

namespace App\Models;

use App\Enums\ActivityStatus;
use App\Enums\Role;
use App\Enums\Theme;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'role',
        'company_id',
        'coach_id',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'country',
        'language',
        'birthday',
        'title',
        'marital_status',
        'age',
        'occupation',
        'industry',
        'coach_viewed_at',
        'theme',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'role' => Role::class,
            'password' => 'hashed',
            'birthday' => 'date',
            'age' => 'integer',
            'coach_viewed_at' => 'datetime',
            'theme' => Theme::class,
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(User::class, 'coach_id')->where('role', Role::Client);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(AppActivity::class, 'client_id');
    }

    public function authoredNotes(): HasMany
    {
        return $this->hasMany(Note::class, 'coach_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'coach_id');
    }

    public function scopeRole(Builder $query, Role $role): Builder
    {
        return $query->where('role', $role);
    }

    public function hasRole(Role $role): bool
    {
        return $this->role === $role;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(Role::SuperAdmin);
    }

    public function isCompany(): bool
    {
        return $this->hasRole(Role::Company);
    }

    public function isCoach(): bool
    {
        return $this->hasRole(Role::Coach);
    }

    public function isClient(): bool
    {
        return $this->hasRole(Role::Client);
    }

    public function homeRoute(): string
    {
        return match ($this->role) {
            Role::SuperAdmin => route('deployer.home'),
            Role::Company => route('company.dashboard'),
            Role::Coach => route('coach.clients.index'),
            Role::Client => route('sign-in'),
        };
    }

    public function daysSinceActive(): ?int
    {
        $last = $this->last_active_on ?? $this->activities()->max('activity_date');

        return $last ? (int) Carbon::parse($last)->startOfDay()->diffInDays(now()->startOfDay()) : null;
    }

    public function activityStatus(): ActivityStatus
    {
        return ActivityStatus::fromDays($this->daysSinceActive());
    }
}
