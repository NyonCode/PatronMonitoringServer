<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentMetricDaily extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'agent_id',
        'date',
        'cpu_avg',
        'cpu_min',
        'cpu_max',
        'ram_avg',
        'ram_min',
        'ram_max',
        'gpu_avg',
        'gpu_min',
        'gpu_max',
        'sample_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var list<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'cpu_avg' => 'decimal:2',
        'cpu_min' => 'decimal:2',
        'cpu_max' => 'decimal:2',
        'ram_avg' => 'decimal:2',
        'ram_min' => 'decimal:2',
        'ram_max' => 'decimal:2',
        'gpu_avg' => 'decimal:2',
        'gpu_min' => 'decimal:2',
        'gpu_max' => 'decimal:2',
    ];


    /**
     * Get the agent that owns the metric.
     *
     * @return BelongsTo
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
