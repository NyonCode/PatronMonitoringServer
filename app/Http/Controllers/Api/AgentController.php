<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AgentResource;
use App\Models\Agent;
use App\Models\RemoteCommand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    /**
     * Return all agents
     */
    public function index(): AnonymousResourceCollection
    {
        return AgentResource::collection(Agent::all());
    }

    /**
     * Create new agent
     */
    public function store(Request $request): AgentResource
    {
        $data = $request->validate([
            'agent_id' => ['required', 'integer'],
            'last_seen_at' => ['required', 'date'],
            'token' => ['required'],
        ]);

        return new AgentResource(Agent::create($data));
    }

    /**
     * Show agent
     */
    public function show(Agent $agent): AgentResource
    {
        return new AgentResource($agent);
    }

    /**
     * Update agent
     */
    public function update(Request $request, Agent $agent): AgentResource
    {
        $data = $request->validate([
            'agent_id' => ['required', 'integer'],
            'last_seen_at' => ['required', 'date'],
            'token' => ['required'],
        ]);

        $agent->update($data);

        return new AgentResource($agent);
    }

    /**
     * Delete agent
     */
    public function destroy(Agent $agent): JsonResponse
    {
        $agent->delete();

        return response()->json();
    }

    /**
     * Check if agent exists
     */
    public function checkUserExists(string|int $agent_id): JsonResponse
    {
        $exists = Agent::where('agent_id', $agent_id)->exists();

        return response()->json(['exists' => $exists]);
    }

    /**
     * Register client
     */
    public function registerClient(Request $request): JsonResponse
    {
        $agent = Agent::updateOrCreate(
            ['uuid' => $request->uuid],
            [
                'uuid' => $request->uuid,
                'hostname' => $request->hostname,
                'ip_address' => $request->ip_address,
                'last_seen_at' => now(),
                'token' => Str::uuid(),
            ]
        );
        $agent->save();

        return response()->json(['status' => 'ok', 'token' => $agent->token, 'interval' => $agent->update_interval]);
    }

    /**
     * Heartbeat from agent
     */
    public function heartbeat(string $UUID, Request $request): JsonResponse
    {
        $agent = Agent::where('uuid', $UUID)->first();

        $agent->update([
            'status' => $request->status,
            'last_seen_at' => now(),
        ]);

        $agent->metrics()->create([
            'recorded_at' => now(),
            'cpu_usage_percent' => $request->system_monitor['CpuUsagePercent'],
            'ram_usage_percent' => $request->system_monitor['RamUsagePercent'],
            'gpu_usage_percent' => $request->system_monitor['GpuUsagePercent'],
        ]);

        foreach ($request->system_info['Disks'] as $diskData) {
            $agent->disk()->updateOrCreate(
                ['name' => $diskData['Name']],
                [
                    'usage_percent' => $diskData['UsagePercent'],
                    'free' => $diskData['Free'],
                    'total' => $diskData['Size'],
                ]
            );
        }

        $agent->network()->updateOrCreate(
            [],
            [
                'ip_address' => $request->network_info['Address'],
                'subnet_mask' => $request->network_info['SubnetMask'],
                'gateway' => $request->network_info['Gateway'],
                'dns' => $request->network_info['Dns'],
                'mac_address' => $request->network_info['MacAddress'],
            ]);

        $agent->sessions()->updateOrCreate(
            [],
            [
                'session_user' => $request->session_info['User'],
                'session_start' => $request->session_info['SessionStart'],
                'mapped_drivers' => $request->session_info['MappedDrives'],
                'accessible_paths' => $request->session_info['AccessiblePaths'],
            ]);

        $pendingCommands = $agent->getPendingCommands(10);
        $pendingCommands->each(fn(RemoteCommand $cmd) => $cmd->markAsSent());

        return response()->json([
            'status' => 'ok',
            'interval' => $agent->update_interval,
            'remote_commands' => $pendingCommands->map(fn(RemoteCommand $cmd) => $cmd->toApiFormat()),
        ]);    }

    /**
     * Create or update logs for agent
     */
    public function logs(string $UUID, Request $request): JsonResponse
    {
        $agent = Agent::where('uuid', $UUID)->firstOrFail();

        // $logs = $this->removeFullHtmlDocument($request->logs);

        $agent->log()->updateOrCreate(
            ['agent_id' => $agent->id],
            [
                'agent_log' => $request->logs,
                'system_logs' => $request->system_logs,
            ]
        );

        return response()->json(['status' => 'ok']);
    }

    /**
     * Return health status
     */
    public function health(): JsonResponse
    {
        return response()->json(['status' => 'ok', 'timestamp' => now()]);
    }

    /**
     * Shutdown agent
     */
    public function shutdown(Request $request, string $UUID): JsonResponse
    {
        $agent = Agent::where('uuid', $UUID)->firstOrFail();

        $agent->update([
            'status' => $request->status,
            'last_seen_at' => now(),
        ]);

        return response()->json(['status' => 'ok']);
    }

    private function removeFullHtmlDocument(mixed $input): mixed
    {
        if (! is_string($input)) {
            return $input;
        }

        return preg_replace(
            '#<!DOCTYPE html>\s*<html[^>]*>.*?</body>\s*</html>#si',
            '',
            $input
        );
    }

}
