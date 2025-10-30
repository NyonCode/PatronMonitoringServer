<?php

namespace App\Http\Resources;

use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Agent */
class AgentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'hostname' => $this->hostname,
            'ip_address' => $this->ip_address,
            'pretty_name' => $this->pretty_name,
            'update_interval' => $this->update_interval,
            'last_seen_at' => $this->last_seen_at,
            'token' => $this->token,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
