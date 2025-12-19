<?php

namespace App\Models;

use App\Enums\RemoteCommandStatus;
use App\Enums\RemoteCommandType;
use App\Services\CommandOutputParser;
use App\Services\ParsedOutput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RemoteCommand extends Model
{
    protected $fillable = [
        'agent_id', 'type', 'command', 'url', 'status',
        'output', 'error', 'exit_code',
        'sent_at', 'started_at', 'completed_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => RemoteCommandType::class,
            'status' => RemoteCommandStatus::class,
            'sent_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', RemoteCommandStatus::PENDING);
    }

    public function scopeForAgent(Builder $query, Agent|int $agent): Builder
    {
        return $query->where('agent_id', $agent instanceof Agent ? $agent->id : $agent);
    }

    public function scopeNotFinished(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            RemoteCommandStatus::COMPLETED,
            RemoteCommandStatus::FAILED,
            RemoteCommandStatus::CANCELLED,
        ]);
    }

    public function markAsSent(): self
    {
        $this->update(['status' => RemoteCommandStatus::SENT, 'sent_at' => now()]);

        return $this;
    }

    public function markAsCompleted(?string $output = null, ?int $exitCode = null): self
    {
        $this->update([
            'status' => RemoteCommandStatus::COMPLETED,
            'output' => $output,
            'exit_code' => $exitCode,
            'completed_at' => now(),
        ]);

        return $this;
    }

    public function markAsFailed(?string $error = null, ?int $exitCode = null): self
    {
        $this->update([
            'status' => RemoteCommandStatus::FAILED,
            'error' => $error,
            'exit_code' => $exitCode,
            'completed_at' => now(),
        ]);

        return $this;
    }

    public function toApiFormat(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'command' => $this->command,
            'url' => $this->url,
        ];
    }

    /**
     * Determine if the command can be cancelled.
     *
     * A command can be cancelled only if it's in PENDING or SENT status.
     *
     * @return bool True if the command can be cancelled, false otherwise.
     */
    public function canBeCancelled(): bool
    {
        return $this->status === RemoteCommandStatus::PENDING || $this->status === RemoteCommandStatus::SENT;
    }

    /**
     * Get parsed output using CommandOutputParser.
     */
    public function getParsedOutputAttribute(): ?ParsedOutput
    {
        if (empty($this->output)) {
            return null;
        }

        return app(CommandOutputParser::class)->parse($this->type, $this->output);
    }

    /**
     * Check if command has parseable output.
     */
    public function hasParsedOutput(): bool
    {
        return $this->parsed_output !== null && $this->parsed_output->isNotEmpty();
    }

    /**
     * Check if output is structured (services/processes).
     */
    public function hasStructuredOutput(): bool
    {
        $parsed = $this->parsed_output;

        return $parsed !== null && ($parsed->isServices() || $parsed->isProcesses());
    }
}
