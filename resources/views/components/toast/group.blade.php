@props([
    'position' => 'bottom end',
    'expanded' => false,
])

@php
    $positionClasses = match($position) {
        'top start' => 'top-4 left-4 items-start',
        'top center' => 'top-4 left-1/2 -translate-x-1/2 items-center',
        'top end' => 'top-4 right-4 items-end',
        'bottom start' => 'bottom-4 left-4 items-start',
        'bottom center' => 'bottom-4 left-1/2 -translate-x-1/2 items-center',
        'bottom end' => 'bottom-4 right-4 items-end',
        default => 'bottom-4 right-4 items-end',
    };

    $isTop = str_starts_with($position, 'top');
@endphp

<div
    x-data="toastGroupManager({{ $expanded ? 'true' : 'false' }}, {{ $isTop ? 'true' : 'false' }})"
    x-on:toast.window="add($event.detail)"
    @mouseenter="hovering = true"
    @mouseleave="hovering = false"
    {{ $attributes->class(['fixed z-50 flex flex-col', $positionClasses]) }}
>
    <template x-for="(toast, index) in toasts" :key="toast.id">
        <div
            x-data="{ show: false }"
            x-init="$nextTick(() => show = true)"
            x-show="show"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-y-2 opacity-0 scale-95"
            x-transition:enter-end="translate-y-0 opacity-100 scale-100"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-y-0 opacity-100 scale-100"
            x-transition:leave-end="translate-y-2 opacity-0 scale-95"
            class="relative w-80 max-w-sm overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black/5 dark:bg-zinc-800 dark:ring-white/10 transition-all duration-300"
            :class="{
                'border-l-4 border-green-500': toast.variant === 'success',
                'border-l-4 border-blue-500': toast.variant === 'info',
                'border-l-4 border-amber-500': toast.variant === 'warning',
                'border-l-4 border-red-500': toast.variant === 'danger',
            }"
            :style="getToastStyle(index)"
        >
            <div class="p-4">
                <div class="flex items-start gap-3">
                    {{-- Success Icon --}}
                    <div x-show="toast.variant === 'success'" class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    {{-- Info Icon --}}
                    <div x-show="toast.variant === 'info'" class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    {{-- Warning Icon --}}
                    <div x-show="toast.variant === 'warning'" class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    {{-- Danger Icon --}}
                    <div x-show="toast.variant === 'danger'" class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    {{-- Default Icon (no variant) --}}
                    <div x-show="!toast.variant" class="flex-shrink-0">
                        <svg class="h-5 w-5 text-zinc-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 pt-0.5">
                        <p x-show="toast.heading" class="text-sm font-medium text-zinc-900 dark:text-zinc-100" x-text="toast.heading"></p>
                        <p
                            class="text-sm text-zinc-500 dark:text-zinc-400"
                            :class="{ 'mt-1': toast.heading }"
                            x-text="toast.text"
                        ></p>
                    </div>

                    {{-- Close button --}}
                    <div class="flex flex-shrink-0">
                        <button
                            type="button"
                            @click="remove(toast.id)"
                            class="inline-flex rounded-md text-zinc-400 hover:text-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 dark:hover:text-zinc-300"
                        >
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Progress bar --}}
            <div x-show="toast.duration > 0" class="h-1 bg-zinc-100 dark:bg-zinc-700">
                <div
                    class="h-full transition-all ease-linear"
                    :class="{
                        'bg-green-500': toast.variant === 'success',
                        'bg-blue-500': toast.variant === 'info',
                        'bg-amber-500': toast.variant === 'warning',
                        'bg-red-500': toast.variant === 'danger',
                        'bg-zinc-400': !toast.variant,
                    }"
                    :style="`width: ${toast.progress}%; transition-duration: 100ms;`"
                ></div>
            </div>
        </div>
    </template>
</div>

@once
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('toastGroupManager', (alwaysExpanded = false, isTop = false) => ({
                toasts: [],
                counter: 0,
                hovering: false,
                alwaysExpanded,
                isTop,

                get isExpanded() {
                    return this.alwaysExpanded || this.hovering;
                },

                add(input) {
                    const id = ++this.counter;
                    const data = this.parseInput(input);
                    const duration = data.duration ?? 5000;

                    const newToast = {
                        id,
                        heading: data.heading || null,
                        text: data.text || '',
                        variant: data.variant || null,
                        duration,
                        progress: 100,
                    };

                    if (this.isTop) {
                        this.toasts.push(newToast);
                    } else {
                        this.toasts.unshift(newToast);
                    }

                    if (duration > 0) {
                        this.startProgress(id, duration);
                    }
                },

                parseInput(input) {
                    // String: "message"
                    if (typeof input === 'string') {
                        return { text: input };
                    }

                    // Null/undefined
                    if (!input) {
                        return {};
                    }

                    // Array from Livewire: [{ text, variant, ... }]
                    if (Array.isArray(input)) {
                        return input[0] || {};
                    }

                    // Object with numeric key from Livewire: { 0: { text, variant, ... } }
                    if (typeof input === 'object' && '0' in input) {
                        const first = input['0'];
                        if (typeof first === 'object') {
                            return first;
                        }
                    }

                    // Regular object: { text, variant, ... }
                    return input;
                },

                startProgress(id, duration) {
                    const interval = 100;
                    const decrement = (interval / duration) * 100;

                    const timer = setInterval(() => {
                        const toast = this.toasts.find(t => t.id === id);
                        if (!toast) {
                            clearInterval(timer);
                            return;
                        }

                        toast.progress -= decrement;

                        if (toast.progress <= 0) {
                            clearInterval(timer);
                            this.remove(id);
                        }
                    }, interval);
                },

                remove(id) {
                    const index = this.toasts.findIndex(t => t.id === id);
                    if (index > -1) {
                        this.toasts.splice(index, 1);
                    }
                },

                getToastStyle(index) {
                    if (this.isExpanded) {
                        return `margin-bottom: 0.5rem; transform: translateY(0) scale(1); z-index: ${100 - index};`;
                    }

                    const offset = index * 8;
                    const scale = 1 - (index * 0.05);
                    const opacity = index > 2 ? 0 : 1;

                    if (this.isTop) {
                        return `
                        position: ${index === 0 ? 'relative' : 'absolute'};
                        top: ${offset}px;
                        transform: scale(${Math.max(scale, 0.9)});
                        opacity: ${opacity};
                        z-index: ${100 - index};
                    `;
                    }

                    return `
                    position: ${index === 0 ? 'relative' : 'absolute'};
                    bottom: ${offset}px;
                    transform: scale(${Math.max(scale, 0.9)});
                    opacity: ${opacity};
                    z-index: ${100 - index};
                `;
                },
            }));
        });

        // Global Toast function
        if (typeof window.Toast === 'undefined') {
            window.Toast = {
                toast(options) {
                    if (typeof options === 'string') {
                        options = { text: options };
                    }
                    window.dispatchEvent(new CustomEvent('toast', { detail: options }));
                },
                success(text, heading = null, duration = 5000) {
                    this.toast({ text, heading, variant: 'success', duration });
                },
                info(text, heading = null, duration = 5000) {
                    this.toast({ text, heading, variant: 'info', duration });
                },
                warning(text, heading = null, duration = 5000) {
                    this.toast({ text, heading, variant: 'warning', duration });
                },
                danger(text, heading = null, duration = 5000) {
                    this.toast({ text, heading, variant: 'danger', duration });
                },
            };

            document.addEventListener('alpine:init', () => {
                Alpine.magic('toast', () => (options) => {
                    window.Toast.toast(options);
                });
            });
        }
    </script>
@endonce
