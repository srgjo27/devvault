<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Services\GitHubService;
use Filament\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->modalHeading('Delete Project')
                ->modalDescription('Are you sure you want to delete this project? This action cannot be undone.')
                ->form([
                    Checkbox::make('delete_from_github')
                        ->label('Also delete/archive repository from GitHub')
                        ->helperText('Warning: This will permanently delete or archive the repository from your GitHub account')
                        ->reactive()
                        ->default(false),

                    Radio::make('github_action')
                        ->label('GitHub Action')
                        ->options([
                            'delete' => 'Permanently Delete (Cannot be undone)',
                            'archive' => 'Archive (Can be restored later)',
                        ])
                        ->default('archive')
                        ->visible(fn (callable $get) => $get('delete_from_github')),
                ])
                ->modalSubmitActionLabel('Yes, Delete')
                ->successNotificationTitle('Project deleted')
                ->after(function (array $data) {
                    if ($data['delete_from_github'] ?? false) {
                        $this->handleGitHubDeletion($data['github_action'] ?? 'archive');
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['sync_to_github']) && $data['sync_to_github']) {
            $githubService = app(GitHubService::class);

            $updates = [
                'description' => $data['description'] ?? null,
                'topics' => $data['topics'] ?? null,
            ];

            $result = $githubService->updateRepository(
                $this->record->name,
                $updates
            );

            if ($result['success']) {
                Notification::make()
                    ->success()
                    ->title('Synced to GitHub')
                    ->body($result['message'])
                    ->send();
            } else {
                Notification::make()
                    ->warning()
                    ->title('GitHub sync failed')
                    ->body($result['error'])
                    ->send();
            }
        }

        unset($data['sync_to_github']);

        return $data;
    }

    protected function handleGitHubDeletion(string $action): void
    {
        $githubService = app(GitHubService::class);
        $repoName = $this->record->name;

        if ($action === 'delete') {
            $result = $githubService->deleteRepository($repoName);
        } else {
            $result = $githubService->archiveRepository($repoName);
        }

        if ($result['success']) {
            Notification::make()
                ->success()
                ->title('GitHub action successful')
                ->body($result['message'])
                ->send();
        } else {
            Notification::make()
                ->warning()
                ->title('GitHub action failed')
                ->body($result['error'])
                ->send();
        }
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Project updated')
            ->body('The project has been updated successfully.');
    }
}
