<div class="space-y-3">
    @foreach($devices as $d)
        <div class="p-3 bg-white rounded-lg border flex items-center justify-between">
            <div>
                <div class="text-sm font-medium text-gray-900">{{ $d['name'] }}</div>
                <div class="text-xs text-gray-500">CPU {{ $d['cpu'] }}% · MEM {{ $d['mem'] }}% · Disk {{ $d['disk'] }}%</div>
            </div>
            <div class="text-right">
                @if($d['status'] === 'critical')
                    <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded-full">critical</span>
                @elseif($d['status'] === 'warning')
                    <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full">warning</span>
                @else
                    <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded-full">ok</span>
                @endif
            </div>
        </div>
    @endforeach
</div>
