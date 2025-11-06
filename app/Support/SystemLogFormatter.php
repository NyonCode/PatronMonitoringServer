<?php

namespace App\Support;

class SystemLogFormatter
{
    /**
     * Formátuje Windows Event Log zprávu jako v Event Vieweru
     */
    public static function formatted(string $message): string
    {
        // Detekce typu zprávy
        if (self::isDCOMMessage($message)) {
            return self::formatDCOMMessage($message);
        }

        // Pro jiné typy zpráv použijeme generické formátování
        return self::formatGenericMessage($message);
    }

    /**
     * Detekuje DCOM Event zprávu
     */
    private static function isDCOMMessage(string $message): bool
    {
        return str_contains($message, 'Event ID') &&
            str_contains($message, 'DCOM');
    }

    /**
     * Formátuje DCOM Event zprávu
     */
    private static function formatDCOMMessage(string $message): string
    {
        // Extrakce Event ID
        preg_match('/Event ID (\d+)/', $message, $eventIdMatch);
        $eventId = $eventIdMatch[1] ?? 'Unknown';

        // Extrakce hodnot v apostrofech
        preg_match_all("/'([^']+)'/", $message, $matches);
        $values = $matches[1] ?? [];

        // Hlavní zpráva (první řádek před hodnotami)
        $mainMessage = self::extractMainMessage($message);

        // Mapování hodnot na Windows Event Viewer labels (v češtině)
        $details = self::mapDCOMValues($values);

        return self::renderWindowsEventStyle($eventId, $mainMessage, $details);
    }

    /**
     * Mapuje hodnoty z DCOM zprávy na Windows Event Viewer strukturu
     */
    private static function mapDCOMValues(array $values): array
    {
        $mapping = [
            0 => ['label' => 'Nastavení oprávnění pro aplikaci', 'icon' => 'settings'],
            1 => ['label' => 'Typ přístupu', 'icon' => 'lock'],
            2 => ['label' => 'Typ operace', 'icon' => 'play'],
            3 => ['label' => 'CLSID', 'icon' => 'code'],
            4 => ['label' => 'APPID', 'icon' => 'code'],
            5 => ['label' => 'Počítač', 'icon' => 'computer'],
            6 => ['label' => 'Uživatel', 'icon' => 'user'],
            7 => ['label' => 'SID uživatele', 'icon' => 'key'],
            8 => ['label' => 'Adresa', 'icon' => 'network'],
            9 => ['label' => 'Balíček aplikace', 'icon' => 'package'],
            10 => ['label' => 'SID balíčku', 'icon' => 'key'],
        ];

        $details = [];
        foreach ($values as $index => $value) {
            if (isset($mapping[$index])) {
                $details[] = [
                    'label' => $mapping[$index]['label'],
                    'value' => $value,
                    'icon' => $mapping[$index]['icon']
                ];
            }
        }

        return $details;
    }

    /**
     * Extrahuje hlavní zprávu (popis problému)
     */
    private static function extractMainMessage(string $message): string
    {
        // Vezme text před prvním apostrofem
        $parts = explode("'", $message, 2);
        $mainPart = trim($parts[0] ?? $message);

        // Odstraní "Event ID XXXX ve zdroji DCOM."
        $mainPart = preg_replace('/Event ID \d+ ve zdroji \w+\.\s*/', '', $mainPart);

        // Odstraní "Následující informace jsou součástí události:"
        $mainPart = str_replace('Následující informace jsou součástí události:', '', $mainPart);
        $mainPart = trim($mainPart);

        return $mainPart;
    }

    /**
     * Vykreslí zprávu ve stylu Windows Event Vieweru
     */
    private static function renderWindowsEventStyle(string $eventId, string $mainMessage, array $details): string
    {
        $html = '<div class="windows-event-log">';

        // Event ID badge
        $html .= '<div class="flex items-center gap-2 mb-3">';
        $html .= '<span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-md text-xs font-semibold border border-blue-200 dark:border-blue-800">';
        $html .= '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        $html .= 'Event ID: ' . htmlspecialchars($eventId);
        $html .= '</span>';
        $html .= '<span class="text-xs text-zinc-500 dark:text-zinc-400">Zdroj: DCOM</span>';
        $html .= '</div>';

        // Hlavní popis
        $html .= '<div class="mb-4 p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-700">';
        $html .= '<p class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed">' . htmlspecialchars($mainMessage) . '</p>';
        $html .= '</div>';

        // Detaily (jako v Event Vieweru)
        if (!empty($details)) {
            $html .= '<div class="space-y-2">';
            $html .= '<div class="flex items-center gap-2 mb-3">';
            $html .= '<svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
            $html .= '<h4 class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wide">Podrobnosti</h4>';
            $html .= '</div>';

            foreach ($details as $detail) {
                $html .= '<div class="grid grid-cols-[160px_1fr] gap-3 py-2.5 border-b border-zinc-100 dark:border-zinc-800 last:border-0 hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors rounded px-2">';
                $html .= '<div class="text-xs font-medium text-zinc-600 dark:text-zinc-400 flex items-center">' . htmlspecialchars($detail['label']) . '</div>';
                $html .= '<div class="text-xs text-zinc-700 dark:text-zinc-300 font-mono break-all">' . htmlspecialchars($detail['value']) . '</div>';
                $html .= '</div>';
            }

            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Formátování pro obecné zprávy
     */
    private static function formatGenericMessage(string $message): string
    {
        // Pro jednoduché zprávy bez speciálního formátu
        $lines = explode("\n", $message);
        $html = '<div class="generic-log-message">';

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $class = $index === 0 ? 'font-medium text-zinc-900 dark:text-zinc-100' : 'text-zinc-600 dark:text-zinc-400';
            $html .= '<div class="' . $class . ' text-sm mb-1">' . htmlspecialchars($line) . '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Zkrácení dlouhých hodnot (např. GUIDs, SIDs)
     */
    public static function shortenValue(string $value, int $maxLength = 50): string
    {
        if (strlen($value) <= $maxLength) {
            return $value;
        }

        $start = substr($value, 0, $maxLength - 15);
        $end = substr($value, -12);

        return $start . '...' . $end;
    }

    /**
     * Detekce typu hodnoty a přidání ikony
     */
    private static function getValueIcon(string $label): string
    {
        $icons = [
            'CLSID' => '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path></svg>',
            'SID' => '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>',
            'Uživatel' => '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>',
            'Počítač' => '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"></path></svg>',
        ];

        foreach ($icons as $key => $icon) {
            if (str_contains($label, $key)) {
                return $icon;
            }
        }

        return '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';
    }
}
