from paddleocr import PaddleOCR
import sys

# Initialize PaddleOCR (do this once)
ocr = PaddleOCR(use_angle_cls=True, lang='en')

# Get image path from command line argument
image_path = sys.argv[1]

# Run OCR
result = ocr.ocr(image_path)

# Convert result to plain text
text_output = ""
for line in result:
    for word in line:
        text_output += word[1][0] + " "

# Print the extracted text (Laravel will capture this)
print(text_output)