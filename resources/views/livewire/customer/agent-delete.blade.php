<div class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-xl w-auto max-w-lg max-h-[90vh] flex flex-col">

        <!-- Content -->
        <div class="p-6 overflow-y-auto">
            <div class="text-center break-words">
                {{ __('Are you sure you want to delete this agent?') }}
            </div>

            <div class="flex justify-center gap-4 mt-6">
                <flux:button variant="danger" class="p-2" wire:click="delete">Delete</flux:button>
                <flux:button variant="primary" class="p-2" wire:click="$parent.closeDelete">Close</flux:button>
            </div>
        </div>
    </div>
</div>
