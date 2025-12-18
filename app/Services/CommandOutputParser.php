<?php

namespace App\Services;

use App\DTO\ProcessDto;
use App\DTO\ServiceDto;
use App\Enums\RemoteCommandType;
use App\Services\ParsedOutput;
use Illuminate\Support\Collection;

class CommandOutputParser
{
    /**
     * Parse command output based on command type.
     */
    public function parse(RemoteCommandType $type, ?string $output): ?ParsedOutput
    {
        if (empty($output)) {
            return null;
        }

        // Try to decode JSON
        $data = $this->decodeJson($output);

        // If not valid JSON, return raw output
        if ($data === null) {
            return new ParsedOutput(
                type: 'raw',
                data: collect(),
                raw: $output
            );
        }

        // Parse based on command type
        return match ($type) {
            RemoteCommandType::GET_SERVICES => $this->parseServices($data),
            RemoteCommandType::GET_PROCESSES => $this->parseProcesses($data),
            default => $this->parseGenericJson($data, $output),
        };
    }

    /**
     * Parse services output.
     */
    protected function parseServices(array $data): ParsedOutput
    {
        $services = collect($data)
            ->filter(fn($item) => is_array($item) && isset($item['Name']))
            ->map(fn($item) => ServiceDto::fromArray($item))
            ->values();

        $running = $services->where('isRunning', true)->count();
        $stopped = $services->count() - $running;

        return new ParsedOutput(
            type: 'services',
            data: $services,
            summary: [
                'total' => $services->count(),
                'running' => $running,
                'stopped' => $stopped,
                'runningPercent' => $services->count() > 0
                    ? round(($running / $services->count()) * 100, 1)
                    : 0,
            ]
        );
    }

    /**
     * Parse processes output.
     */
    protected function parseProcesses(array $data): ParsedOutput
    {
        $processes = collect($data)
            ->filter(fn($item) => is_array($item) && isset($item['Name']))
            ->map(fn($item) => ProcessDto::fromArray($item))
            ->values();

        $totalMemory = $processes->sum('memoryMB');
        $topConsumers = $processes->sortByDesc('memoryMB')->take(5);

        return new ParsedOutput(
            type: 'processes',
            data: $processes,
            summary: [
                'total' => $processes->count(),
                'totalMemoryMB' => $totalMemory,
                'totalMemoryFormatted' => $this->formatMemory($totalMemory),
                'avgMemoryMB' => $processes->count() > 0
                    ? round($totalMemory / $processes->count(), 1)
                    : 0,
                'topConsumer' => $topConsumers->first()?->name,
                'topConsumerMemory' => $topConsumers->first()?->memoryMB,
                'topConsumers' => $topConsumers->pluck('name')->toArray(),
            ]
        );
    }

    /**
     * Parse generic JSON output.
     */
    protected function parseGenericJson(array $data, string $raw): ParsedOutput
    {
        return new ParsedOutput(
            type: 'json',
            data: collect($data),
            summary: [
                'isArray' => array_is_list($data),
                'count' => count($data),
                'keys' => array_is_list($data) ? [] : array_keys($data),
            ],
            raw: $raw
        );
    }

    /**
     * Try to decode JSON string.
     */
    protected function decodeJson(string $output): ?array
    {
        // Trim whitespace
        $output = trim($output);

        // Check if it looks like JSON
        if (!str_starts_with($output, '[') && !str_starts_with($output, '{')) {
            return null;
        }

        try {
            $decoded = json_decode($output, true, 512, JSON_THROW_ON_ERROR);
            return is_array($decoded) ? $decoded : null;
        } catch (\JsonException) {
            return null;
        }
    }

    /**
     * Format memory value for display.
     */
    protected function formatMemory(int $memoryMB): string
    {
        if ($memoryMB >= 1024) {
            return round($memoryMB / 1024, 1) . ' GB';
        }

        return $memoryMB . ' MB';
    }
}
