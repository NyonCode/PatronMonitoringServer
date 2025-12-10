<div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4"
     x-data="{ activeTab: 'agent' }"
     @if($autoRefresh) wire:poll.5s="refreshLogs" @endif>

    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-xl w-full max-w-7xl max-h-[90vh] flex flex-col">
        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="text-center py-4">{{ __('Are you sure you want to delete this agent?') }}</div>
            <flux:button variant="danger" wire:click="delete">Delete</flux:button>
            <flux:button variant="primary" wire:click="$parent.closeDelete">Close</flux:button>
        </div>
    </div>
</div>
