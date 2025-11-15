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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'agent_log' => 'array',
        'system_logs' => 'array',
    ];

    /**
     * Return formatted system log message
     */
    public function getFormattedSystemMessage(string $message): string
    {
        // zachytí text mezi apostrofy
        preg_match_all("/'([^']+)'/", $message, $matches);

        $labels = [
            'Výchozí pro počítač',
            'Oprávnění',
            'Operace',
            'CLSID',
            'APPID',
            'Počítač',
            'Uživatel',
            'SID',
            'Místo volání',
            'Aplikace',
            'Aplikační SID',
        ];

        $details = collect($matches[1] ?? [])
            ->map(function ($value, $index) use ($labels) {
                $label = $labels[$index] ?? "Parametr $index";

                return "<div><strong>{$label}:</strong> {$value}</div>";
            })
            ->implode('');

        // nahradí původní text prvním řádkem + oddělí detaily
        $header = strtok($message, "\n");

        return "<p>{$header}</p><hr>{$details}";
    }

    /**
     * Get the agent that owns the log.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
