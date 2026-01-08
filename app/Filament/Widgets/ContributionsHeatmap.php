<?php

namespace App\Filament\Widgets;

use App\Services\GitHubService;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;

class ContributionsHeatmap extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.contributions-heatmap';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public ?int $selectedYear = null;

    public function mount(): void
    {
        $this->selectedYear = $this->selectedYear ?? now()->year;
    }

    protected function getFormSchema(): array
    {
        $currentYear = now()->year;
        $years = [];

        for ($year = 2020; $year <= $currentYear + 1; $year++) {
            $years[$year] = $year;
        }

        return [
            Select::make('selectedYear')
                ->label('Year')
                ->options($years)
                ->default($currentYear)
                ->live(),
        ];
    }

    public function getYear(): int
    {
        return $this->selectedYear ?? now()->year;
    }

    public function getTotalContributions(): int
    {
        return $this->getContributions()['totalContributions'] ?? 0;
    }

    public function getWeeks(): array
    {
        return $this->getContributions()['weeks'] ?? [];
    }

    protected function getContributions(): array
    {
        $githubService = app(GitHubService::class);

        return $githubService->getContributionsForYear($this->getYear());
    }
}
