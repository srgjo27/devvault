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

    public function createRepository(string $name, ?string $description = null, bool $private = false, bool $autoInit = true)
    {
        $response = Http::withToken($this->token)
            ->post("{$this->baseUrl}/user/repos", [
                'name' => $name,
                'description' => $description,
                'private' => $private,
                'auto_init' => $autoInit,
            ]);

        if ($response->successful()) {
            $repo = $response->json();

            return [
                'success' => true,
                'data' => [
                    'id' => $repo['id'],
                    'name' => $repo['name'],
                    'full_name' => $repo['full_name'],
                    'description' => $repo['description'],
                    'url' => $repo['html_url'],
                    'clone_url' => $repo['clone_url'],
                    'ssh_url' => $repo['ssh_url'],
                    'private' => $repo['private'],
                ],
            ];
        }

        return [
            'success' => false,
            'error' => $response->json()['message'] ?? 'Failed to create repository',
            'status' => $response->status(),
        ];
    }

    public function repositoryExists(string $name): bool
    {
        $response = Http::withToken($this->token)
            ->get("{$this->baseUrl}/repos/{$this->username}/{$name}");

        return $response->successful();
    }

    public function deleteRepository(string $name)
    {
        $response = Http::withToken($this->token)
            ->delete("{$this->baseUrl}/repos/{$this->username}/{$name}");

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => "Repository '{$name}' has been deleted from GitHub",
            ];
        }

        return [
            'success' => false,
            'error' => $response->json()['message'] ?? 'Failed to delete repository',
            'status' => $response->status(),
        ];
    }

    public function archiveRepository(string $name)
    {
        $response = Http::withToken($this->token)
            ->patch("{$this->baseUrl}/repos/{$this->username}/{$name}", [
                'archived' => true,
            ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => "Repository '{$name}' has been archived on GitHub",
            ];
        }

        return [
            'success' => false,
            'error' => $response->json()['message'] ?? 'Failed to archive repository',
            'status' => $response->status(),
        ];
    }

    public function updateRepository(string $name, array $updates)
    {
        $allowedFields = ['description', 'homepage', 'topics'];
        $filteredUpdates = array_intersect_key($updates, array_flip($allowedFields));

        if (empty($filteredUpdates)) {
            return [
                'success' => true,
                'message' => 'No changes to sync',
            ];
        }

        if (isset($filteredUpdates['topics']) && is_string($filteredUpdates['topics'])) {
            $filteredUpdates['topics'] = array_filter(
                array_map('trim', explode(',', $filteredUpdates['topics']))
            );
        }

        $response = Http::withToken($this->token)
            ->patch("{$this->baseUrl}/repos/{$this->username}/{$name}", $filteredUpdates);

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => "Repository '{$name}' has been updated on GitHub",
                'updated_fields' => array_keys($filteredUpdates),
            ];
        }

        return [
            'success' => false,
            'error' => $response->json()['message'] ?? 'Failed to update repository',
            'status' => $response->status(),
        ];
    }

    public function getRecentCommits(string $repoName, int $limit = 5)
    {
        return Cache::remember("github_commits_{$repoName}_{$limit}", 1800, function () use ($repoName, $limit) {
            $response = Http::withToken($this->token)
                ->get("{$this->baseUrl}/repos/{$this->username}/{$repoName}/commits", [
                    'per_page' => $limit,
                ]);

            if ($response->successful()) {
                return collect($response->json())->map(function ($commit) {
                    return [
                        'sha' => $commit['sha'],
                        'message' => $commit['commit']['message'],
                        'author' => $commit['commit']['author']['name'],
                        'date' => $commit['commit']['author']['date'],
                        'url' => $commit['html_url'],
                    ];
                })->toArray();
            }

            return [];
        });
    }

    public function getRecentEvents(int $limit = 30)
    {
        return Cache::remember("github_events_{$limit}", 600, function () use ($limit) {
            $response = Http::withToken($this->token)
                ->get("{$this->baseUrl}/users/{$this->username}/events", [
                    'per_page' => $limit,
                ]);

            if ($response->successful()) {
                return collect($response->json())
                    ->filter(function ($event) {
                        return in_array($event['type'], ['PushEvent', 'CreateEvent', 'IssuesEvent', 'PullRequestEvent']);
                    })
                    ->map(function ($event) {
                        return [
                            'id' => $event['id'],
                            'type' => $event['type'],
                            'repo' => $event['repo']['name'],
                            'created_at' => $event['created_at'],
                            'payload' => $this->formatEventPayload($event),
                        ];
                    })
                    ->values()
                    ->toArray();
            }

            return [];
        });
    }

    protected function getCommitComparison(string $repo, string $base, string $head)
    {
        $cacheKey = "github_comparison_{$repo}_{$base}_{$head}";

        return Cache::remember($cacheKey, 3600, function () use ($repo, $base, $head) {
            $response = Http::withToken($this->token)
                ->get("{$this->baseUrl}/repos/{$repo}/compare/{$base}...{$head}");

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'total_commits' => $data['total_commits'] ?? 0,
                    'commits' => collect($data['commits'] ?? [])->map(function ($commit) {
                        return [
                            'sha' => $commit['sha'],
                            'message' => $commit['commit']['message'],
                        ];
                    })->toArray(),
                ];
            }

            return null;
        });
    }

    protected function formatEventPayload($event)
    {
        switch ($event['type']) {
            case 'PushEvent':
                $ref = $event['payload']['ref'] ?? '';
                $branch = str_replace('refs/heads/', '', $ref);
                $before = $event['payload']['before'] ?? null;
                $head = $event['payload']['head'] ?? null;

                if ($before && $head && isset($event['repo']['name'])) {
                    $comparison = $this->getCommitComparison($event['repo']['name'], $before, $head);

                    if ($comparison && $comparison['total_commits'] > 0) {
                        $commitCount = $comparison['total_commits'];
                        $commits = $comparison['commits'];

                        if (! empty($commits)) {
                            $firstCommit = $commits[0]['message'];
                            $messageLine = explode("\n", $firstCommit)[0];
                            $message = strlen($messageLine) > 60 ? substr($messageLine, 0, 60).'...' : $messageLine;

                            if ($commitCount === 1) {
                                return "Pushed 1 commit to {$branch}: {$message}";
                            } else {
                                return "Pushed {$commitCount} commits to {$branch}: {$message}";
                            }
                        }

                        return $commitCount === 1
                            ? "Pushed 1 commit to {$branch}"
                            : "Pushed {$commitCount} commits to {$branch}";
                    }
                }

                return $branch ? "Pushed to {$branch}" : 'Pushed commits';

            case 'CreateEvent':
                $refType = $event['payload']['ref_type'] ?? 'unknown';
                $ref = $event['payload']['ref'] ?? '';

                return $ref
                    ? "Created {$refType}: {$ref}"
                    : "Created {$refType}";

            case 'IssuesEvent':
                $action = $event['payload']['action'] ?? 'unknown';
                $issue = $event['payload']['issue']['title'] ?? '';

                return $issue
                    ? "Issue {$action}: ".(strlen($issue) > 40 ? substr($issue, 0, 40).'...' : $issue)
                    : "Issue {$action}";

            case 'PullRequestEvent':
                $action = $event['payload']['action'] ?? 'unknown';
                $pr = $event['payload']['pull_request']['title'] ?? '';

                return $pr
                    ? "Pull request {$action}: ".(strlen($pr) > 40 ? substr($pr, 0, 40).'...' : $pr)
                    : "Pull request {$action}";

            default:
                return $event['type'];
        }
    }
}
