# OCR & Resume Extraction System

This document describes how the Optical Character Recognition (OCR) and resume extraction system works in the Castcrew (OptiCrew) platform, covering the multi-engine pipeline, field extraction logic, and data flow.

---

## 1. Overview

When an applicant uploads a resume (PDF or DOCX), the system automatically extracts structured information to pre-fill the application form. It uses a **multi-engine pipeline** that runs several extraction methods and selects the best result.

### File Size Limits

| File Type | Max Size | Rationale |
|-----------|----------|-----------|
| DOCX | 10 MB | Text-based, extraction is lightweight |
| PDF | 1 MB | Requires OCR processing (cloud API), keep payloads small |

### Pipeline Summary

```
DOCX Upload                          PDF Upload
    │                                     │
    ▼                                     ▼
PHP ZipArchive                      OCR.space Cloud API
(reads word/document.xml)           (Engine 2, cloud OCR)
    │                                     │
    ▼                                     ├── smalot/pdfparser (fallback)
Extract Fields (Regex)              ├── Python PaddleOCR (fallback, requires Poppler)
    │                                     └── PHP FlateDecode (fallback)
    ▼                                     │
Pre-fill Form                            ▼
                                    Score each method → Pick best
                                         │
                                         ▼
                                    Extract Fields (Regex)
                                         │
                                         ▼
                                    Pre-fill Form
```

---

## 2. Extraction Pipeline

### 2.1 Pipeline Architecture

The `ApplicantDashboardController::extractResume()` method orchestrates the pipeline. Each extraction method runs independently, and the method that extracts the most valid fields wins.

**For DOCX files** — only one method is needed:

| Method | Engine | Description |
|--------|--------|-------------|
| DOCX XML | PHP ZipArchive | Opens the DOCX as a ZIP, reads `word/document.xml`, strips XML tags. Fast and reliable. |

**For PDF files** — multiple methods are attempted:

| Priority | Method | Engine | Best For | Status |
|----------|--------|--------|----------|--------|
| 1 | OCR.space API | Cloud OCR (Engine 2) | Scanned PDFs, image-based PDFs, dense text | **Primary** — requires `OCR_SPACE_API_KEY` in `.env` |
| 2 | smalot/pdfparser | PHP PDF library | Text-based PDFs with selectable text | **Fallback** — returns empty for scanned/image PDFs |
| 3 | Python Script | PaddleOCR + Tesseract | Complex layouts, multi-column resumes | **Fallback** — requires Poppler installed and in PATH |
| 4 | PHP FlateDecode | Native PHP | Simple text-based PDFs | **Last resort** — limited format support |

### 2.2 Scoring System

After each method extracts text and parses fields, the system scores the result by counting non-empty extracted fields. The method with the highest score is returned to the frontend.

A readability check filters out garbled output (e.g., from font-encoded PDFs that produce nonsense text). If no method passes readability, the longest raw text candidate is used as a fallback.

### 2.3 Why DOCX is More Reliable

A DOCX file is a ZIP archive containing `word/document.xml` — PHP reads the XML directly without any OCR. This means:
- No external API calls needed
- No Python dependencies needed
- Extraction is instant and deterministic
- All text is preserved exactly as written

For PDF files, the text may be embedded as images (scanned documents), which requires OCR to convert images back to text — an inherently less reliable process.

---

## 3. OCR Engines

### 3.1 OCR.space Cloud API (Primary for PDFs)

**File**: `ApplicantDashboardController::callOcrSpace()`

The primary extraction method for PDFs uses the OCR.space REST API:

- **API Endpoint**: `https://api.ocr.space/parse/image`
- **Engine**: OCR Engine 2 (optimized for dense text like resumes)
- **Configuration**:
  - Language: English
  - Orientation detection: Enabled
  - Scale: Enabled (improves accuracy for low-res scans)
  - Overlay: Disabled (text-only output)
- **Rate Limit**: 25,000 requests/month on the free tier
- **API Key**: Stored in `.env` as `OCR_SPACE_API_KEY`, configured in `config/services.php`
- **Timeout**: 30 seconds

**Process**:
1. Upload the resume file as a multipart form attachment
2. OCR.space processes the file and returns parsed text per page
3. Concatenate all page results into a single text block
4. Pass the text to the field extraction function

### 3.2 DOCX XML Extraction (Primary for DOCX)

**File**: `ApplicantDashboardController::extractDocxText()`

For Word documents, no OCR is needed:

1. Open the DOCX file as a PHP `ZipArchive`
2. Read the `word/document.xml` entry
3. Strip XML tags while preserving paragraph breaks (`<w:p>` → newline)
4. Return clean plain text

This is the fastest and most accurate method since DOCX files store text as structured XML.

### 3.3 smalot/pdfparser (PDF Fallback)

**File**: `ApplicantDashboardController::extractResume()` (inline)

For text-based PDFs that have selectable text (not scanned):

- **Library**: `smalot/pdfparser` (PHP Composer package)
- **Handles**: Font encodings, CMap tables, text extraction from PDF streams
- **Limitation**: Returns empty for scanned/image-based PDFs (no text layer)

### 3.4 PaddleOCR + Tesseract (Local Fallback)

**Files**: `scripts/ocr_extract.py`, `scripts/extract_resume.py`

Local OCR engines that run as Python scripts:

- **PaddleOCR**: Primary local OCR engine (Baidu), with angle classification
- **Tesseract**: Complementary engine, better for certain fonts/layouts
- **PDF Handling**: Uses `pdf2image` to convert PDF pages to 200 DPI PNG images, then runs OCR on each image

**Important**: This method requires **Poppler** (`pdftoppm`) installed and in the system PATH. Without Poppler, `pdf2image` cannot convert PDF pages to images and this method will silently fail.

**Selection Logic**:
- If PaddleOCR produces >50 characters of text, use PaddleOCR
- If PaddleOCR fails or produces insufficient text, use Tesseract
- If both produce results, prefer PaddleOCR (typically higher accuracy)

### 3.5 PHP-Native FlateDecode (Last Resort)

**File**: `ApplicantDashboardController::extractPdfText()`

For simple text-based PDFs:

- Reads raw PDF stream data and decompresses FlateDecode streams
- Parses PDF `Tj` and `TJ` text operators
- Very limited — cannot handle scanned PDFs, complex encodings, or modern PDF features

---

## 4. Named Entity Recognition (NER)

### 4.1 spaCy NER (Python Script Only)

**File**: `scripts/extract_resume.py`

When the Python script path is used, **spaCy** (`en_core_web_sm` model) provides Named Entity Recognition:

| Entity Type | Used For |
|-------------|----------|
| `PERSON` | Extracting applicant name (first, middle, last) |
| `GPE` (Geo-Political Entity) | Detecting country and city names |
| `LOC` (Location) | Detecting geographic locations |

**Name Extraction Process**:
1. Run spaCy NER on the first 3,000 characters of the resume
2. Find the first `PERSON` entity
3. Split into name parts:
   - 1 word: first name only
   - 2 words: first name + last name
   - 3+ words: first name + middle name + last name

**Note**: When OCR.space is used (primary path for PDFs), spaCy NER is not involved. Field extraction relies on regex patterns instead.

### 4.2 Fallback Name Detection (Regex/Heuristic)

When spaCy is unavailable (which is the case for OCR.space and PHP-based extraction):
1. Scan the first 5 lines of the resume text
2. Find a line that doesn't contain emails, phone numbers, or excessive digits
3. Has 2-5 words (typical name length)
4. Split into first/middle/last name parts

---

## 5. Field Extraction (Regex-Based)

### 5.1 Extracted Fields

The system extracts and normalizes the following fields from the raw text:

| Field | Method | Pattern |
|-------|--------|---------|
| `email` | Regex | Standard email pattern (`user@domain.tld`) |
| `alternative_email` | Regex | Second email found in text |
| `phone` | Regex | International phone patterns with optional country code |
| `linkedin` | Regex | `linkedin.com/in/username` URLs |
| `birthdate` | Regex | Multiple date formats (DD/MM/YYYY, YYYY-MM-DD, "Jan 15, 1990") |
| `postal_code` | Regex | Country-specific patterns (US 5-digit, Canada A1A 1A1, UK postcodes, 4-digit AU/PH) |
| `city` | Regex + NER | Labeled keywords ("City:", "Municipality:") or spaCy GPE entities |
| `country` | Regex + NER | Labeled keywords ("Country:", "Nationality:") or matched from 190+ country names |
| `skills` | Regex | Text after "Skills:", "Expertise:", "Competencies:" headers (up to 10 items) |
| `languages` | Regex | Text after "Languages:", "Dialects:" headers (up to 6 items) |
| `first_name` | NER / Heuristic | spaCy PERSON entity or first clean text line |
| `last_name` | NER / Heuristic | From PERSON entity split |
| `middle_name` | NER / Heuristic | Middle part of 3+ word name |

### 5.2 Birthdate Patterns

The system recognizes multiple date formats:

```
DD/MM/YYYY    →  15/03/1995
YYYY-MM-DD    →  1995-03-15
Mon DD, YYYY  →  March 15, 1995
Mon DD YYYY   →  Mar 15 1995
```

### 5.3 Postal Code Patterns

Country-specific postal code detection:

| Country | Pattern | Example |
|---------|---------|---------|
| USA | 5 digits (± 4-digit extension) | 90210, 90210-1234 |
| Canada | Letter-Digit-Letter Digit-Letter-Digit | A1A 1A1 |
| UK | 1-2 letters + digits + space + digit + 2 letters | SW1A 2AA |
| Philippines/Australia | 4 digits | 1234 |

Years (1900-2099) are excluded to avoid false positives from birth dates.

### 5.4 Country Normalization

Common aliases are normalized to standard names:

| Input | Normalized |
|-------|-----------|
| "usa", "us", "u.s.a", "u.s." | United States |
| "uk", "u.k." | United Kingdom |
| "uae" | United Arab Emirates |

---

## 6. PHP-Python Bridge

### 6.1 Script Execution

PHP calls the Python scripts using `proc_open()` (preferred) or `shell_exec()` (fallback):

```
PHP Controller
    │
    ├── proc_open() preferred (captures stdout/stderr separately)
    │   ├── stdin: closed immediately
    │   ├── stdout: captured as extraction result
    │   └── stderr: captured but discarded (logging only)
    │
    └── shell_exec() fallback
        └── Redirects stderr: cmd 2>NUL (Windows) or 2>/dev/null (Linux)
```

**Python Environment**:
- Virtual environment located at `ocr-env/` in the project root
- Python executable: `ocr-env/Scripts/python.exe` (Windows)
- Available packages: `paddleocr`, `pdf2image`
- Not currently installed: `pdfplumber`, `PyPDF2` (not needed — PHP handles these)

### 6.2 Error Handling

- If the Python executable or script is not found, the method returns an empty string (graceful fallback)
- PaddleOCR logging is suppressed (`show_log=False`, environment variables silence GLOG)
- All exceptions in the Python scripts are caught — the script always outputs valid text or nothing
- PHP catches timeouts and exceptions from `proc_open()` / `shell_exec()`

### 6.3 Poppler Dependency

The Python OCR path for PDFs requires **Poppler** (`pdftoppm`/`pdfinfo`) installed on the system:
- `pdf2image` uses Poppler to convert PDF pages to PNG images before OCR
- Without Poppler, this extraction method silently returns empty
- **This is a fallback path** — the primary OCR.space cloud API does not require Poppler

---

## 7. Frontend Integration

### 7.1 Upload & Extraction Flow

The applicant-facing modal handles the OCR flow:

1. **Step 1**: Applicant uploads resume file (DOCX up to 10 MB, or PDF up to 1 MB)
2. **Extraction**: Frontend sends the file to `POST /applicant/apply/extract`
3. **Loading State**: A spinner displays while extraction runs
4. **Step 2**: Extracted fields are auto-populated into the application form
5. **Manual Override**: Applicant can edit any pre-filled field before submission

### 7.2 Field Mapping

The frontend maps extracted fields to form fields with multiple key aliases:

| Form Field | Accepted Keys |
|-----------|--------------|
| First Name | `first_name`, `firstname`, `firstName` |
| Last Name | `last_name`, `lastname`, `lastName` |
| Middle Initial | `middle_name`, `middlename` |
| Birthdate | `birthdate`, `date_of_birth`, `dob` |
| Phone | `phone`, `mobile`, `mobile_number` |
| Email | `email`, `email_address` |
| Country | `country`, `nationality` |
| Region | `region`, `state`, `province` |
| LinkedIn | `linkedin`, `linkedin_url` |

### 7.3 Location Auto-Matching

After extraction, the frontend matches the extracted country against the Country-State-City API:
1. Find the country in the CSC countries list
2. Load states/regions for that country
3. Attempt to match the extracted region/city
4. Populate the corresponding dropdowns

---

## 8. Data Storage

### 8.1 Resume Files

- **Storage Path**: `storage/app/public/job-applications/`
- **Naming**: Random hash with original filename preserved in DB
- **Access**: Authenticated routes for viewing/downloading (`admin.recruitment.view-resume`, `admin.recruitment.download-resume`)

### 8.2 Extracted Profile Data

Parsed resume data is stored as JSON in the `applicant_profile` field of the `job_applications` table:

```json
{
    "first_name": "Maria",
    "last_name": "Santos",
    "middle_name": "Cruz",
    "email": "maria.santos@email.com",
    "phone": "+63 917 123 4567",
    "country": "Philippines",
    "city": "Manila",
    "skills": "Cleaning, Sanitization, Time Management",
    "languages": "English, Filipino, Japanese"
}
```

### 8.3 Additional Documents

The `documents` JSON field stores metadata for additional uploaded files (cover letters, certificates):

```json
[
    {"name": "Cover Letter", "path": "documents/cover_letter_123.pdf", "original_name": "CoverLetter.pdf"},
    {"name": "Certificate", "path": "documents/cert_456.pdf", "original_name": "CleaningCert.pdf"}
]
```

---

## 9. End-to-End Data Flow

### DOCX Flow (Simple)

```
┌─────────────────────────┐
│  Applicant uploads DOCX │
│  (max 10 MB)            │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│  PHP ZipArchive reads   │
│  word/document.xml      │
│  → strips XML tags      │
│  → plain text           │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│  Extract Fields (Regex) │
│  • Name (heuristic)     │
│  • Email, Phone         │
│  • Skills, Languages    │
│  • Country, City        │
└───────────┬─────────────┘
            │
            ▼
┌─────────────────────────┐
│  Return JSON → Pre-fill │
│  application form       │
└─────────────────────────┘
```

### PDF Flow (Multi-Engine)

```
┌─────────────────────────┐
│  Applicant uploads PDF  │
│  (max 1 MB)             │
└───────────┬─────────────┘
            │
            ▼
┌──────────────────────────────────────────────────┐
│  Run all available extraction methods:            │
│                                                    │
│  ① OCR.space Cloud API (primary)                  │
│     └── Sends PDF to cloud, returns parsed text   │
│                                                    │
│  ② smalot/pdfparser (PHP)                         │
│     └── Extracts text from text-based PDFs        │
│                                                    │
│  ③ Python PaddleOCR (if Poppler installed)        │
│     └── Converts pages to images → OCR            │
│                                                    │
│  ④ PHP FlateDecode (native)                       │
│     └── Decompresses PDF streams → extract text   │
└───────────────────┬──────────────────────────────┘
                    │
                    ▼
       ┌────────────────────────┐
       │  Score each method     │
       │  (count extracted      │
       │   non-empty fields)    │
       │                        │
       │  Pick highest score    │
       └────────────┬───────────┘
                    │
                    ▼
       ┌────────────────────────┐
       │  Extract Fields (Regex)│
       └────────────┬───────────┘
                    │
                    ▼
       ┌────────────────────────┐
       │  Return JSON → Pre-fill│
       │  application form      │
       └────────────────────────┘
```

---

## 10. Configuration & Dependencies

### 10.1 Environment Variables

| Variable | Required | Description |
|----------|----------|-------------|
| `OCR_SPACE_API_KEY` | Yes (for PDF extraction) | API key for OCR.space cloud service. Get free key at https://ocr.space/ocrapi (25k requests/month) |

### 10.2 PHP Dependencies (Composer)

| Package | Purpose | Status |
|---------|---------|--------|
| `smalot/pdfparser` | PHP-native PDF text extraction | Installed |
| `illuminate/http` | HTTP client for OCR.space API calls | Installed (Laravel built-in) |

### 10.3 Python Dependencies (ocr-env)

| Package | Purpose | Status |
|---------|---------|--------|
| `paddleocr` | Local OCR engine for images/scanned PDFs | Installed |
| `pdf2image` | PDF to image conversion (requires Poppler) | Installed |
| `spacy` + `en_core_web_sm` | Named Entity Recognition for names/locations | Available in extract_resume.py |
| `pytesseract` | Complementary OCR engine | Available |
| `pdfplumber` | Text-based PDF extraction | Not installed (handled by PHP instead) |
| `PyPDF2` | Text-based PDF extraction | Not installed (handled by PHP instead) |
| `python-docx` | DOCX text extraction | Available as fallback |

### 10.4 System Requirements

| Requirement | Required? | Purpose |
|-------------|-----------|---------|
| `OCR_SPACE_API_KEY` | **Yes** | Primary PDF text extraction via cloud API |
| Python 3.8+ with `ocr-env/` | Optional | Local OCR fallback (PaddleOCR) |
| Poppler (`pdftoppm`) | Optional | Required only if using Python OCR for PDFs (converts PDF to images) |
| Tesseract OCR | Optional | Complementary local OCR engine |

### 10.5 What Works Without External Dependencies

| File Type | Works Without Python/Poppler? | Method Used |
|-----------|-------------------------------|-------------|
| DOCX | Yes | PHP ZipArchive (reads XML directly) |
| PDF (text-based) | Yes | smalot/pdfparser or PHP FlateDecode |
| PDF (scanned/image) | Needs `OCR_SPACE_API_KEY` | OCR.space cloud API |
| PDF (scanned, local) | Needs Python + Poppler | PaddleOCR + pdf2image |
