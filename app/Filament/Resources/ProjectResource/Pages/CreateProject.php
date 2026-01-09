<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Services\GitHubService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['create_github_repo']) && $data['create_github_repo']) {
            $githubService = app(GitHubService::class);

            if ($githubService->repositoryExists($data['name'])) {
                Notification::make()
                    ->warning()
                    ->title('Repository already exists')
                    ->body("Repository '{$data['name']}' already exists in your GitHub account.")
                    ->send();

                unset($data['create_github_repo']);

                return $data;
            }

            $result = $githubService->createRepository(
                name: $data['name'],
                description: $data['description'] ?? null,
                private: $data['is_private'] ?? false,
                autoInit: true
            );

            if ($result['success']) {
                $data['github_repo_id'] = $result['data']['id'];
                $data['url'] = $result['data']['url'];
                $data['stars'] = 0;
                $data['forks'] = 0;

                Notification::make()
                    ->success()
                    ->title('Repository created on GitHub')
                    ->body("Repository '{$data['name']}' has been created successfully!")
                    ->send();
            } else {
                Notification::make()
                    ->danger()
                    ->title('Failed to create GitHub repository')
                    ->body($result['error'])
                    ->send();

                $this->halt();
            }
        }

        unset($data['create_github_repo']);
        unset($data['is_private']);

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
