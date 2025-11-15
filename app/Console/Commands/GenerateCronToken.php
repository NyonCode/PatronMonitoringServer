<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateCronToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:generate-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vygeneruje unikátní cron token a uloží ho do .env souboru.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Generování unikátního tokenu
        $token = Str::random(32);

        // Získání cesty k .env souboru
        $envFile = base_path('.env');

        if (File::exists($envFile)) {
            // Uložení tokenu do .env souboru
            file_put_contents($envFile, str_replace(
                'CRON_TOKEN='.env('CRON_TOKEN'),
                'CRON_TOKEN='.$token,
                file_get_contents($envFile)
            ));

            $this->info('Nový cron token byl vygenerován a uložen do .env souboru.');

            $this->info($token);

        } else {
            $this->error('.env soubor neexistuje.');
        }
    }
}
