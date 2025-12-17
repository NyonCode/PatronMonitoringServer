<?php

namespace App\Services;

use App\DTO\ProcessDto;
use App\DTO\ServiceDto;
use App\Enums\RemoteCommandType;
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

        $data = $this->decodeJson($output);

        if ($data === null) {
            return new ParsedOutput(
                type: 'raw',
                data: collect(),
                raw: $output
            );
        }

        return match ($type) {
            RemoteCommandType::GET_SERVICES => $this->parseServices($data),
            RemoteCommandType::GET_PROCESSES => $this->parseProcesses($data),
            default => new ParsedOutput(
                type: 'json',
                data: collect($data),
                raw: $output
            ),
        };
    }

    private function parseServices(array $data): ParsedOutput
    {
        $services = collect($data)->map(fn($item) => ServiceDto::fromArray($item));

        return new ParsedOutput(
            type: 'services',
            data: $services,
            summary: [
                'total' => $services->count(),
                'running' => $services->where('isRunning', true)->count(),
                'stopped' => $services->where('isRunning', false)->count(),
            ]
        );
    }

    private function parseProcesses(array $data): ParsedOutput
    {
        $processes = collect($data)->map(fn($item) => ProcessDto::fromArray($item));

        return new ParsedOutput(
            type: 'processes',
            data: $processes,
            summary: [
                'total' => $processes->count(),
                'totalMemoryMB' => $processes->sum('memoryMB'),
                'topConsumer' => $processes->sortByDesc('memoryMB')->first()?->name,
            ]
        );
    }

    private function decodeJson(string $output): ?array
    {
        try {
            $decoded = json_decode($output, true, flags: JSON_THROW_ON_ERROR);
            return is_array($decoded) ? $decoded : null;
        } catch (\JsonException) {
            return null;
        }
    }
}
