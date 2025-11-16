<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(Artisan::call('metrics:clean'))->daily();
Schedule::call(Artisan::call('metrics:aggregate'))->hourly();
