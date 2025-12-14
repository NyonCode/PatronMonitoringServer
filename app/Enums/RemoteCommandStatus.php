<?php

namespace App\Enums;

enum RemoteCommandStatus: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case RUNNING = 'running';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Čeká',
            self::SENT => 'Odesláno',
            self::RUNNING => 'Běží',
            self::COMPLETED => 'Dokončeno',
            self::FAILED => 'Selhalo',
            self::CANCELLED => 'Zrušeno',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::SENT => 'blue',
            self::RUNNING => 'yellow',
            self::COMPLETED => 'green',
            self::FAILED => 'red',
            self::CANCELLED => 'orange',
        };
    }

    public function isFinished(): bool
    {
        return in_array($this, [self::COMPLETED, self::FAILED, self::CANCELLED]);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::PENDING, self::SENT]);
    }
}
