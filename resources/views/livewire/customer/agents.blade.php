<div>
    <div class="p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Agents</h2>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Monitoring system resources</p>
            </div>
            <button 
                wire:click="refresh" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
        </div>

        <!-- Agents Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($agents as $agent)
            <div 
                wire:click="selectAgent({{ $agent->id }})"
                class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 cursor-pointer hover:shadow-lg hover:border-blue-500 dark:hover:border-blue-400 transition-all duration-200 {{ $selectedAgentId === $agent->id ? 'ring-2 ring-blue-500 border-blue-500' : '' }}"
            >
                <!-- Agent Header -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                </svg>
                            </div>
                            <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white dark:border-zinc-900 rounded-full"></span>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $agent->name }}</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $agent->hostname }}</p>
                        </div>
                    </div>
                </div>

                <!-- CPU Metric -->
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                            <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">CPU</span>
                        </div>
                        <span class="text-xs font-semibold 
                            @if($agent->latestMetric && $agent->latestMetric->cpu_usage > 80)
                                text-red-600 dark:text-red-400
                            @elseif($agent->latestMetric && $agent->latestMetric->cpu_usage > 60)
                                text-yellow-600 dark:text-yellow-400
                            @else
                                text-green-600 dark:text-green-400
                            @endif
                        ">
                            {{ $agent->latestMetric ? number_format($agent->latestMetric->cpu_usage, 1) : '0.0' }}% 
                            <span class="text-zinc-500 dark:text-zinc-400">/ 100%</span>
                        </span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 overflow-hidden">
                        <div
                            class="h-2 rounded-full transition-all duration-500 
                            @if($agent->latestMetric && $agent->latestMetric->cpu_usage > 80)
                                bg-red-500
                            @elseif($agent->latestMetric && $agent->latestMetric->cpu_usage > 60)
                                bg-yellow-500
                            @else
                                bg-blue-500
                            @endif"
                            style="width: {{ $agent->latestMetric ? $agent->latestMetric->cpu_usage : 0 }}%"
                        ></div>
                    </div>
                </div>

                <!-- RAM Metric -->
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">RAM</span>
                        </div>
                        @php
                            $ramUsed = $agent->latestMetric ? $agent->latestMetric->memory_used / (1024**3) : 0;
                            $ramTotal = $agent->latestMetric ? $agent->latestMetric->memory_total / (1024**3) : 0;
                            $ramPercent = $ramTotal > 0 ? ($ramUsed / $ramTotal) * 100 : 0;
                        @endphp
                        <span class="text-xs font-semibold 
                            @if($ramPercent > 80)
                                text-red-600 dark:text-red-400
                            @elseif($ramPercent > 60)
                                text-yellow-600 dark:text-yellow-400
                            @else
                                text-green-600 dark:text-green-400
                            @endif
                        ">
                            {{ number_format($ramUsed, 1) }} GB / {{ number_format($ramTotal, 1) }} GB
                        </span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 overflow-hidden">
                        <div
                            class="h-2 rounded-full transition-all duration-500 
                            @if($ramPercent > 80)
                                bg-red-500
                            @elseif($ramPercent > 60)
                                bg-yellow-500
                            @else
                                bg-purple-500
                            @endif"
                            style="width: {{ $ramPercent }}%"
                        ></div>
                    </div>
                </div>

                <!-- GPU Metric -->
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                            <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">GPU</span>
                        </div>
                        @php
                            $gpuUsed = $agent->latestMetric && $agent->latestMetric->gpu_memory_used ? $agent->latestMetric->gpu_memory_used / 1024 : 0;
                            $gpuTotal = $agent->latestMetric && $agent->latestMetric->gpu_memory_total ? $agent->latestMetric->gpu_memory_total / 1024 : 0;
                            $gpuPercent = $agent->latestMetric ? $agent->latestMetric->gpu_usage : 0;
                        @endphp
                        <span class="text-xs font-semibold 
                            @if($gpuPercent > 80)
                                text-red-600 dark:text-red-400
                            @elseif($gpuPercent > 60)
                                text-yellow-600 dark:text-yellow-400
                            @else
                                text-green-600 dark:text-green-400
                            @endif
                        ">
                            @if($gpuTotal > 0)
                                {{ number_format($gpuUsed, 1) }} GB / {{ number_format($gpuTotal, 1) }} GB
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 overflow-hidden">
                        <div
                            class="h-2 rounded-full transition-all duration-500 
                            @if($gpuPercent > 80)
                                bg-red-500
                            @elseif($gpuPercent > 60)
                                bg-yellow-500
                            @else
                                bg-green-500
                            @endif"
                            style="width: {{ $gpuPercent }}%"
                        ></div>
                    </div>
                </div>

                <!-- Disk Metric -->
                @if($agent->latestMetric && $agent->latestMetric->disk_used && $agent->latestMetric->disk_total)
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                            <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Disk</span>
                        </div>
                        @php
                            $diskUsed = $agent->latestMetric->disk_used / (1024**3);
                            $diskTotal = $agent->latestMetric->disk_total / (1024**3);
                            $diskPercent = ($diskUsed / $diskTotal) * 100;
                        @endphp
                        <span class="text-xs font-semibold 
                            @if($diskPercent > 80)
                                text-red-600 dark:text-red-400
                            @elseif($diskPercent > 60)
                                text-yellow-600 dark:text-yellow-400
                            @else
                                text-green-600 dark:text-green-400
                            @endif
                        ">
                            {{ number_format($diskUsed, 1) }} GB / {{ number_format($diskTotal, 1) }} GB
                        </span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 overflow-hidden">
                        <div
                            class="h-2 rounded-full transition-all duration-500 
                            @if($diskPercent > 80)
                                bg-red-500
                            @elseif($diskPercent > 60)
                                bg-yellow-500
                            @else
                                bg-orange-500
                            @endif"
                            style="width: {{ $diskPercent }}%"
                        ></div>
                    </div>
                </div>
                @endif

                <!-- Last Update -->
                <div class="mt-3 pt-3 border-t border-zinc-200 dark:border-zinc-700">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Last update: {{ $agent->latestMetric ? $agent->latestMetric->created_at->diffForHumans() : 'Never' }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        @if($agents->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-zinc-900 dark:text-white">No agents found</h3>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Get started by adding a new agent.</p>
        </div>
        @endif
    </div>
</div>
