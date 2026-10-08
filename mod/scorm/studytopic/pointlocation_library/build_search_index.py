#!/usr/bin/env python3

import json
import re
from pathlib import Path

BASE_DIR = Path('/var/www/html/moodle/mod/scorm/studytopic/pointlocation_library')
QUESTIONS_FILE = BASE_DIR / 'questions.jsonl'
OUTPUT_FILE = BASE_DIR / 'search_index.jsonl'


def clean(value):
    if value is None:
        return ''
    value = str(value)
    value = re.sub(r'\s+', ' ', value)
    return value.strip()


def add(parts, value):
    value = clean(value)
    if value:
        parts.append(value)


def searchable_text(record):
    q = record.get('question', {})
    qtype = record.get('question_type', '')
    parts = []

    # Provenance is searchable too.
    for placement in record.get('placements', []):
        add(parts, placement.get('sectionname'))
        add(parts, placement.get('scormname'))
        add(parts, placement.get('packagefilename'))

    add(parts, q.get('QuestionText'))
    add(parts, q.get('QuestionNotes'))

    for value in q.get('Feedback', []):
        add(parts, value)

    for value in q.get('Groups', []):
        add(parts, value)

    if qtype == 'MultipleChoiceQuestion':
        for value in q.get('Answers', []):
            add(parts, value)
        add(parts, q.get('RightAnswer'))

    elif qtype == 'MatchingQuestion':
        for item in q.get('LeftColumn', []):
            add(parts, item.get('Content'))
        for item in q.get('RightColumn', []):
            add(parts, item.get('Content'))

    elif qtype == 'ClickMapQuestion':
        add(parts, q.get('ImageURL'))

    elif qtype == 'FillInTheBlankQuestion':
        for value in q.get('Answers', []):
            add(parts, value)

    elif qtype == 'TrueFalseQuestion':
        add(parts, q.get('RightAnswer'))

    elif qtype == 'MultipleResponseQuestion':
        for item in q.get('Answers', []):
            if isinstance(item, list) and len(item) >= 2:
                add(parts, item[1])

    elif qtype == 'SequenceQuestion':
        items = sorted(
            q.get('Items', []),
            key=lambda x: x.get('Id', 0)
        )
        for item in items:
            add(parts, item.get('Content'))

    # Remove exact duplicate fragments while preserving order.
    unique = []
    seen = set()

    for part in parts:
        key = part.casefold()
        if key not in seen:
            seen.add(key)
            unique.append(part)

    return ' | '.join(unique)


def main():
    count = 0

    with QUESTIONS_FILE.open('r', encoding='utf-8') as source, \
         OUTPUT_FILE.open('w', encoding='utf-8') as output:

        for line in source:
            record = json.loads(line)

            index_record = {
                'library_id': record.get('library_id'),
                'contenthash': record.get('contenthash'),
                'question_index': record.get('question_index'),
                'question_type': record.get('question_type'),
                'placements': record.get('placements', []),
                'search_text': searchable_text(record),
            }

            output.write(
                json.dumps(
                    index_record,
                    ensure_ascii=False,
                    separators=(',', ':')
                ) + '\n'
            )

            count += 1

    print('Search index build complete')
    print('Indexed questions:', count)
    print('Output:', OUTPUT_FILE)


if __name__ == '__main__':
    main()
