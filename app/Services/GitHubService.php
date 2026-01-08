<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GitHubService
{
    protected $baseUrl = 'https://api.github.com';

    protected $token;

    protected $username;

    public function __construct()
    {
        $this->token = config('services.github.token');
        $this->username = config('services.github.username');
    }

    public function getRepositories($fromCache = true)
    {
        if (! $fromCache) {
            return $this->fetchFromApi();
        }

        return Cache::remember('github_repos', 3600, function () {
            return $this->fetchFromApi();
        });
    }

    protected function fetchFromApi()
    {
        $response = Http::withToken($this->token)
            ->get("{$this->baseUrl}/users/{$this->username}/repos", [
                'sort' => 'updated',
                'direction' => 'desc',
                'per_page' => 100,
                'type' => 'owner',
            ]);

        if ($response->successful()) {
            return collect($response->json())->map(function ($repo) {
                return [
                    'name' => $repo['name'],
                    'description' => $repo['description'],
                    'url' => $repo['html_url'],
                    'stars' => $repo['stargazers_count'],
                    'forks' => $repo['forks_count'],
                    'language' => $repo['language'],
                    'topics' => $repo['topics'] ?? [],
                ];
            })->toArray();
        }

        return [];
    }

    public function getContributionsForYear($year = 2026)
    {
        return Cache::remember("github_contributions_{$year}", 3600, function () use ($year) {
            $query = <<<'GRAPHQL'
            query($username: String!, $from: DateTime!, $to: DateTime!) {
              user(login: $username) {
                contributionsCollection(from: $from, to: $to) {
                  contributionCalendar {
                    totalContributions
                    weeks {
                      contributionDays {
                        contributionCount
                        date
                      }
                    }
                  }
                }
              }
            }
            GRAPHQL;

            $from = "{$year}-01-01T00:00:00Z";
            $to = "{$year}-12-31T23:59:59Z";

            $response = Http::withToken($this->token)
                ->post('https://api.github.com/graphql', [
                    'query' => $query,
                    'variables' => [
                        'username' => $this->username,
                        'from' => $from,
                        'to' => $to,
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data']['user']['contributionsCollection']['contributionCalendar'])) {
                    return $data['data']['user']['contributionsCollection']['contributionCalendar'];
                }
            }

            return [
                'totalContributions' => 0,
                'weeks' => [],
            ];
        });
    }
}
