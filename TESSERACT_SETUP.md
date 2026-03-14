# Tesseract OCR Setup Guide

This project now uses both **PaddleOCR** and **Tesseract OCR** for improved accuracy in resume extraction. Tesseract is optional but recommended.

## Installation

### Windows

1. **Download the installer** from [UB Mannheim's Tesseract repository](https://github.com/UB-Mannheim/tesseract/wiki):
   - Latest version (e.g., `tesseract-ocr-w64-setup-v5.x.x.exe`)

2. **Run the installer** and note the installation path (default: `C:\Program Files\Tesseract-OCR`)

3. **Install Python bindings** in your ocr-env:
   ```bash
   # Activate the ocr-env if not already activated
   & d:\xampp\htdocs\opticrew\ocr-env\Scripts\Activate.ps1

   # Install pytesseract
   pip install pytesseract
   ```

4. **Set environment variable** (optional but recommended):
   - Go to System Environment Variables
   - Add `TESSDATA_PREFIX = C:\Program Files\Tesseract-OCR\tessdata`
   - Or the script will automatically find it in common locations

### Linux (Ubuntu/Debian)

```bash
sudo apt-get install tesseract-ocr
pip install pytesseract
```

### macOS

```bash
brew install tesseract
pip install pytesseract
```

## Verification

To verify Tesseract is working:

```python
import pytesseract
from PIL import Image

# Test with a simple image
text = pytesseract.image_to_string("path/to/test-image.png")
print(text)
```

## How It Works

- **PaddleOCR**: Fast, deep learning-based OCR. Primary engine used for resume extraction.
- **Tesseract**: Complementary OCR engine. Used when:
  - PaddleOCR returns less than 50 characters
  - Tesseract has more content
  - Both engines work together for maximum accuracy

The script automatically handles cases where Tesseract is not installed—it will simply fall back to PaddleOCR only.

## Performance Impact

- First run: Tesseract initialization adds ~200-300ms
- Subsequent runs: Cached model files make it faster
- Total extraction time: Usually 1-3 seconds per page (PDF)

## Troubleshooting

### "TesseractNotFoundError"

This means pytesseract can't find the Tesseract executable. Solutions:

1. **Windows**: Set the path explicitly in your Python code:
   ```python
   import pytesseract
   pytesseract.pytesseract.pytesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract.exe'
   ```

2. **Linux/Mac**: Ensure Tesseract is in your PATH:
   ```bash
   which tesseract  # Should return the path
   ```

### Low accuracy with Tesseract

- Try preprocessing images (increase contrast, deskew)
- Tesseract works best with 300+ DPI images
- Combine results from PaddleOCR (usually more reliable for printed text)

## References

- [Tesseract GitHub](https://github.com/UB-Mannheim/tesseract/wiki)
- [pytesseract Documentation](https://pypi.org/project/pytesseract/)
- [PaddleOCR Documentation](https://github.com/PaddlePaddle/PaddleOCR)
