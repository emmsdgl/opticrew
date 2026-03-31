<?php

namespace App\Console\Commands;

use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Services\Embedding\EmbeddingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncEmbeddings extends Command
{
    protected $signature = 'embeddings:sync {--export : Export skill_embeddings table to SQL file after syncing}';
    protected $description = 'Encode all skills from job postings and applications into the skill_embeddings table via SBERT API';

    public function handle(): int
    {
        $service = new EmbeddingService();

        // Check SBERT API
        $this->info('Checking SBERT API...');
        if (!$service->isAvailable()) {
            $this->error('SBERT API is not running at ' . config('services.sbert.url', 'http://127.0.0.1:5050'));
            $this->error('Start it with: .\ocr-env\Scripts\python.exe scripts\sbert_api.py');
            return 1;
        }
        $this->info('SBERT API is available.');

        // Collect all unique skills
        $allSkills = collect();

        // From job postings
        $this->info('Collecting skills from job postings...');
        $jobPostings = JobPosting::whereNotNull('required_skills')->get();
        foreach ($jobPostings as $job) {
            $skills = is_array($job->required_skills) ? $job->required_skills : json_decode($job->required_skills, true);
            if ($skills) {
                $allSkills = $allSkills->merge($skills);
            }
        }
        $this->info("  Found {$jobPostings->count()} job postings");

        // From applications
        $this->info('Collecting skills from applications...');
        $applications = JobApplication::whereNotNull('applicant_profile')->get();
        foreach ($applications as $app) {
            $profile = is_string($app->applicant_profile)
                ? json_decode($app->applicant_profile, true)
                : $app->applicant_profile;
            if (!empty($profile['skills'])) {
                $skills = array_map('trim', explode(',', $profile['skills']));
                $allSkills = $allSkills->merge($skills);
            }
        }
        $this->info("  Found {$applications->count()} applications");

        // Deduplicate
        $uniqueSkills = $allSkills
            ->map(fn($s) => trim($s))
            ->filter(fn($s) => strlen($s) > 0)
            ->unique(fn($s) => strtolower($s))
            ->values()
            ->toArray();

        $this->info("Total unique skills: " . count($uniqueSkills));

        // Check which are already cached
        $cached = DB::table('skill_embeddings')
            ->pluck('text_normalized')
            ->toArray();
        $cachedSet = array_flip($cached);

        $uncached = array_filter($uniqueSkills, fn($s) => !isset($cachedSet[strtolower(trim($s))]));
        $uncached = array_values($uncached);

        $this->info("Already cached: " . count($cached));
        $this->info("Need encoding: " . count($uncached));

        if (empty($uncached)) {
            $this->info('All skills are already cached. Nothing to do.');
        } else {
            // Encode in batches of 50
            $batches = array_chunk($uncached, 50);
            $bar = $this->output->createProgressBar(count($uncached));
            $bar->start();

            foreach ($batches as $batch) {
                $service->getEmbeddings($batch, 'sync');
                $bar->advance(count($batch));
            }

            $bar->finish();
            $this->newLine();
            $this->info('All skills encoded and cached.');
        }

        $totalCached = DB::table('skill_embeddings')->count();
        $this->info("Total embeddings in database: {$totalCached}");

        // Export if requested
        if ($this->option('export')) {
            $this->exportTable();
        } else {
            $this->newLine();
            $this->info('To export for production, run:');
            $this->info('  php artisan embeddings:sync --export');
            $this->info('Or manually: mysqldump -u root YOUR_DB skill_embeddings > skill_embeddings.sql');
        }

        return 0;
    }

    private function exportTable(): void
    {
        $this->info('Exporting skill_embeddings table...');

        $outputPath = base_path('skill_embeddings_export.sql');
        $rows = DB::table('skill_embeddings')->get();

        if ($rows->isEmpty()) {
            $this->warn('No embeddings to export.');
            return;
        }

        $sql = "-- Skill embeddings export: " . now()->toDateTimeString() . "\n";
        $sql .= "-- Total records: " . $rows->count() . "\n\n";
        $sql .= "TRUNCATE TABLE `skill_embeddings`;\n\n";

        foreach ($rows as $row) {
            $text = addslashes($row->text);
            $normalized = addslashes($row->text_normalized);
            $embedding = addslashes($row->embedding);
            $source = addslashes($row->source);
            $sourceId = $row->source_id ?? 'NULL';
            $sourceIdSql = $sourceId === 'NULL' ? 'NULL' : "'{$sourceId}'";
            $createdAt = $row->created_at ?? now();
            $updatedAt = $row->updated_at ?? now();

            $sql .= "INSERT INTO `skill_embeddings` (`text`, `text_normalized`, `embedding`, `source`, `source_id`, `created_at`, `updated_at`) VALUES ('{$text}', '{$normalized}', '{$embedding}', '{$source}', {$sourceIdSql}, '{$createdAt}', '{$updatedAt}');\n";
        }

        file_put_contents($outputPath, $sql);
        $this->info("Exported to: {$outputPath}");
        $this->info("Upload this file to production and run: mysql -u USER -p DATABASE < skill_embeddings_export.sql");
    }
}
