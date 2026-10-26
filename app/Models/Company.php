<?php

namespace App\Models;

use App\Enums\Role;
use App\Support\Options;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory;

    public const ACTIVE = 'active';

    public const SUSPENDED = 'suspended';

    protected $fillable = [
        'name',
        'slug',
        'console_language',
        'interface_color',
        'logo_path',
        'contact_person',
        'preferred_language',
        'about',
        'addresses',
        'phones',
        'emails',
        'city',
        'country',
        'time_zone',
        'text_opt_in',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'addresses' => 'array',
            'phones' => 'array',
            'emails' => 'array',
            'text_opt_in' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function admins(): HasMany
    {
        return $this->users()->where('role', Role::Company);
    }

    public function coaches(): HasMany
    {
        return $this->users()->where('role', Role::Coach);
    }

    public function clients(): HasMany
    {
        return $this->users()->where('role', Role::Client);
    }

    public function scopeListing(Builder $query, ?string $search = null): Builder
    {
        return $query
            ->withCount(['coaches', 'clients'])
            ->when($search, fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->orderBy('name');
    }

    public function isActive(): bool
    {
        return $this->status === self::ACTIVE;
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    public function languageName(): string
    {
        return Options::languages()[$this->console_language] ?? $this->console_language;
    }

    public function isRtl(): bool
    {
        return in_array($this->console_language, Options::RTL_LANGUAGES, true);
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'company';
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
