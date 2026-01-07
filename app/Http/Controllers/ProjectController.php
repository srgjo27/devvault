<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\GitHubService;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    protected $gitHubService;

    public function __construct(GitHubService $gitHubService)
    {
        $this->gitHubService = $gitHubService;
    }

    public function index(): Response
    {
        $projects = Project::where('is_active', true)
                    ->orderBy('stars', 'desc') 
                    ->get();
        
        return Inertia::render('Projects/Index', [
            'projects' => $projects
        ]);
    }
}