<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Recent Repository Activity
        </x-slot>

        <x-slot name="description">
            Latest updates and contributions across your repositories
        </x-slot>

        <div class="space-y-6">
            @php
            $groupedEvents = $this->getGroupedEvents();
            @endphp

            @forelse($groupedEvents as $month => $events)
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 sticky top-0 bg-white dark:bg-gray-900 py-2 z-10">
                    {{ $month }} ({{ count($events) }} activities)
                </h3>

                <div class="space-y-3">
                    @foreach($events as $event)
                    <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <div class="flex-shrink-0 mt-1">
                            <x-filament::icon
                                :icon="$this->getActivityIcon($event['type'])"
                                class="w-5 h-5 text-{{ $this->getActivityColor($event['type']) }}-500" />
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ str_replace(config('services.github.username') . '/', '', $event['repo']) }}
                                </p>
                                <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($event['created_at'])->diffForHumans() }}
                                </span>
                            </div>

                            <div class="mt-2 flex items-center justify-between">
                                <p class="text-xs text-gray-600 dark:text-gray-300">
                                    {{ $event['payload'] }}
                                </p>
                                <x-filament::badge :color="$this->getActivityColor($event['type'])">
                                    {{ str_replace('Event', '', $event['type']) }}
                                </x-filament::badge>
                            </div>

                            @if(isset($event['all_messages']) && count($event['all_messages']) > 1)
                            <div class="mt-2" x-data="{ expanded: false }">
                                <div @click="expanded = !expanded" class="text-xs text-primary-600 dark:text-primary-400 cursor-pointer hover:underline flex items-center gap-1">
                                    <x-filament::icon
                                        icon="heroicon-o-chevron-right"
                                        class="w-3 h-3" />

                                    See all {{ count($event['all_messages']) }} commits
                                </div>
                                <div x-show="expanded" x-collapse class="mt-2 ml-4 space-y-1">
                                    @foreach($event['all_messages'] as $index => $message)
                                    <div class="flex items-start gap-2 text-xs">
                                        <span class="text-gray-400 dark:text-gray-500 font-mono">{{ $index + 1 }}.</span>
                                        <span class="text-gray-600 dark:text-gray-300">{{ $message }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            @empty
            <div class="text-center py-6">
                <x-filament::icon
                    icon="heroicon-o-inbox"
                    class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600" />
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    No recent activity found this month
                </p>
            </div>
            @endforelse

            @if(!$showAll && count($groupedEvents) > 0)
            <div class="text-center pt-2">
                <x-filament::button
                    wire:click="loadMore"
                    color="gray"
                    outlined
                    size="sm">
                    <div class="flex items-center gap-1">
                        <x-filament::icon icon="heroicon-o-arrow-down" class="w-4 h-4" />
                        <span>Show More Activity</span>
                    </div>
                </x-filament::button>
            </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>