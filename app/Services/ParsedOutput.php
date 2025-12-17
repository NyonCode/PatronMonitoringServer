<?php

namespace App\Services;

use Illuminate\Support\Collection;

final readonly class ParsedOutput
{
    public function __construct(
        public string $type,
        public Collection $data,
        public array $summary = [],
        public ?string $raw = null,
    ) {}

    public function isServices(): bool
    {
        return $this->type === 'services';
    }

    public function isProcesses(): bool
    {
        return $this->type === 'processes';
    }

    public function isRaw(): bool
    {
        return $this->type === 'raw';
    }

    public function isEmpty(): bool
    {
        return $this->data->isEmpty();
    }
}
