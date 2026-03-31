#!/usr/bin/env python3
"""
sbert_api.py
Lightweight Flask API for Sentence-BERT (SBERT) embeddings.
Used by Laravel to compute semantic similarity for applicant skill matching.

Usage:
    pip install flask sentence-transformers
    python sbert_api.py

Endpoints:
    GET  /health          — Health check
    POST /embed           — Get embeddings for a list of texts
    POST /similarity      — Compute cosine similarity between query and candidates
"""

import os
import logging

# Silence noisy logs before imports
logging.disable(logging.WARNING)
os.environ.setdefault('TOKENIZERS_PARALLELISM', 'false')

from flask import Flask, request, jsonify
from sentence_transformers import SentenceTransformer
import numpy as np

app = Flask(__name__)

# Load model once at startup (~80MB, ~1s cold start)
MODEL_NAME = 'all-MiniLM-L6-v2'
print(f'Loading SBERT model: {MODEL_NAME}...')
model = SentenceTransformer(MODEL_NAME)
print(f'Model loaded. Embedding dimension: {model.get_sentence_embedding_dimension()}')


@app.route('/health', methods=['GET'])
def health():
    return jsonify({
        'status': 'ok',
        'model': MODEL_NAME,
        'dimension': model.get_sentence_embedding_dimension(),
    })


@app.route('/embed', methods=['POST'])
def embed():
    """
    Get embeddings for a list of texts.

    Request:  { "texts": ["Team Management", "Leadership Skills"] }
    Response: { "embeddings": [[0.23, -0.41, ...], [0.19, -0.38, ...]] }
    """
    data = request.json or {}
    texts = data.get('texts', [])

    if not texts:
        return jsonify({'embeddings': []})

    embeddings = model.encode(texts, normalize_embeddings=True, show_progress_bar=False)
    return jsonify({
        'embeddings': embeddings.tolist()
    })


@app.route('/similarity', methods=['POST'])
def similarity():
    """
    Compute cosine similarity between a query and a list of candidates.

    Request:  { "query": "Team Management", "candidates": ["Leadership", "Cleaning"] }
    Response: { "scores": [0.82, 0.12] }
    """
    data = request.json or {}
    query = data.get('query', '')
    candidates = data.get('candidates', [])

    if not query or not candidates:
        return jsonify({'scores': []})

    query_emb = model.encode([query], normalize_embeddings=True)
    cand_embs = model.encode(candidates, normalize_embeddings=True)

    # Dot product of normalized vectors = cosine similarity
    scores = (cand_embs @ query_emb.T).flatten().tolist()
    return jsonify({'scores': scores})


@app.route('/batch-score', methods=['POST'])
def batch_score():
    """
    Score multiple applicants against job required skills in one call.

    Request: {
        "required_skills": ["Deep Cleaning", "Waste Disposal"],
        "applicants": [
            { "id": 1, "skills": ["Cleaning", "Sanitization"] },
            { "id": 2, "skills": ["Waste Management", "Floor Mopping"] }
        ],
        "threshold": 0.5
    }
    Response: {
        "results": [
            { "id": 1, "score": 72, "matches": [...] },
            { "id": 2, "score": 65, "matches": [...] }
        ]
    }
    """
    data = request.json or {}
    required_skills = data.get('required_skills', [])
    applicants = data.get('applicants', [])
    threshold = data.get('threshold', 0.5)

    if not required_skills or not applicants:
        return jsonify({'results': []})

    # Encode all required skills once
    req_embs = model.encode(required_skills, normalize_embeddings=True, show_progress_bar=False)

    # Collect all unique applicant skills and encode once
    all_skills = set()
    for app in applicants:
        all_skills.update(app.get('skills', []))
    all_skills = list(all_skills)

    if not all_skills:
        return jsonify({'results': [{'id': a.get('id'), 'score': 0, 'matches': []} for a in applicants]})

    skill_embs = model.encode(all_skills, normalize_embeddings=True, show_progress_bar=False)
    skill_map = {s: skill_embs[i] for i, s in enumerate(all_skills)}

    results = []
    for app in applicants:
        app_skills = app.get('skills', [])
        if not app_skills:
            results.append({'id': app.get('id'), 'score': 0, 'matches': []})
            continue

        app_emb_list = np.array([skill_map[s] for s in app_skills if s in skill_map])
        if len(app_emb_list) == 0:
            results.append({'id': app.get('id'), 'score': 0, 'matches': []})
            continue

        matches = []
        total_sim = 0.0

        for i, req_skill in enumerate(required_skills):
            sims = (app_emb_list @ req_embs[i]).flatten()
            best_idx = int(np.argmax(sims))
            best_sim = float(sims[best_idx])

            if best_sim >= threshold:
                matches.append({
                    'required': req_skill,
                    'matched': app_skills[best_idx] if best_idx < len(app_skills) else '',
                    'similarity': round(best_sim, 3),
                })
                total_sim += best_sim

        score = round((total_sim / len(required_skills)) * 100) if required_skills else 0
        results.append({
            'id': app.get('id'),
            'score': score,
            'matches': matches,
        })

    # Sort by score descending
    results.sort(key=lambda x: x['score'], reverse=True)
    return jsonify({'results': results})


if __name__ == '__main__':
    port = int(os.environ.get('SBERT_PORT', 5050))
    print(f'SBERT API running on http://127.0.0.1:{port}')
    app.run(host='127.0.0.1', port=port, debug=False)
