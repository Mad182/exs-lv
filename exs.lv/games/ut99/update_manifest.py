import os
import json

base_dir = '/var/www/exs-lv/exs.lv/games/ut99/gamedata'
manifest_path = os.path.join(base_dir, 'manifest.json')

manifest = {}

# Keep original flyby manifest entries as base
if os.path.exists(manifest_path):
    with open(manifest_path, 'r', encoding='utf-8') as f:
        manifest = json.load(f)

for root, dirs, files in os.walk(base_dir):
    for file in files:
        if file == 'manifest.json':
            continue
        full_path = os.path.join(root, file)
        rel_path = os.path.relpath(full_path, base_dir).replace('\\', '/')
        stat = os.stat(full_path)
        manifest[rel_path] = {
            "filesize": stat.st_size,
            "filetime": int(stat.st_mtime)
        }

with open(manifest_path, 'w', encoding='utf-8') as f:
    json.dump(manifest, f, indent=2)

print(f"Updated manifest.json with {len(manifest)} files.")
