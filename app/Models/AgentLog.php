<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class AgentLog extends Model
{
    protected $fillable = [
        'agent_id',
        'agent_log',
        'system_logs',
    ];

    // JSON dekódujeme ručně – ne pomocí castu 'array'
    protected $casts = [];

    /**
     * Vrátí system_logs jako kolekci objektů.
     */
    public function getSystemLogsAttribute($value): Collection
    {
        $logs = collect(json_decode($value, false));

        return $logs->map(function ($log) {
            $log->formatted_message = $this->getFormattedSystemMessage($log->Message ?? '');
            return $log;
        });
    }

    /**
     * Vrátí agent_log jako kolekci objektů.
     */
    public function getAgentLogAttribute($value): Collection
    {
        $logs = collect(json_decode($value, false));

        return $logs->map(function ($log) {
            $log->Time = $log->Time ?? now()->toISOString();
            $log->EntryType = $log->EntryType ?? 'Info';
            $log->Message = $log->Message ?? '';
            return $log;
        });
    }

    /**
     * Formátuje text systémového logu.
     */
    public function getFormattedSystemMessage(string $message): string
    {
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

        $header = strtok($message, "\n");
        return "<p>{$header}</p><hr>{$details}";
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
