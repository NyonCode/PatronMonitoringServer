<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AgentResource;
use App\Models\Agent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use Psy\Util\Json;

class AgentController extends Controller
{

    /**
     * Return all agents
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return AgentResource::collection(Agent::all());
    }

    /**
     * Create new agent
     *
     * @param  Request  $request
     *
     * @return AgentResource
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
     *
     * @param  Agent  $agent
     *
     * @return AgentResource
     */
    public function show(Agent $agent): AgentResource
    {
        return new AgentResource($agent);
    }

    /**
     * Update agent
     *
     * @param  Request  $request
     * @param  Agent  $agent
     *
     * @return AgentResource
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
     *
     * @param  Agent  $agent
     *
     * @return JsonResponse
     */
    public function destroy(Agent $agent): JsonResponse
    {
        $agent->delete();

        return response()->json();
    }

    /**
     * Check if agent exists
     *
     * @param  string|int  $agent_id
     *
     * @return JsonResponse
     */
    public function checkUserExists(string|int $agent_id): JsonResponse
    {
        $exists = Agent::where('agent_id', $agent_id)->exists();

        return response()->json(['exists' => $exists]);
    }

    /**
     * Register client
     *
     * @param  Request  $request
     *
     * @return JsonResponse
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
                'token' => Str::uuid()
            ]
        );
        $agent->save();

        return response()->json(['status' => 'ok', 'token' => $agent->token, 'interval' => $agent->update_interval]);
    }

    /**
     * Heartbeat from agent
     *
     * @param  string  $UUID
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function heartbeat(string $UUID, Request $request): JsonResponse
    {
        $agent = Agent::where('uuid', $UUID)->first();

        $agent->update([
            'last_seen_at' => now()
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
                    'size' => $diskData['Size']
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
                'mac_address' => $request->network_info['MacAddress']
        ]);

        return response()->json(['RemoteCommands' => '', 'interval' => $agent->update_interval]);
    }

    /**
     * Create or update logs for agent
     *
     * @param  string  $UUID
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function logs(string $UUID, Request $request): JsonResponse
    {
        $agent = Agent::where('uuid', $UUID)->firstOrFail();

        $logs = $this->removeFullHtmlDocument($request->logs);

        $agent->log()->updateOrCreate(
            ['agent_id' => $agent->id],
            [
                'agent_log' => $logs,
                'system_logs' => $request->system_logs,
            ]
        );

        return response()->json(['status' => 'ok']);
    }

    /**
     * Return health status
     *
     * @return JsonResponse
     */
    public function health(): JsonResponse
    {
        return response()->json(['status' => 'ok', "timestamp" => now()]);
    }

    private function removeFullHtmlDocument(mixed $input): mixed
    {
        if (!is_string($input)) {
            return $input;
        }
    
        return preg_replace(
            '#<!DOCTYPE html>\s*<html[^>]*>.*?</body>\s*</html>#si',
            '',
            $input
        );
    }

}
