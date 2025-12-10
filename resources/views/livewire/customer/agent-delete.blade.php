<div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" x-data="{ activeTab: 'agent' }">
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-xl container mx-auto flex flex-col">
        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="text-center py-4">{{ __('Are you sure you want to delete this agent?') }}</div>
            <div class="flex items-end justify-center gap-4">
                <flux:button variant="danger" class="p-2" wire:click="delete">Delete</flux:button>
                <flux:button variant="primary" class="p-2" wire:click="$parent.closeDelete">Close</flux:button>
            </div>
        </div>
    </div>
</div>
