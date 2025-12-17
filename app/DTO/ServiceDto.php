<?php

namespace App\DTO;

final readonly class ServiceDto
{
    public function __construct(
        public string $name,
        public string $displayName,
        public string $status,
        public bool $isRunning,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['Name'] ?? '',
            displayName: $data['DisplayName'] ?? '',
            status: $data['Status'] ?? 'Unknown',
            isRunning: ($data['Status'] ?? '') === 'Running',
        );
    }
}
