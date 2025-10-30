<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AgentResource;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    public function index()
    {
        return AgentResource::collection(Agent::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'agent_id' => ['required', 'integer'],
            'last_seen_at' => ['required', 'date'],
            'token' => ['required'],
        ]);

        return new AgentResource(Agent::create($data));
    }

    public function show(Agent $agent)
    {
        return new AgentResource($agent);
    }

    public function update(Request $request, Agent $agent)
    {
        $data = $request->validate([
            'agent_id' => ['required', 'integer'],
            'last_seen_at' => ['required', 'date'],
            'token' => ['required'],
        ]);

        $agent->update($data);

        return new AgentResource($agent);
    }

    public function destroy(Agent $agent)
    {
        $agent->delete();

        return response()->json();
    }

    public function checkUserExists(string|int $agent_id)
    {
        $exists = Agent::where('agent_id', $agent_id)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function registerClient(Request $request)
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

    public function heartbeat(string $UUID, Request $request)
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

        return response()->json(['RemoteCommands' => '']);
    }

    public function logs(string $UUID, Request $request)
    {
        $agent = Agent::where('uuid', $UUID)->first();

        $agent->log()->updateOrCreate(
            [],
            [
            'agent_log' => $request->logs,
            'system_logs' => $request->system_logs
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function health()
    {
        return response()->json(['status' => 'ok', "timestamp" => now()]);
    }
}
