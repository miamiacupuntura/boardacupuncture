#!/usr/bin/env python3

import json
import subprocess
import zipfile
from collections import Counter, defaultdict
from pathlib import Path

COURSE_ID = 34
MOODLEDATA = Path('/mnt/volume_nyc1_01/moodledata')
OUTPUT_DIR = Path('/var/www/html/moodle/mod/scorm/studytopic/pointlocation_library')
INVENTORY_HELPER = OUTPUT_DIR / 'get_scorm_inventory.php'

PACKAGES_FILE = OUTPUT_DIR / 'packages.jsonl'
QUESTIONS_FILE = OUTPUT_DIR / 'questions.jsonl'
PLACEMENTS_FILE = OUTPUT_DIR / 'placements.json'
MANIFEST_FILE = OUTPUT_DIR / 'manifest.json'
ERRORS_FILE = OUTPUT_DIR / 'errors.json'


def package_path(contenthash):
    return (
        MOODLEDATA
        / 'filedir'
        / contenthash[:2]
        / contenthash[2:4]
        / contenthash
    )


def load_inventory():
    result = subprocess.run(
        ['php', str(INVENTORY_HELPER)],
        check=True,
        capture_output=True,
        text=True
    )
    return json.loads(result.stdout)


def main():
    inventory = load_inventory()

    placements_by_hash = defaultdict(list)

    for row in inventory:
        placements_by_hash[row['contenthash']].append({
            'fileid': row.get('fileid'),
            'cmid': row.get('cmid'),
            'instanceid': row.get('instanceid'),
            'sectionid': row.get('sectionid'),
            'sectionnumber': row.get('sectionnumber'),
            'sectionname': row.get('sectionname'),
            'scormid': row.get('scormid'),
            'scormname': row.get('scormname'),
            'packagefilename': row.get('packagefilename'),
        })

    valid_packages = 0
    question_count = 0
    type_counts = Counter()
    errors = []

    with PACKAGES_FILE.open('w', encoding='utf-8') as packages_out, \
         QUESTIONS_FILE.open('w', encoding='utf-8') as questions_out:
        for contenthash in sorted(placements_by_hash):
            path = package_path(contenthash)
            placements = placements_by_hash[contenthash]

            try:
                with zipfile.ZipFile(path, 'r') as zf:
                    names = zf.namelist()

                    if 'QUIZDATA.json' not in names:
                        errors.append({
                            'contenthash': contenthash,
                            'error': 'QUIZDATA.json not found',
                            'placements': placements,
                        })
                        continue

                    raw = zf.read('QUIZDATA.json')
                    quizdata = json.loads(raw.decode('utf-8-sig'))

            except Exception as exc:
                errors.append({
                    'contenthash': contenthash,
                    'error': f'{type(exc).__name__}: {exc}',
                    'placements': placements,
                })
                continue

            valid_packages += 1

            package_record = {
                'courseid': COURSE_ID,
                'contenthash': contenthash,
                'placements': placements,
                'quizdata': quizdata,
            }

            packages_out.write(
                json.dumps(
                    package_record,
                    ensure_ascii=False,
                    separators=(',', ':')
                ) + '\n'
            )

            questions = quizdata.get('Questions', [])

            for question_index, question in enumerate(questions, start=1):
                qtype = question.get('__type', 'UNKNOWN')

                question_count += 1
                type_counts[qtype] += 1

                question_record = {
                    'library_id': f'{contenthash}:{question_index}',
                    'courseid': COURSE_ID,
                    'contenthash': contenthash,
                    'question_index': question_index,
                    'question_type': qtype,
                    'placements': placements,
                    'question': question,
                }

                questions_out.write(
                    json.dumps(
                        question_record,
                        ensure_ascii=False,
                        separators=(',', ':')
                    ) + '\n'
                )

    placements_output = {
        contenthash: placements
        for contenthash, placements in sorted(placements_by_hash.items())
    }

    PLACEMENTS_FILE.write_text(
        json.dumps(
            placements_output,
            ensure_ascii=False,
            indent=2
        ) + '\n',
        encoding='utf-8'
    )

    ERRORS_FILE.write_text(
        json.dumps(
            errors,
            ensure_ascii=False,
            indent=2
        ) + '\n',
        encoding='utf-8'
    )

    manifest = {
        'courseid': COURSE_ID,
        'inventory_records': len(inventory),
        'unique_contenthashes': len(placements_by_hash),
        'valid_packages': valid_packages,
        'invalid_packages': len(errors),
        'questions': question_count,
        'question_types': dict(sorted(type_counts.items())),
        'files': {
            'packages': PACKAGES_FILE.name,
            'questions': QUESTIONS_FILE.name,
            'placements': PLACEMENTS_FILE.name,
            'errors': ERRORS_FILE.name,
        },
    }

    MANIFEST_FILE.write_text(
        json.dumps(
            manifest,
            ensure_ascii=False,
            indent=2
        ) + '\n',
        encoding='utf-8'
    )

    print('Point Location Library Build Complete')
    print('Inventory records:', len(inventory))
    print('Unique contenthashes:', len(placements_by_hash))
    print('Valid packages:', valid_packages)
    print('Invalid packages:', len(errors))
    print('Questions:', question_count)
    print('Question types:')
    for qtype, count in sorted(type_counts.items()):
        print(f'  {qtype}: {count}')


if __name__ == '__main__':
    main()
