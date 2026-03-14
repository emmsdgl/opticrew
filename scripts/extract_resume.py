#!/usr/bin/env python3
"""
extract_resume.py
Extracts structured applicant information from a resume file (PDF or image)
using PaddleOCR + Tesseract for text extraction and spaCy for NER.

Usage:
    python extract_resume.py <file_path>

Output:
    JSON object with extracted fields printed to stdout.
"""

import sys
import json
import re
import os


def _normalize_country_value(value: str) -> str:
    """Normalize common country aliases for cleaner form population."""
    if not value:
        return ''
    cleaned = re.sub(r'\s+', ' ', value).strip(" \t\r\n,.;:-")
    aliases = {
        'usa': 'United States',
        'us': 'United States',
        'u.s.a': 'United States',
        'u.s.': 'United States',
        'uk': 'United Kingdom',
        'u.k.': 'United Kingdom',
        'uae': 'United Arab Emirates',
    }
    key = cleaned.lower().replace('.', '')
    if key in aliases:
        return aliases[key]
    return cleaned.title()


def extract_text_tesseract(file_path: str) -> str:
    """Extract text using Tesseract OCR as complementary engine."""
    try:
        import pytesseract
        text = pytesseract.image_to_string(file_path, lang='eng')
        return text.strip()
    except Exception:
        return ''


def extract_text(file_path: str) -> str:
    """Extract raw text from a PDF or image file using PaddleOCR + Tesseract."""
    ext = os.path.splitext(file_path)[1].lower()
    paddle_parts = []
    tesseract_parts = []

    # Try PaddleOCR first
    try:
        from paddleocr import PaddleOCR
        ocr = PaddleOCR(use_angle_cls=True, lang='en', show_log=False)

        if ext == '.pdf':
            try:
                from pdf2image import convert_from_path
                import tempfile
                pages = convert_from_path(file_path, dpi=200)
                for page in pages:
                    with tempfile.NamedTemporaryFile(suffix='.png', delete=False) as tmp:
                        page.save(tmp.name, 'PNG')
                        tmp_path = tmp.name
                    result = ocr.ocr(tmp_path, cls=True)
                    # Also try Tesseract on this page
                    tess = extract_text_tesseract(tmp_path)
                    if tess:
                        tesseract_parts.append(tess)
                    os.unlink(tmp_path)
                    if result and result[0]:
                        paddle_parts.extend(line[1][0] for line in result[0])
            except ImportError:
                # pdf2image not installed — fall back to PyPDF2 text extraction
                try:
                    import PyPDF2
                    with open(file_path, 'rb') as f:
                        reader = PyPDF2.PdfReader(f)
                        for page in reader.pages:
                            text = page.extract_text() or ''
                            if text:
                                paddle_parts.append(text)
                except Exception:
                    pass
        else:
            # For images, try both engines
            result = ocr.ocr(file_path, cls=True)
            if result and result[0]:
                paddle_parts.extend(line[1][0] for line in result[0])
            tess = extract_text_tesseract(file_path)
            if tess:
                tesseract_parts.append(tess)

    except ImportError:
        # PaddleOCR not installed — fall back to Tesseract + PyPDF2
        if ext == '.pdf':
            try:
                import PyPDF2
                with open(file_path, 'rb') as f:
                    reader = PyPDF2.PdfReader(f)
                    for page in reader.pages:
                        paddle_parts.append(page.extract_text() or '')
            except Exception:
                pass
        else:
            tess = extract_text_tesseract(file_path)
            if tess:
                tesseract_parts.append(tess)

    # Prefer PaddleOCR if it has reasonable output, otherwise use Tesseract
    paddle_text = '\n'.join(paddle_parts)
    tesseract_text = '\n'.join(tesseract_parts)
    
    if paddle_text.strip() and len(paddle_text.strip()) > 50:
        return paddle_text
    elif tesseract_text.strip() and len(tesseract_text.strip()) > 50:
        return tesseract_text
    else:
        return (paddle_text + '\n' + tesseract_text).strip() if (paddle_text or tesseract_text) else ''


def extract_fields(text: str) -> dict:
    """Extract structured fields from raw resume text."""
    fields = {
        'first_name':        '',
        'last_name':         '',
        'middle_name':       '',
        'birthdate':         '',
        'phone':             '',
        'email':             '',
        'alternative_email': '',
        'home_address':      '',
        'city':              '',
        'country':           '',
        'postal_code':       '',
        'linkedin':          '',
        'skills':            '',
        'languages':         '',
    }

    # ── Country name set for GPE classification ────────────────────────────
    _COUNTRIES = {
        'afghanistan','albania','algeria','andorra','angola','argentina',
        'armenia','australia','austria','azerbaijan','bahamas','bahrain',
        'bangladesh','barbados','belarus','belgium','belize','benin',
        'bhutan','bolivia','bosnia','botswana','brazil','brunei',
        'bulgaria','burkina faso','burundi','cambodia','cameroon','canada',
        'cape verde','chad','chile','china','colombia','comoros',
        'congo','costa rica','croatia','cuba','cyprus','czech republic',
        'czechia','denmark','djibouti','dominica','dominican republic',
        'ecuador','egypt','el salvador','eritrea','estonia','ethiopia',
        'fiji','finland','france','gabon','gambia','georgia','germany',
        'ghana','greece','grenada','guatemala','guinea','guyana','haiti',
        'honduras','hungary','iceland','india','indonesia','iran','iraq',
        'ireland','israel','italy','jamaica','japan','jordan','kazakhstan',
        'kenya','kiribati','north korea','south korea','korea','kuwait',
        'kyrgyzstan','laos','latvia','lebanon','lesotho','liberia','libya',
        'liechtenstein','lithuania','luxembourg','madagascar','malawi',
        'malaysia','maldives','mali','malta','mauritania','mauritius',
        'mexico','moldova','monaco','mongolia','montenegro','morocco',
        'mozambique','myanmar','namibia','nepal','netherlands','new zealand',
        'nicaragua','niger','nigeria','norway','oman','pakistan','palau',
        'panama','papua new guinea','paraguay','peru','philippines',
        'poland','portugal','qatar','romania','russia','rwanda','samoa',
        'san marino','saudi arabia','senegal','serbia','sierra leone',
        'singapore','slovakia','slovenia','solomon islands','somalia',
        'south africa','spain','sri lanka','sudan','suriname','swaziland',
        'sweden','switzerland','syria','taiwan','tajikistan','tanzania',
        'thailand','timor-leste','togo','tonga','trinidad and tobago',
        'tunisia','turkey','turkmenistan','tuvalu','uganda','ukraine',
        'united arab emirates','uae','united kingdom','uk','great britain',
        'united states','usa','us','america','uruguay','uzbekistan',
        'vanuatu','venezuela','vietnam','yemen','zambia','zimbabwe',
    }

    if not text:
        return fields

    # ── Email ──────────────────────────────────────────────────────────────
    emails = re.findall(r'[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}', text)
    if emails:
        fields['email'] = emails[0]
    if len(emails) > 1:
        fields['alternative_email'] = emails[1]

    # ── Phone ──────────────────────────────────────────────────────────────
    phones = re.findall(
        r'(?:\+?\d{1,3}[\s\-.]?)?\(?\d{3,4}\)?[\s\-.]?\d{3,4}[\s\-.]?\d{4}', text
    )
    if phones:
        fields['phone'] = phones[0].strip()

    # ── LinkedIn ───────────────────────────────────────────────────────────
    linkedin = re.findall(r'linkedin\.com/in/[\w\-]+', text, re.IGNORECASE)
    if linkedin:
        fields['linkedin'] = 'https://www.' + linkedin[0]

    # ── Birthdate ──────────────────────────────────────────────────────────
    dob_patterns = [
        r'\b\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}\b',
        r'\b\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}\b',
        r'\b(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\.?\s+\d{1,2},?\s+\d{4}\b',
    ]
    for pattern in dob_patterns:
        match = re.search(pattern, text, re.IGNORECASE)
        if match:
            fields['birthdate'] = match.group(0)
            break

    # ── Postal Code (regex) ────────────────────────────────────────────────
    # Labelled first, then bare standalone number patterns
    _postal_patterns = [
        r'(?:zip(?:\s*code)?|postal(?:\s*code)?|postcode)[:\s#]+([A-Za-z0-9][A-Za-z0-9 \-]{2,9})',
        r'\b([A-Z]\d[A-Z]\s?\d[A-Z]\d)\b',            # Canada: A1A 1A1
        r'\b([A-Z]{1,2}\d{1,2}[A-Z]?\s?\d[A-Z]{2})\b', # UK: SW1A 2AA
        r'\b(\d{5}(?:[- ]\d{4})?)\b',                   # US: 90210 / 90210-1234
        r'\b(\d{4})\b',                                  # PH / AU / NZ 4-digit
    ]
    for _pp in _postal_patterns:
        _pm = re.search(_pp, text, re.IGNORECASE)
        if _pm:
            _candidate = _pm.group(1).strip()
            # Skip obvious years (1900-2099) and birth-date fragments
            if not re.match(r'^(19|20)\d{2}$', _candidate):
                fields['postal_code'] = _candidate
                break

    # ── City / Country (labeled keywords in resume text) ──────────────────
    _city_m = re.search(
        r'(?:^|\n)\s*(?:city|municipality|town)\s*[:\-]\s*([A-Za-z][\w\s,\.]{1,40}?)(?:\n|,|\||$)',
        text, re.IGNORECASE | re.MULTILINE,
    )
    if _city_m:
        fields['city'] = _city_m.group(1).strip().rstrip(',').strip()

    # Support explicit country hints from common resume labels.
    _country_label_patterns = [
        r'(?:^|\n)\s*(?:country|country\s*of\s*residence|nationality|citizenship|residence)\s*[:\-]\s*([A-Za-z][A-Za-z\s\.-]{1,40}?)(?:\n|,|\||$)',
        r'(?:^|\n)\s*(?:based\s*in|located\s*in|from)\s*[:\-]?\s*([A-Za-z][A-Za-z\s\.-]{1,40}?)(?:\n|,|\||$)',
    ]
    for _pattern in _country_label_patterns:
        _country_m = re.search(_pattern, text, re.IGNORECASE | re.MULTILINE)
        if _country_m:
            _country_candidate = _country_m.group(1).strip().rstrip(',').strip()
            if _country_candidate:
                fields['country'] = _normalize_country_value(_country_candidate)
                break

    # If not explicitly labelled, infer from known country names in location-like lines.
    if not fields['country']:
        _loc_lines = [ln.strip() for ln in text.split('\n') if ln.strip()]
        _loc_lines = _loc_lines[:40]
        _country_candidates = sorted(_COUNTRIES, key=len, reverse=True)
        for _line in _loc_lines:
            _line_lower = _line.lower()
            if '@' in _line_lower:
                continue
            if not re.search(r'address|location|residence|based|from|city|country|nationality|citizenship', _line_lower):
                continue
            for _country in _country_candidates:
                if re.search(r'(?<![A-Za-z])' + re.escape(_country) + r'(?![A-Za-z])', _line_lower):
                    fields['country'] = _normalize_country_value(_country)
                    break
            if fields['country']:
                break

    # ── Name + Locations (spaCy NER) ───────────────────────────────────────
    try:
        import spacy
        nlp = spacy.load('en_core_web_sm')
        doc = nlp(text[:3000])

        persons = [ent.text.strip() for ent in doc.ents if ent.label_ == 'PERSON']
        if persons:
            parts = persons[0].split()
            fields['first_name'] = parts[0] if parts else ''
            if len(parts) >= 3:
                fields['middle_name'] = parts[1]
                fields['last_name']   = ' '.join(parts[2:])
            elif len(parts) == 2:
                fields['last_name'] = parts[1]

        locations = [ent.text.strip() for ent in doc.ents if ent.label_ in ('GPE', 'LOC')]
        for loc in locations:
            if loc.lower() in _COUNTRIES:
                if not fields['country']:
                    fields['country'] = _normalize_country_value(loc)
            else:
                if not fields['city']:
                    fields['city'] = loc

    except Exception:
        # spaCy unavailable — fall back to first non-email, non-phone line
        lines = [ln.strip() for ln in text.split('\n') if ln.strip()]
        for line in lines[:5]:
            if not re.search(r'[@\d\(\)\+]', line) and 1 < len(line.split()) <= 5:
                parts = line.split()
                fields['first_name'] = parts[0]
                fields['last_name']  = parts[-1] if len(parts) > 1 else ''
                if len(parts) == 3:
                    fields['middle_name'] = parts[1]
                break

    # ── Skills ─────────────────────────────────────────────────────────────
    skills_match = re.search(
        r'(?:skills?|expertise|competencies|proficiencies)[:\s\n]+(.+?)(?:\n{2,}|\Z)',
        text, re.IGNORECASE | re.DOTALL
    )
    if skills_match:
        raw   = skills_match.group(1)
        items = re.split(r'[,\n•|\-–]+', raw)
        clean = [s.strip() for s in items if 2 < len(s.strip()) < 50][:10]
        fields['skills'] = ', '.join(clean)

    # ── Languages ──────────────────────────────────────────────────────────
    lang_match = re.search(
        r'(?:languages?|dialects?)[:\s\n]+(.+?)(?:\n{2,}|\Z)',
        text, re.IGNORECASE | re.DOTALL
    )
    if lang_match:
        raw   = lang_match.group(1)
        items = re.split(r'[,\n•|\-–]+', raw)
        clean = [l.strip() for l in items if 1 < len(l.strip()) < 30][:6]
        fields['languages'] = ', '.join(clean)

    return fields


if __name__ == '__main__':
    if len(sys.argv) < 2:
        print(json.dumps({}))
        sys.exit(0)

    path = sys.argv[1]

    if not os.path.exists(path):
        print(json.dumps({'error': 'File not found'}))
        sys.exit(0)

    raw_text = extract_text(path)
    result   = extract_fields(raw_text)
    print(json.dumps(result, ensure_ascii=False))
