<?php

namespace App\Enums;

    enum RemoteCommandType: string
{
    case RESTART = 'restart';
    case SHUTDOWN = 'shutdown';
    case EXEC = 'exec';
    case POWERSHELL = 'powershell';
    case UPDATE = 'update';
    case SERVICE_START = 'service_start';
    case SERVICE_STOP = 'service_stop';
    case SERVICE_RESTART = 'service_restart';
    case KILL_PROCESS = 'kill_process';
    case GET_PROCESSES = 'get_processes';
    case GET_SERVICES = 'get_services';
    case DOWNLOAD = 'download';
    case GET_FILE = 'get_file';
    case DELETE_FILE = 'delete_file';
    case CREATE_FILE = 'create_file';
    case RUN_SCRIPT = 'run_script';
    case TERMINAL_CREATE = 'terminal_create';
    case TERMINAL_INPUT = 'terminal_input';
    case TERMINAL_OUTPUT = 'terminal_output';
    case TERMINAL_CLOSE = 'terminal_close';
    case TERMINAL_LIST = 'terminal_list';

    public function label(): string
    {
        return match ($this) {
            self::RESTART => 'Restart systému',
            self::SHUTDOWN => 'Vypnutí systému',
            self::EXEC => 'Spustit příkaz',
            self::POWERSHELL => 'PowerShell příkaz',
            self::UPDATE => 'Aktualizace agenta',
            self::SERVICE_START => 'Spustit službu',
            self::SERVICE_STOP => 'Zastavit službu',
            self::SERVICE_RESTART => 'Restartovat službu',
            self::KILL_PROCESS => 'Ukončit proces',
            self::GET_PROCESSES => 'Seznam procesů',
            self::GET_SERVICES => 'Seznam služeb',
            self::DOWNLOAD => 'Stáhnout soubor',
            self::GET_FILE => 'Získat soubor',
            self::DELETE_FILE => 'Smazat soubor',
            self::CREATE_FILE => 'Vytvořit soubor',
            self::RUN_SCRIPT => 'Spustit skript',
            self::TERMINAL_CREATE => 'Vytvořit terminál',
            self::TERMINAL_INPUT => 'Vstup do terminálu',
            self::TERMINAL_OUTPUT => 'Výstup z terminálu',
            self::TERMINAL_CLOSE => 'Zavřít terminál',
            self::TERMINAL_LIST => 'Seznam terminálů',
        };
    }

    public function isTerminalCommand(): bool
    {
        return in_array($this, [
            self::TERMINAL_CREATE,
            self::TERMINAL_INPUT,
            self::TERMINAL_OUTPUT,
            self::TERMINAL_CLOSE,
            self::TERMINAL_LIST,
        ]);
    }
}
