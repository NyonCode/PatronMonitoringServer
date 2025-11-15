<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentUserSession extends Model
{
    protected $fillable = [
        'agent_id',
        'session_user',
        'session_start',
        'mapped_drivers',
        'accessible_paths',
    ];

    protected $casts = [
        'session_user' => 'string',
        'session_start' => 'datetime',
        'mapped_drivers' => 'array',
        'accessible_paths' => 'array',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
