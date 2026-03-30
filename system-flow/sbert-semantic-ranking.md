# SBERT Semantic Ranking for Applicant Matching

This document outlines the implementation plan for replacing the current fuzzy substring matching with Sentence-BERT (SBERT) semantic matching for ranking job applicants against job posting requirements.

---

## 1. Problem with Current Approach

The current ranking system uses **fuzzy substring matching**:

```
Score = (matched skills / total required skills) x 100%
```

Where a "match" is determined by checking if one string contains the other:
```javascript
aSkill.includes(reqSkill) || reqSkill.includes(aSkill)
```

**Limitations**:
- "Team Management" does NOT match "Leadership" (semantically similar but no substring overlap)
- "Microsoft Office Word" does NOT match "MS Word" or "Word Processing"
- "Problem-solving" does NOT match "Analyti
cal Thinking" (related competencies)
- "CRM systems" does NOT match "Salesforce" or "Customer Relationship Management"
- No understanding of skill hierarchy or relatedness

---

## 2. Solution: SBERT Semantic Matching

Sentence-BERT converts text into **dense vector embeddings** where semantically similar phrases have high cosine similarity, regardless of exact wording.

```
"Team Management"  →  [0.23, -0.41, 0.67, ...]  ─┐
                                                     ├── cosine similarity: 0.82 (high)
"Leadership Skills" →  [0.19, -0.38, 0.71, ...]  ─┘

"Team Management"  →  [0.23, -0.41, 0.67, ...]  ─┐
                                                     ├── cosine similarity: 0.12 (low)
"Carpet Cleaning"  →  [-0.55, 0.33, -0.21, ...] ─┘
```

---

## 3. Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    SBERT PIPELINE                             │
│                                                               │
│  ┌──────────────┐    ┌──────────────┐    ┌───────────────┐  │
│  │ Python SBERT │    │   Laravel    │    │   Database    │  │
│  │ API Service  │◄──►│  Controller  │◄──►│  Embeddings   │  │
│  │ (Flask)      │    │              │    │  Table        │  │
│  └──────────────┘    └──────┬───────┘    └───────────────┘  │
│                              │                                │
│                    ┌─────────▼──────────┐                    │
│                    │  Cosine Similarity  │                    │
│                    │  Computation (PHP)  │                    │
│                    └─────────┬──────────┘                    │
│                              │                                │
│                    ┌─────────▼──────────┐                    │
│                    │  Applicant Ranking  │                    │
│                    │  (Admin Dashboard)  │                    │
│                    └────────────────────┘                    │
└─────────────────────────────────────────────────────────────┘
```

---

## 4. Implementation Steps

### Step 1: Create SBERT Python API Service

**File**: `scripts/sbert_api.py`

A lightweight Flask API that accepts text and returns embeddings.

```python
# scripts/sbert_api.py

from flask import Flask, request, jsonify
from sentence_transformers import SentenceTransformer

app = Flask(__name__)
model = SentenceTransformer('all-MiniLM-L6-v2')  # Fast, 384-dim embeddings

@app.route('/embed', methods=['POST'])
def embed():
    """
    Accept a list of texts and return their embeddings.

    Request body:
        { "texts": ["Team Management", "Leadership Skills", ...] }

    Response:
        { "embeddings": [[0.23, -0.41, ...], [0.19, -0.38, ...], ...] }
    """
    data = request.json
    texts = data.get('texts', [])

    if not texts:
        return jsonify({'embeddings': []})

    embeddings = model.encode(texts, normalize_embeddings=True)
    return jsonify({
        'embeddings': embeddings.tolist()
    })

@app.route('/similarity', methods=['POST'])
def similarity():
    """
    Compute cosine similarity between a query and a list of candidates.

    Request body:
        {
            "query": "Team Management",
            "candidates": ["Leadership", "Cleaning", "Supervision"]
        }

    Response:
        {
            "scores": [0.82, 0.12, 0.76]
        }
    """
    data = request.json
    query = data.get('query', '')
    candidates = data.get('candidates', [])

    if not query or not candidates:
        return jsonify({'scores': []})

    query_embedding = model.encode([query], normalize_embeddings=True)
    candidate_embeddings = model.encode(candidates, normalize_embeddings=True)

    # Cosine similarity (embeddings are already normalized, so dot product = cosine sim)
    scores = (candidate_embeddings @ query_embedding.T).flatten().tolist()
    return jsonify({'scores': scores})

@app.route('/health', methods=['GET'])
def health():
    return jsonify({'status': 'ok', 'model': 'all-MiniLM-L6-v2'})

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5050, debug=False)
```

**Python Dependencies** (add to `requirements.txt`):
```
flask
sentence-transformers
torch
```

**Model Choice**: `all-MiniLM-L6-v2`
- 384-dimensional embeddings
- ~80MB model size
- Fast inference (~5ms per sentence on CPU)
- Good balance of speed and accuracy for skill matching

---

### Step 2: Database Migration for Embeddings

**File**: `database/migrations/xxxx_create_skill_embeddings_table.php`

```php
Schema::create('skill_embeddings', function (Blueprint $table) {
    $table->id();
    $table->string('text');              // Original skill text
    $table->string('text_normalized');   // Lowercase, trimmed
    $table->binary('embedding');         // 384-dim float vector (serialized)
    $table->string('source');            // 'job_posting' or 'applicant'
    $table->unsignedBigInteger('source_id')->nullable(); // FK to job_postings or job_applications
    $table->timestamps();

    $table->index('text_normalized');
    $table->index(['source', 'source_id']);
});
```

**Why store embeddings?**
- Avoid re-computing embeddings for the same skill text
- Cache embeddings for job posting skills (rarely change)
- Only compute new embeddings for incoming applicant skills
- Reduces SBERT API calls significantly

---

### Step 3: Laravel Service for Embedding Management

**File**: `app/Services/Embedding/EmbeddingService.php`

```php
<?php

namespace App\Services\Embedding;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class EmbeddingService
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.sbert.url', 'http://127.0.0.1:5050');
    }

    /**
     * Get embeddings for a list of texts.
     * Uses cache/DB first, calls SBERT API only for uncached texts.
     */
    public function getEmbeddings(array $texts, string $source = 'general', ?int $sourceId = null): array
    {
        $results = [];
        $uncached = [];

        foreach ($texts as $text) {
            $normalized = strtolower(trim($text));
            $cached = $this->getCachedEmbedding($normalized);
            if ($cached !== null) {
                $results[$text] = $cached;
            } else {
                $uncached[] = $text;
            }
        }

        // Fetch uncached embeddings from SBERT API
        if (!empty($uncached)) {
            $response = Http::timeout(30)->post("{$this->apiUrl}/embed", [
                'texts' => $uncached,
            ]);

            if ($response->successful()) {
                $embeddings = $response->json('embeddings', []);
                foreach ($uncached as $i => $text) {
                    if (isset($embeddings[$i])) {
                        $results[$text] = $embeddings[$i];
                        $this->cacheEmbedding($text, $embeddings[$i], $source, $sourceId);
                    }
                }
            }
        }

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

        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $dot   += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        $denominator = sqrt($normA) * sqrt($normB);
        return $denominator > 0 ? $dot / $denominator : 0.0;
    }

    /**
     * Score an applicant's skills against a job posting's required skills.
     * Returns a 0-100 score based on semantic similarity.
     */
    public function scoreApplicant(array $requiredSkills, array $applicantSkills): array
    {
        if (empty($requiredSkills) || empty($applicantSkills)) {
            return ['score' => 0, 'matches' => []];
        }

        $reqEmbeddings = $this->getEmbeddings($requiredSkills, 'job_posting');
        $appEmbeddings = $this->getEmbeddings($applicantSkills, 'applicant');

        $matches = [];
        $totalScore = 0.0;
        $threshold = 0.5; // Minimum similarity to count as a match

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
                    'required'  => $reqSkill,
                    'matched'   => $bestMatch,
                    'score'     => round($bestSim * 100),
                ];
                $totalScore += $bestSim;
            }
        }

        $overallScore = count($requiredSkills) > 0
            ? round(($totalScore / count($requiredSkills)) * 100)
            : 0;

        return [
            'score'   => $overallScore,
            'matches' => $matches,
        ];
    }

    private function getCachedEmbedding(string $normalized): ?array
    {
        $row = DB::table('skill_embeddings')
            ->where('text_normalized', $normalized)
            ->first();

        if ($row) {
            return unserialize($row->embedding);
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
                'embedding'  => serialize($embedding),
                'source'     => $source,
                'source_id'  => $sourceId,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
```

---

### Step 4: API Route for Ranking

**File**: `routes/web.php` (add to admin routes)

```php
Route::get('/admin/job-postings/{id}/rank-applicants', [JobPostingController::class, 'rankApplicants'])
    ->name('admin.job-postings.rank');
```

**File**: `app/Http/Controllers/Admin/JobPostingController.php` (add method)

```php
public function rankApplicants($id)
{
    $job = JobPosting::findOrFail($id);
    $requiredSkills = $job->required_skills ?? [];

    $applications = JobApplication::where('job_title', $job->title)->get();

    $embeddingService = new \App\Services\Embedding\EmbeddingService();
    $ranked = [];

    foreach ($applications as $app) {
        $profile = json_decode($app->applicant_profile, true) ?? [];
        $applicantSkills = array_filter(
            array_map('trim', explode(',', $profile['skills'] ?? '')),
            fn($s) => strlen($s) > 0
        );

        $result = $embeddingService->scoreApplicant($requiredSkills, $applicantSkills);

        $ranked[] = [
            'application_id' => $app->id,
            'email'          => $app->email,
            'score'          => $result['score'],
            'matches'        => $result['matches'],
            'total_skills'   => count($applicantSkills),
        ];
    }

    usort($ranked, fn($a, $b) => $b['score'] - $a['score']);

    return response()->json(['ranked' => $ranked]);
}
```

---

### Step 5: Update Frontend Ranking

**File**: `resources/views/admin/recruitment/index.blade.php`

Replace the current `calculateRanking()` JavaScript function to call the API:

```javascript
async calculateRanking(job) {
    if (!job.id) return [];

    try {
        const response = await fetch(`/admin/job-postings/${job.id}/rank-applicants`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();

        if (!data.ranked) return [];

        // Map ranked results to the existing applicant data
        return data.ranked.map(r => {
            const app = allApps.find(a => a.id === r.application_id);
            if (!app) return null;
            return {
                ...app,
                score: r.score,
                matchedSkills: r.matches.map(m => m.required),
                semanticMatches: r.matches, // detailed match info
            };
        }).filter(Boolean);
    } catch (e) {
        console.error('Ranking failed, falling back to fuzzy match', e);
        return this.calculateRankingFuzzy(job); // fallback to current method
    }
},

// Keep current method as fallback
calculateRankingFuzzy(job) {
    // ... existing substring matching code ...
}
```

---

### Step 6: Configuration

**File**: `config/services.php` (add)

```php
'sbert' => [
    'url' => env('SBERT_API_URL', 'http://127.0.0.1:5050'),
],
```

**File**: `.env` (add)

```
SBERT_API_URL=http://127.0.0.1:5050
```

---

## 5. Embedding Lifecycle

### When Embeddings Are Generated

| Event | Skills Embedded | Source |
|-------|----------------|--------|
| Job posting created/updated | Required skills | `job_posting` |
| Resume extracted (OCR) | Applicant skills from resume | `applicant` |
| Admin views job posting drawer | Both (if not cached) | On-demand |

### Caching Strategy

```
Request: "Team Management"
    │
    ├── Check DB: skill_embeddings WHERE text_normalized = 'team management'
    │   ├── Found → Return cached embedding (0ms)
    │   └── Not found ↓
    │
    ├── Call SBERT API: POST /embed { texts: ["Team Management"] }
    │   └── Returns [0.23, -0.41, 0.67, ...] (~5ms)
    │
    └── Store in DB for future use
```

After initial population, most ranking requests use **only cached embeddings** — no SBERT API calls needed.

---

## 6. Scoring Algorithm

### Per-Skill Matching

For each required skill in the job posting:
1. Compute cosine similarity against ALL applicant skills
2. Take the **highest similarity** as the best match
3. If best match >= threshold (0.5), count as matched

### Overall Score

```
Overall Score = (Σ best_similarity_per_required_skill / total_required_skills) × 100
```

### Threshold Calibration

| Cosine Similarity | Interpretation | Example |
|------------------|----------------|---------|
| 0.90 - 1.00 | Near-identical | "Deep Cleaning" ↔ "Deep Cleaning Services" |
| 0.70 - 0.89 | Strong match | "Team Management" ↔ "Leadership & Supervision" |
| 0.50 - 0.69 | Related | "CRM Systems" ↔ "Salesforce Administration" |
| 0.30 - 0.49 | Weak relation | "Cleaning" ↔ "Maintenance" |
| 0.00 - 0.29 | Unrelated | "Cleaning" ↔ "Software Development" |

Default threshold: **0.5** (configurable)

---

## 7. Comparison: Fuzzy vs SBERT

| Scenario | Fuzzy Match | SBERT Match |
|----------|------------|-------------|
| "Deep Cleaning" vs "Deep Cleaning" | 100% (exact) | ~98% |
| "CRM systems" vs "Salesforce" | 0% (no overlap) | ~72% |
| "Team Management" vs "Leadership" | 0% (no overlap) | ~81% |
| "MS Word" vs "Microsoft Office Word" | 100% (substring) | ~89% |
| "Problem-solving" vs "Analytical Thinking" | 0% (no overlap) | ~67% |
| "Cleaning" vs "Janitorial Services" | 0% (no overlap) | ~76% |

---

## 8. Files to Create/Modify

### New Files

| File | Purpose |
|------|---------|
| `scripts/sbert_api.py` | Flask API wrapping SBERT model |
| `scripts/requirements-sbert.txt` | Python dependencies |
| `app/Services/Embedding/EmbeddingService.php` | Laravel service for embeddings |
| `database/migrations/xxxx_create_skill_embeddings_table.php` | Embeddings storage |

### Modified Files

| File | Change |
|------|--------|
| `config/services.php` | Add `sbert.url` config |
| `.env` | Add `SBERT_API_URL` |
| `app/Http/Controllers/Admin/JobPostingController.php` | Add `rankApplicants()` method |
| `routes/web.php` | Add ranking API route |
| `resources/views/admin/recruitment/index.blade.php` | Update `calculateRanking()` to use API with fuzzy fallback |

---

## 9. Deployment

### Local Development

```bash
# 1. Install SBERT dependencies
cd scripts
pip install -r requirements-sbert.txt

# 2. Start SBERT API (separate terminal)
python sbert_api.py
# → Running on http://127.0.0.1:5050

# 3. Run migration
php artisan migrate

# 4. Test
curl http://127.0.0.1:5050/health
# → {"status": "ok", "model": "all-MiniLM-L6-v2"}
```

### Production

```bash
# Run SBERT API as a background service (systemd, supervisor, or PM2)
# Example with supervisor:
[program:sbert-api]
command=/path/to/python /path/to/scripts/sbert_api.py
autostart=true
autorestart=true
stdout_logfile=/var/log/sbert-api.log
```

### Fallback Behavior

If the SBERT API is unavailable:
- The frontend catches the error and falls back to the current fuzzy substring matching
- No user-facing errors — ranking still works, just with lower accuracy
- A log warning is recorded for the admin to investigate

---

## 10. Performance Expectations

| Operation | Time | Notes |
|-----------|------|-------|
| SBERT API embed (1 skill) | ~5ms | CPU inference |
| SBERT API embed (20 skills) | ~15ms | Batched |
| DB embedding lookup | <1ms | Indexed query |
| Cosine similarity (PHP, 384-dim) | <0.01ms | Pure math |
| Full ranking (10 applicants, 6 skills each) | ~20ms first time | Subsequent: ~2ms (cached) |

First-time ranking for a job posting triggers SBERT API calls. After that, all embeddings are cached in DB and ranking is purely PHP math — effectively instant.

Admin clicks on a job posting → fuzzy ranking shows immediately
In the background, the API calls SBERT for semantic ranking
Once SBERT responds, the ranking updates in-place with better scores
If SBERT API is down, fuzzy results remain — no errors shown
Embeddings are cached in DB — second load is instant

### Setup summary
Locally (no changes needed)
Your .env already has SBERT_API_URL=http://127.0.0.1:5050. Just add:

SBERT_DRIVER=local
Then run the Flask API as before (python scripts/sbert_api.py).

On Hostinger (deployed)
Add these 2 lines to your production .env via the Hostinger dashboard:

SBERT_DRIVER=huggingface
HUGGINGFACE_TOKEN=hf_xxxxxxxxxxxxxxxxx
To get the token:

Create a free account at huggingface.co
Go to Settings > Access Tokens
Create a token (read access is enough)
That's it — no Python, no VPS, no extra server. The HuggingFace free tier gives you ~30k requests/month, and since embeddings are cached in your database, each unique skill only hits the API once. After the first ranking, everything is served from cache.

The first time someone ranks applicants for a job posting, say the required skill is "Team Management":

Laravel checks the skill_embeddings table → not found
Laravel calls HuggingFace API → gets the embedding (a list of 384 numbers)
Laravel saves that embedding to your database → now it's cached
The second time anyone needs "Team Management":

Laravel checks the skill_embeddings table → found!
Uses the cached embedding directly → no API call needed
So if your job postings use 50 unique skills and applicants have 200 unique skills total, that's only 250 API calls ever. After that, all ranking is just math in PHP using the cached numbers — HuggingFace is never called again for those skills.

The 30k/month free limit is way more than enough since you're only paying for new, never-seen-before skills, not every ranking request.

TL;DR: HuggingFace is only used once per unique skill text. After that, your database has the data and does the work itself.

