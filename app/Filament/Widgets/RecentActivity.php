<?php

namespace App\Filament\Widgets;

use App\Services\GitHubService;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class RecentActivity extends Widget
{
    protected static string $view = 'filament.widgets.recent-activity';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public bool $showAll = false;

    public ?int $selectedYear = null;

    public function mount(): void
    {
        $this->selectedYear = $this->selectedYear ?? now()->year;
    }

    #[On('year-changed')]
    public function updateYear($year): void
    {
        $this->selectedYear = $year;
        $this->showAll = false;
    }

    public function getEvents(): array
    {
        $githubService = app(GitHubService::class);
        $events = $githubService->getRecentEvents(100);

        if (! $this->showAll) {
            $selectedYear = $this->selectedYear ?? now()->year;

            if ($selectedYear == now()->year) {
                $currentMonth = now()->format('Y-m');
                $events = array_filter($events, function ($event) use ($currentMonth) {
                    return str_starts_with($event['created_at'], $currentMonth);
                });
            } else {
                $events = array_filter($events, function ($event) use ($selectedYear) {
                    return str_starts_with($event['created_at'], $selectedYear);
                });
            }
        }

        return $this->groupSimilarEvents($events);
    }

    protected function groupSimilarEvents(array $events): array
    {
        $grouped = [];
        $used = [];

        foreach ($events as $index => $event) {
            if (isset($used[$index])) {
                continue;
            }

            if ($event['type'] !== 'PushEvent') {
                $grouped[] = $event;
                continue;
            }

            $similarEvents = [$event];
            $used[$index] = true;
            $eventTime = \Carbon\Carbon::parse($event['created_at']);

            foreach ($events as $checkIndex => $checkEvent) {
                if ($checkIndex === $index || isset($used[$checkIndex])) {
                    continue;
                }

                if ($checkEvent['type'] !== 'PushEvent') {
                    continue;
                }

                $checkTime = \Carbon\Carbon::parse($checkEvent['created_at']);

                if (
                    $checkEvent['repo'] === $event['repo'] &&
                    abs($eventTime->diffInMinutes($checkTime)) <= 3600
                ) {

                    $similarEvents[] = $checkEvent;
                    $used[$checkIndex] = true;
                }
            }

            if (count($similarEvents) > 1) {
                $grouped[] = $this->mergePushEvents($similarEvents);
            } else {
                $grouped[] = $event;
            }
        }

        return $grouped;
    }

    protected function mergePushEvents(array $events): array
    {
        $firstEvent = $events[0];
        $totalCommits = 0;
        $allMessages = [];

        foreach ($events as $event) {
            if (preg_match('/Pushed (\d+) commits? to/', $event['payload'], $matches)) {
                $totalCommits += (int) $matches[1];

                if (preg_match('/: (.+)$/', $event['payload'], $msgMatches)) {
                    $allMessages[] = $msgMatches[1];
                }
            }
        }

        $branch = 'main';
        if (preg_match('/to ([^\s:]+)/', $firstEvent['payload'], $matches)) {
            $branch = $matches[1];
        }

        if ($totalCommits > 0) {
            $messagePreview = !empty($allMessages) ? ': ' . $allMessages[0] : '';
            if (count($allMessages) > 1) {
                $messagePreview .= ' (+ ' . (count($allMessages) - 1) . ' more)';
            }

            $firstEvent['payload'] = $totalCommits === 1
                ? "Pushed 1 commit to {$branch}{$messagePreview}"
                : "Pushed {$totalCommits} commits to {$branch}{$messagePreview}";

            if (count($allMessages) > 1) {
                $firstEvent['all_messages'] = $allMessages;
            }
        }

        return $firstEvent;
    }

    public function getGroupedEvents(): array
    {
        $events = $this->getEvents();
        $grouped = [];

        foreach ($events as $event) {
            $date = \Carbon\Carbon::parse($event['created_at']);
            $monthKey = $date->format('F Y');

            if (! isset($grouped[$monthKey])) {
                $grouped[$monthKey] = [];
            }

            $grouped[$monthKey][] = $event;
        }

        return $grouped;
    }

    public function loadMore(): void
    {
        $this->showAll = true;
    }

    public function getActivityIcon(string $type): string
    {
        return match ($type) {
            'PushEvent' => 'heroicon-o-arrow-up-circle',
            'CreateEvent' => 'heroicon-o-plus-circle',
            'IssuesEvent' => 'heroicon-o-exclamation-circle',
            'PullRequestEvent' => 'heroicon-o-arrow-path',
            default => 'heroicon-o-document-text',
        };
    }

    public function getActivityColor(string $type): string
    {
        return match ($type) {
            'PushEvent' => 'success',
            'CreateEvent' => 'info',
            'IssuesEvent' => 'warning',
            'PullRequestEvent' => 'primary',
            default => 'gray',
        };
    }
}
