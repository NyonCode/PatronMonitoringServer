<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Agent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'hostname',
        'ip_address',
        'pretty_name',
        'last_seen_at',
        'token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
        ];
    }

    /**
     * Define a one-to-one relationship with the AgentLog model.
     */
    public function log(): HasOne
    {
        return $this->hasOne(AgentLog::class);
    }

    /**
     * Define a one-to-one relationship with the AgentNetworkInfo model.
     */
    public function network(): HasOne
    {
        return $this->hasOne(AgentNetworkInfo::class);
    }

    /**
     * Define a one-to-many relationship with the AgentDisk model.
     */
    public function disk(): HasMany
    {
        return $this->hasMany(AgentDisk::class);
    }

    /**
     * Define a one-to-many relationship with the AgentSystemMetric model.
     */
    public function metrics(): HasMany
    {
        return $this->hasMany(AgentSystemMetric::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(AgentUserSession::class);
    }
}
