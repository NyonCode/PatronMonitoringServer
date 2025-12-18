<?php

namespace App\UI;

use Livewire\Component;

class Toast
{
    /**
     * Dispatch a toast notification.
     */
    public static function make(
        string $text = '',
        ?string $heading = null,
        ?string $variant = null,
        int $duration = 5000
    ): void {
        $component = app('livewire')->current();

        if ($component instanceof Component) {
            $component->dispatch('toast', [
                'heading' => $heading,
                'text' => $text,
                'variant' => $variant,
                'duration' => $duration,
            ]);
        }
    }

    /**
     * Dispatch a success toast.
     */
    public static function success(string $text, ?string $heading = null, int $duration = 5000): void
    {
        static::make($text, $heading, 'success', $duration);
    }

    /**
     * Dispatch a warning toast.
     */
    public static function warning(string $text, ?string $heading = null, int $duration = 5000): void
    {
        static::make($text, $heading, 'warning', $duration);
    }

    /**
     * Dispatch a danger toast.
     */
    public static function danger(string $text, ?string $heading = null, int $duration = 5000): void
    {
        static::make($text, $heading, 'danger', $duration);
    }
}
