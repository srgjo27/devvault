<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GitHubService;
use App\Models\Project;

class SyncGitHubProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-github';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data repository dari GitHub ke Database Lokal';

    /**
     * Execute the console command.
     */
    public function handle(GitHubService $githubService)
    {
        $this->info('Memulai sinkronisasi dengan GitHub...');

        // Ambil data mentah dari Service yang sudah kita buat sebelumnya
        // Catatan: Pastikan Service mengembalikan array mentah, bukan cache (nanti kita sesuaikan servicenya sedikit)
        $repos = $githubService->getRepositories(fromCache: false); 

        if (empty($repos)) {
            $this->error('Gagal mengambil data atau tidak ada repo ditemukan.');
            return;
        }

        $bar = $this->output->createProgressBar(count($repos));
        $bar->start();

        foreach ($repos as $repo) {
            // Logika "Update or Create"
            // Cari project berdasarkan 'github_repo_id'. 
            // Kalau ketemu -> Update datanya. Kalau belum ada -> Buat baru.
            Project::updateOrCreate(
                ['name' => $repo['name']], // Kita pakai nama sebagai kunci unik sementara (atau bisa pakai ID jika service return ID)
                [
                    'github_repo_id' => $repo['name'], // Sementara pakai nama dulu sbg ID unik
                    'description' => $repo['description'],
                    'url' => $repo['url'],
                    'stars' => $repo['stars'],
                    'forks' => $repo['forks'],
                    'language' => $repo['language'],
                    'topics' => $repo['topics'],
                    // 'is_active' tidak kita update agar settingan manual kita tidak tertimpa!
                ]
            );
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Sinkronisasi Selesai! Data sudah ada di Database.');
    }
}
