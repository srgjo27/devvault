<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'github_repo_id',
        'name',
        'description',
        'url',
        'stars',
        'forks',
        'language',
        'topics',
        'is_active',
    ];

    protected $casts = [
        'topics' => 'array',
        'is_active' => 'boolean',
    ];
}
