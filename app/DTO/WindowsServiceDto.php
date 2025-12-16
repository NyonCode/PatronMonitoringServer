<?php

namespace App\DTO;

final class WindowsServiceDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $displayName,
        public readonly string $status,
        public readonly bool $isRunning,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) ($data['Name'] ?? ''),
            displayName: (string) ($data['DisplayName'] ?? ''),
            status: (string) ($data['Status'] ?? 'Unknown'),
            isRunning: ($data['Status'] ?? '') === 'Running',
        );
    }
}
