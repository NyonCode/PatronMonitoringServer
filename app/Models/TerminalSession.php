<?php

namespace App\Models;

use App\Enums\TerminalSessionStatus;
use App\Enums\TerminalType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TerminalSession extends Model
{
    // ODSTRANĚNO: use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', // ← Přidáno do fillable!
        'agent_id', 'type', 'user_session_id', 'status',
        'started_at', 'closed_at', 'created_by',
    ];

    /**
     * Get the casts property.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'type' => TerminalType::class,
            'status' => TerminalSessionStatus::class,
            'started_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     *
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TerminalLog::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', TerminalSessionStatus::RUNNING);
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function close(): self
    {
        $this->update(['status' => TerminalSessionStatus::CLOSED, 'closed_at' => now()]);
        return $this;
    }

    public function logInput(string $content): TerminalLog
    {
        return $this->logs()->create(['direction' => 'input', 'content' => $content]);
    }

    public function logOutput(string $content): TerminalLog
    {
        return $this->logs()->create(['direction' => 'output', 'content' => $content]);
    }

    public function toApiFormat(): array
    {
        return [
            'session_id' => $this->id,
            'type' => $this->type->value,
            'user_session_id' => $this->user_session_id,
            'status' => $this->status->value,
            'started_at' => $this->started_at?->toIso8601String(),
        ];
    }
}
