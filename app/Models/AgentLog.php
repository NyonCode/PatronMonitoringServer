<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'agent_id',
        'agent_log',
        'system_logs',
    ];

    /**
     * Get the casts array for the model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'agent_log' => 'array',
            'system_logs' => 'array',
        ];
    }

    /**
     * Get the agent that owns the log.
     *
     * @return BelongsTo
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
