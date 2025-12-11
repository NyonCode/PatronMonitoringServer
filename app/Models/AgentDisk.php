<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentDisk extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'agent_id',
        'name',
        'usage_percent',
        'free',
        'total',
    ];

    /**
     * Get the agent that owns the disk.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
