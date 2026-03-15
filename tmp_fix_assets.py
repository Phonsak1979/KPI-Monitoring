import re
import sys

def process_file(file_path):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Replace href="dist/..." or href="plugins/..."
    content = re.sub(r'href="(dist/|plugins/)([^"]+)"', r'href="{{ asset(\'\1\2\') }}"', content)
    
    # Replace src="dist/..." or src="plugins/..."
    content = re.sub(r'src="(dist/|plugins/)([^"]+)"', r'src="{{ asset(\'\1\2\') }}"', content)
    
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Processed {file_path}")

files = [
    r"d:\0-LARAVEL\KPI-Dashboard\resources\views\layouts\head.blade.php",
    r"d:\0-LARAVEL\KPI-Dashboard\resources\views\layouts\script.blade.php",
    r"d:\0-LARAVEL\KPI-Dashboard\resources\views\layouts\sidebar.blade.php",
]

for file in files:
    process_file(file)
