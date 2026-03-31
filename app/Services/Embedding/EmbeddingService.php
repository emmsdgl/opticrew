<?php

namespace App\Services\Embedding;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    private string $driver;   // 'local' or 'huggingface'
    private string $apiUrl;
    private ?string $hfToken;

    public function __construct()
    {
        $this->driver  = config('services.sbert.driver', 'local');
        $this->apiUrl  = config('services.sbert.url', 'http://127.0.0.1:5050');
        $this->hfToken = config('services.sbert.hf_token');
    }

    /**
     * Check if the embedding backend is available.
     */
    public function isAvailable(): bool
    {
        try {
            if ($this->driver === 'huggingface') {
                return !empty($this->hfToken);
            }
            $response = Http::timeout(2)->get("{$this->apiUrl}/health");
            return $response->successful() && ($response->json('status') === 'ok');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get embeddings for a list of texts.
     * Uses DB cache first, calls SBERT API only for uncached texts.
     */
    public function getEmbeddings(array $texts, string $source = 'general', ?int $sourceId = null): array
    {
        $results = [];
        $uncached = [];

        foreach ($texts as $text) {
            $normalized = strtolower(trim($text));
            if ($normalized === '') continue;

            $cached = $this->getCachedEmbedding($normalized);
            if ($cached !== null) {
                $results[$text] = $cached;
            } else {
                $uncached[] = $text;
            }
        }

        if (!empty($uncached)) {
            try {
                $embeddings = $this->fetchEmbeddings($uncached);
                foreach ($uncached as $i => $text) {
                    if (isset($embeddings[$i])) {
                        $results[$text] = $embeddings[$i];
                        $this->cacheEmbedding($text, $embeddings[$i], $source, $sourceId);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('SBERT embed call failed', ['error' => $e->getMessage()]);
            }
        }

        return $results;
    }

    /**
     * Fetch embeddings from the configured backend (local Flask API or HuggingFace).
     */
    private function fetchEmbeddings(array $texts): array
    {
        if ($this->driver === 'huggingface') {
            return $this->fetchFromHuggingFace($texts);
        }

        $response = Http::timeout(15)->post("{$this->apiUrl}/embed", [
            'texts' => $texts,
        ]);

        return $response->successful() ? $response->json('embeddings', []) : [];
    }

    /**
     * Call HuggingFace Inference API for embeddings.
     * Uses the same all-MiniLM-L6-v2 model as the local Flask API.
     */
    private function fetchFromHuggingFace(array $texts): array
    {
        $url = 'https://api-inference.huggingface.co/pipeline/feature-extraction/sentence-transformers/all-MiniLM-L6-v2';

        $response = Http::timeout(30)
            ->withHeaders(['Authorization' => "Bearer {$this->hfToken}"])
            ->post($url, ['inputs' => $texts, 'options' => ['wait_for_model' => true]]);

        if (!$response->successful()) {
            Log::warning('HuggingFace API failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return [];
        }

        return $response->json() ?? [];
    }

    /**
     * Score an applicant's skills against a job posting's required skills.
     * Returns a 0-100 score and detailed match info.
     */
    public function scoreApplicant(array $requiredSkills, array $applicantSkills, float $threshold = 0.5): array
    {
        if (empty($requiredSkills) || empty($applicantSkills)) {
            return ['score' => 0, 'matches' => []];
        }

        $reqEmbeddings = $this->getEmbeddings($requiredSkills, 'job_posting');
        $appEmbeddings = $this->getEmbeddings($applicantSkills, 'applicant');

        $matches = [];
        $totalScore = 0.0;

        foreach ($requiredSkills as $reqSkill) {
            if (!isset($reqEmbeddings[$reqSkill])) continue;

            $bestSim = 0.0;
            $bestMatch = '';

            foreach ($applicantSkills as $appSkill) {
                if (!isset($appEmbeddings[$appSkill])) continue;

                $sim = self::cosineSimilarity($reqEmbeddings[$reqSkill], $appEmbeddings[$appSkill]);
                if ($sim > $bestSim) {
                    $bestSim = $sim;
                    $bestMatch = $appSkill;
                }
            }

            if ($bestSim >= $threshold) {
                $matches[] = [
                    'required'   => $reqSkill,
                    'matched'    => $bestMatch,
                    'similarity' => round($bestSim, 3),
                ];
                $totalScore += $bestSim;
            }
        }

        $overallScore = count($requiredSkills) > 0
            ? (int) round(($totalScore / count($requiredSkills)) * 100)
            : 0;

        return [
            'score'   => $overallScore,
            'matches' => $matches,
        ];
    }

    /**
     * Batch score multiple applicants using the SBERT batch endpoint.
     * More efficient than scoring one at a time — single API call encodes all skills at once.
     */
    public function batchScore(array $requiredSkills, array $applicants, float $threshold = 0.5): array
    {
        if (empty($requiredSkills) || empty($applicants)) {
            return [];
        }

        // Local Flask API has a batch-score endpoint; HuggingFace does not
        if ($this->driver === 'local') {
            try {
                $response = Http::timeout(30)->post("{$this->apiUrl}/batch-score", [
                    'required_skills' => $requiredSkills,
                    'applicants'      => $applicants,
                    'threshold'       => $threshold,
                ]);

                if ($response->successful()) {
                    return $response->json('results', []);
                }
            } catch (\Throwable $e) {
                Log::warning('SBERT batch-score call failed, falling back to individual scoring', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Individual scoring (uses cached embeddings, works with any driver)
        $results = [];
        foreach ($applicants as $app) {
            $result = $this->scoreApplicant($requiredSkills, $app['skills'] ?? [], $threshold);
            $result['id'] = $app['id'] ?? null;
            $results[] = $result;
        }
        usort($results, fn($a, $b) => $b['score'] - $a['score']);
        return $results;
    }

    /**
     * Compute cosine similarity between two embedding vectors.
     */
    public static function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        $len = min(count($a), count($b));

        for ($i = 0; $i < $len; $i++) {
            $dot   += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        $denominator = sqrt($normA) * sqrt($normB);
        return $denominator > 0 ? $dot / $denominator : 0.0;
    }

    private function getCachedEmbedding(string $normalized): ?array
    {
        $row = DB::table('skill_embeddings')
            ->where('text_normalized', $normalized)
            ->first();

        if ($row && $row->embedding) {
            return json_decode($row->embedding, true);
        }
        return null;
    }

    private function cacheEmbedding(string $text, array $embedding, string $source, ?int $sourceId): void
    {
        $normalized = strtolower(trim($text));

        DB::table('skill_embeddings')->updateOrInsert(
            ['text_normalized' => $normalized],
            [
                'text'       => $text,
                'embedding'  => json_encode($embedding),
                'source'     => $source,
                'source_id'  => $sourceId,
                'updated_at' => now(),
                'created_at' => DB::raw('COALESCE(created_at, NOW())'),
            ]
        );
    }
}
