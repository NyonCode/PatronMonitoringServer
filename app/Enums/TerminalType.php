<?php

namespace App\Enums;

enum TerminalType: string
{
    case CMD = 'cmd';
    case POWERSHELL = 'powershell';
    case PWSH = 'pwsh';

    public function label(): string
    {
        return match ($this) {
            self::CMD => 'Command Prompt',
            self::POWERSHELL => 'Windows PowerShell',
            self::PWSH => 'PowerShell Core',
        };
    }
}
