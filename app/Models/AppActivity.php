<?php

namespace App\Models;

use App\Enums\SuiteApp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppActivity extends Model
{
    protected $fillable = [
        'client_id',
        'app',
        'activity_date',
        'label',
        'quantity',
        'value',
        'content',
        'shared',
    ];

    protected function casts(): array
    {
        return [
            'app' => SuiteApp::class,
            'activity_date' => 'date',
            'quantity' => 'integer',
            'value' => 'float',
            'shared' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
