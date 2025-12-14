<?php

namespace App\Services;

use App\Enums\RemoteCommandStatus;
use App\Enums\RemoteCommandType;
use App\Models\Agent;
use App\Models\TerminalLog;

class TerminalPollingService
{
    public function getHistory(Agent $agent, string $sessionId, int $limit = 100): array
    {
        $session = $agent->terminalSessions()->where('id', $sessionId)->first();

        if (!$session) {
            return ['status' => 'error', 'error' => 'Session not found'];
        }

        $logs = $session->logs()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();

        return [
            'status' => 'ok',
            'session' => $session->toApiFormat(),
            'logs' => $logs->map(fn(TerminalLog $log) => [
                'direction' => $log->direction->value,
                'content' => $log->content,
                'timestamp' => $log->created_at->toIso8601String(),
            ]),
        ];
    }
}
