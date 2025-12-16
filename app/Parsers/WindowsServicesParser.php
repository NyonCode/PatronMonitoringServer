<?php

namespace App\Parsers;

use App\DTO\WindowsServiceDto;
use Illuminate\Support\Collection;
use JsonException;

final class WindowsServicesParser
{
    /**
     * @throws JsonException
     */
    public function parse(string $json): Collection
    {
        $decoded = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

        return collect($decoded)
            ->filter(fn ($item) => is_array($item))
            ->map(fn ($item) => WindowsServiceDto::fromArray($item));
    }
}
