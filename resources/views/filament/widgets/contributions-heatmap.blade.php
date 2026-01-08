<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            GitHub Contributions {{ $this->getYear() }}
        </x-slot>

        <x-slot name="headerEnd">
            {{ $this->form }}
        </x-slot>

        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Total: <strong>{{ $this->getTotalContributions() }}</strong> contributions
                </span>
                <div class="flex gap-2 items-center text-xs text-gray-500">
                    <span>Less</span>
                    <div class="flex gap-1">
                        <div class="w-3 h-3 rounded-sm bg-gray-200 dark:bg-gray-700"></div>
                        <div class="w-3 h-3 rounded-sm" style="background-color: #9be9a8;"></div>
                        <div class="w-3 h-3 rounded-sm" style="background-color: #40c463;"></div>
                        <div class="w-3 h-3 rounded-sm" style="background-color: #30a14e;"></div>
                        <div class="w-3 h-3 rounded-sm" style="background-color: #216e39;"></div>
                    </div>
                    <span>More</span>
                </div>
            </div>

            <div class="overflow-x-auto pb-2">
                <div class="inline-flex gap-1">
                    @foreach($this->getWeeks() as $week)
                        <div class="flex flex-col gap-1">
                            @foreach($week['contributionDays'] as $day)
                                @php
                                    $count = $day['contributionCount'];
                                    $date = \Carbon\Carbon::parse($day['date']);
                                    $dayName = $date->format('D');
                                    
                                    if ($count === 0) {
                                        $color = 'bg-gray-200 dark:bg-gray-700';
                                    } elseif ($count <= 3) {
                                        $color = '#9be9a8';
                                    } elseif ($count <= 6) {
                                        $color = '#40c463';
                                    } elseif ($count <= 9) {
                                        $color = '#30a14e';
                                    } else {
                                        $color = '#216e39';
                                    }
                                @endphp
                                <div 
                                    class="w-3 h-3 rounded-sm {{ $count === 0 ? $color : '' }} hover:ring-2 hover:ring-gray-400 transition-all cursor-pointer" 
                                    @if($count > 0) style="background-color: {{ $color }};" @endif
                                    title="{{ $date->format('M d, Y') }} ({{ $dayName }}): {{ $count }} contributions"
                                ></div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                <p>Contribution activity in {{ $this->getYear() }}</p>
                <p>{{ count($this->getWeeks()) }} weeks tracked</p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
