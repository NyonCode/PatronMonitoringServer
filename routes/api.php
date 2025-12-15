<?php

use App\Http\Controllers\Api\AgentController;
use App\Http\Controllers\Api\RemoteCommandController;
use App\Http\Middleware\AgentTokenMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/agent-exist/{agent_id}', [AgentController::class, 'checkUserExists']);
// ->middleware('auth:sanctum');

Route::get('/health ', [AgentController::class, 'health']);
Route::post('/clients/register', [AgentController::class, 'registerClient']);

Route::prefix('client/{uuid}')->middleware(AgentTokenMiddleware::class)->group(function () {

    Route::post('/heartbeat', [AgentController::class, 'heartbeat']);
    Route::post('/logs', [AgentController::class, 'logs']);
    Route::post('/shutdown', [AgentController::class,'shutdown',]);



    Route::post('/terminal', [RemoteCommandController::class, 'createTerminal']);
    Route::get('/terminal', [RemoteCommandController::class, 'listTerminals']);
    Route::post('/terminal/{sessionId}/input', [RemoteCommandController::class, 'sendTerminalInput']);
    Route::get('/terminal/{sessionId}/output', [RemoteCommandController::class, 'getTerminalOutput']);
    Route::delete('/terminal/{sessionId}', [RemoteCommandController::class, 'closeTerminal']);

    Route::post('/command-results', [RemoteCommandController::class, 'storeResults']);

});
