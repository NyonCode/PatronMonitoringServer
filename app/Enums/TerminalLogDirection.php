<?php

namespace App\Enums;

enum TerminalLogDirection: string
{
    case INPUT = 'input';
    case OUTPUT = 'output';

    public function label(): string
    {
        return match ($this) {
            self::INPUT => 'Vstup',
            self::OUTPUT => 'Výstup',
        };
    }
}
