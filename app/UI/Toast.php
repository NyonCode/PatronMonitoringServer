<?php

namespace App\UI;

use Livewire\Component;

class Toast
{
    /**
     * Dispatch a toast notification.
     *
     * @param  string  $text
     * @param  string|null  $heading
     * @param  string|null  $variant
     * @param  int  $duration
     *
     * @return void
     */
    public static function toast(
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
     *
     * @param  string  $text
     * @param  string|null  $heading
     * @param  int  $duration
     *
     * @return void
     */
    public static function success(string $text, ?string $heading = null, int $duration = 5000): void
    {
        static::toast($text, $heading, 'success', $duration);
    }


    /**
     * Dispatch a warning toast.
     *
     * @param  string  $text
     * @param  string|null  $heading
     * @param  int  $duration
     *
     * @return void
     */
    public static function warning(string $text, ?string $heading = null, int $duration = 5000): void
    {
        static::toast($text, $heading, 'warning', $duration);
    }

    /**
     * Dispatch a danger toast.
     *
     * @param  string  $text
     * @param  string|null  $heading
     * @param  int  $duration
     *
     * @return void
     */
    public static function danger(string $text, ?string $heading = null, int $duration = 5000): void
    {
        static::toast($text, $heading, 'danger', $duration);
    }
}
