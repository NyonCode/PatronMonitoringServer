<?php

namespace App\Enums;

enum TerminalSessionStatus: string
{
    case RUNNING = 'running';
    case CLOSED = 'closed';
    case ERROR = 'error';

    public function label(): string
    {
        return match ($this) {
            self::RUNNING => 'Běží',
            self::CLOSED => 'Uzavřeno',
            self::ERROR => 'Chyba',
        };
    }

    public function isActive(): bool
    {
        return $this === self::RUNNING;
    }
}
