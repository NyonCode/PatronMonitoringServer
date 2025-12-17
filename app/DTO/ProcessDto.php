<?php

namespace App\DTO;

final readonly class ProcessDto
{
    public function __construct(
        public string $name,
        public int $pid,
        public int $memoryMB,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['Name'] ?? '',
            pid: (int) ($data['PID'] ?? 0),
            memoryMB: (int) ($data['MemoryMB'] ?? 0),
        );
    }
}
