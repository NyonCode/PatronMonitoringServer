<?php

namespace App\Support;

class MacAddressFormatter
{
    /**
     * Normalize MAC address to a standard format (no separator).
     */
    public static function normalize(string $mac): string
    {
        return strtoupper(preg_replace('/[^a-fA-F0-9]/', '', $mac));
    }

    /**
     * Format MAC address with colons (e.g., AA:BB:CC:DD:EE:FF).
     */
    public static function formatWithColons(string $mac): string
    {
        $normalized = self::normalize($mac);

        return implode(':', str_split($normalized, 2));
    }

    /**
     * Format MAC address with dashes (e.g., AA-BB-CC-DD-EE-FF).
     */
    public static function formatWithDashes(string $mac): string
    {
        $normalized = self::normalize($mac);

        return implode('-', str_split($normalized, 2));
    }

    /**
     * Format MAC address without any separator (e.g., AABBCCDDEEFF).
     */
    public static function formatWithoutSeparator(string $mac): string
    {
        return self::normalize($mac);
    }

    /**
     * Check if the given MAC address is valid.
     */
    public static function isValid(string $mac): bool
    {
        return preg_match('/^([a-fA-F0-9]{2}([-:])?){5}[a-fA-F0-9]{2}$/', $mac);
    }
}
