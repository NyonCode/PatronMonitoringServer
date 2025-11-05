<?php

namespace App\Support;

class SystemLogFormatter
{
    public static function formatted(string $message): string
    {
        preg_match_all("/'([^']+)'/", $message, $matches);

        $labels = [
            'Výchozí pro počítač',
            'Oprávnění',
            'Operace',
            'CLSID',
            'APPID',
            'Počítač',
            'Uživatel',
            'SID',
            'Místo volání',
            'Aplikace',
            'Aplikační SID',
        ];

        $details = collect($matches[1] ?? [])
            ->map(function ($value, $index) use ($labels) {
                $label = $labels[$index] ?? "Parametr $index";
                return "<div><strong>{$label}:</strong> {$value}</div>";
            })
            ->implode('');

        $header = strtok($message, "\n");
        return "<div>{$header}</div><hr class='py-2'>{$details}";
    }
}
