<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('GitHub Integration')
                    ->description('Create a new repository on GitHub automatically')
                    ->schema([
                        Forms\Components\Toggle::make('create_github_repo')
                            ->label('Create Repository on GitHub')
                            ->helperText('Check this to automatically create a new repository in your GitHub account')
                            ->reactive()
                            ->default(false)
                            ->hiddenOn('edit'),

                        Forms\Components\Toggle::make('is_private')
                            ->label('Private Repository')
                            ->helperText('Make the repository private (only you can see it)')
                            ->default(false)
                            ->visible(fn (callable $get) => $get('create_github_repo'))
                            ->hiddenOn('edit'),

                        Forms\Components\Toggle::make('sync_to_github')
                            ->label('Sync Changes to GitHub')
                            ->helperText('Update repository description and topics on GitHub')
                            ->default(false)
                            ->visibleOn('edit'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Repository Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Repository Name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Use lowercase, hyphens, and underscores only'),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->helperText('Brief description of the repository')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('language')
                            ->label('Primary Language')
                            ->maxLength(255)
                            ->helperText('e.g., PHP, JavaScript, Python'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('GitHub Data')
                    ->description('This information will be auto-filled when creating from GitHub')
                    ->schema([
                        Forms\Components\TextInput::make('github_repo_id')
                            ->label('GitHub Repository ID')
                            ->disabled(fn ($context) => $context === 'create')
                            ->dehydrated()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('url')
                            ->label('Repository URL')
                            ->url()
                            ->disabled(fn ($context) => $context === 'create')
                            ->dehydrated()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('stars')
                            ->label('Stars')
                            ->numeric()
                            ->default(0)
                            ->disabled(fn ($context) => $context === 'create')
                            ->dehydrated(),

                        Forms\Components\TextInput::make('forks')
                            ->label('Forks')
                            ->numeric()
                            ->default(0)
                            ->disabled(fn ($context) => $context === 'create')
                            ->dehydrated(),

                        Forms\Components\TextInput::make('topics')
                            ->label('Topics')
                            ->helperText('Comma-separated topics'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('github_repo_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stars')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('forks')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('language')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
