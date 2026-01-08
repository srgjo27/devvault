<?php

namespace App\Filament\Widgets;

use App\Services\GitHubService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ContributionsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $contributions = $this->getContributionsData();

        return [
            Stat::make('Total Repositories', $contributions['total_repos'])
                ->description('Your GitHub repositories')
                ->descriptionIcon('heroicon-m-code-bracket')
                ->color('success'),

            Stat::make('Total Stars', $contributions['total_stars'])
                ->description('Stars across all repos')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('Total Forks', $contributions['total_forks'])
                ->description('Forks across all repos')
                ->descriptionIcon('heroicon-m-arrow-path-rounded-square')
                ->color('info'),

            Stat::make('Active Projects', $contributions['active_projects'])
                ->description('Projects in database')
                ->descriptionIcon('heroicon-m-folder-open')
                ->color('primary'),
        ];
    }

    protected function getContributionsData(): array
    {
        return Cache::remember('github_contributions_stats', 3600, function () {
            $githubService = app(GitHubService::class);
            $repos = $githubService->getRepositories();

            $totalStars = 0;
            $totalForks = 0;

            foreach ($repos as $repo) {
                $totalStars += $repo['stars'] ?? 0;
                $totalForks += $repo['forks'] ?? 0;
            }

            return [
                'total_repos' => count($repos),
                'total_stars' => $totalStars,
                'total_forks' => $totalForks,
                'active_projects' => \App\Models\Project::where('is_active', true)->count(),
            ];
        });
    }
}
