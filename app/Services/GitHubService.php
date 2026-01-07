<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

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
        if (!$fromCache) {
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
}