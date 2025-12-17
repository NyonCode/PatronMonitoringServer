<?php

namespace App\Http\Controllers\Crons;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CronController extends Controller
{
    /**
     * Spustí všechny naplánované úkoly podle Laravel Scheduleru.
     */
    public function runSchedule(Request $request, string $token): JsonResponse
    {
        $this->validateToken($token);

        Artisan::call('schedule:run');
        Log::info('CRON: schedule:run spuštěn', ['request' => $request->all()]);

        return response()->json(['status' => 'OK', 'task' => 'schedule']);
    }

    /**
     * Zpracuje všechny joby ve frontě pomocí `queue:work`
     */
    public function runQueue(Request $request, string $token): JsonResponse
    {
        $this->validateToken($token);

        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
            '--tries' => 3,
        ]);
        Log::info('CRON: queue:work spuštěn', ['request' => $request->all()]);

        return response()->json(['status' => 'OK', 'task' => 'queue']);
    }

    /**
     * Zpracuje specifické fronty jobů (e-maily a notifikace)
     */
    public function runNotifications(Request $request, string $token): JsonResponse
    {
        $this->validateToken($token);

        Artisan::call('queue:work', [
            '--queue' => 'emails,notifications',
            '--stop-when-empty' => true,
        ]);
        Log::info('CRON: notifications spuštěn', ['request' => $request->all()]);

        return response()->json(['status' => 'OK', 'task' => 'notifications']);
    }

    /**
     * Validate token
     */
    protected function validateToken(string $token): void
    {
        if ($token !== config('app.cron_token')) {
            abort(403, 'Unauthorized access');
        }
    }
}
