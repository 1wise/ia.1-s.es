import os
import re

def load_replacements(replacements_file):
    replacements = []
    with open(replacements_file, "r", encoding="utf-8") as f:
        for line in f:
            parts = line.strip().split(" -->> ")
            if len(parts) == 2:
                replacements.append((parts[0], parts[1]))
    return replacements

def update_parameters(input_folder, replacements):
    for root, _, files in os.walk(input_folder):
        for file in files:
            if file.endswith(".html") or file.endswith(".php"):
                file_path = os.path.join(root, file)
                with open(file_path, "r", encoding="utf-8") as f:
                    content = f.read()

                for search_value, replace_value in replacements:
                    content = re.sub(search_value, replace_value, content)

                with open(file_path, "w", encoding="utf-8") as f:
                    f.write(content)

if __name__ == "__main__":
    input_folder = input("Carpeta de instalacion: ").strip()
    replacements_file = input("Archivo de instalacion: ").strip()

    replacements = load_replacements(replacements_file)
    update_parameters(input_folder, replacements)
