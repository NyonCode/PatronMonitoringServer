<?php

use App\Http\Controllers\Api\AgentController;
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

Route::middleware(AgentTokenMiddleware::class)->group(function () {
    Route::post('/client/{UUID}/heartbeat', [AgentController::class, 'heartbeat']);
    Route::post('/client/{UUID}/logs', [AgentController::class, 'logs']);
    Route::post('/client/{UUID}/shutdown', [AgentController::class,'shutdown',]);
});
