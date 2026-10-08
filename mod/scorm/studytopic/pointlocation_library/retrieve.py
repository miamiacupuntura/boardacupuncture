#!/usr/bin/env python3

import json
import re
import sys
from pathlib import Path

BASE_DIR = Path('/var/www/html/moodle/mod/scorm/studytopic/pointlocation_library')
INDEX_FILE = BASE_DIR / 'search_index.jsonl'
QUESTIONS_FILE = BASE_DIR / 'questions.jsonl'

STOPWORDS = {
    'a', 'an', 'and', 'are', 'as', 'at', 'be', 'by',
    'for', 'from', 'in', 'is', 'it', 'of', 'on', 'or',
    'the', 'to', 'with'
}


def normalize(text):
    text = str(text or '').casefold()
    text = re.sub(r'[^a-z0-9]+', ' ', text)
    return re.sub(r'\s+', ' ', text).strip()


def tokens(text):
    return [
        word for word in normalize(text).split()
        if len(word) > 1 and word not in STOPWORDS
    ]


def topic_names(record):
    return {
        normalize(p.get('sectionname'))
        for p in record.get('placements', [])
        if p.get('sectionname')
    }


def load_question_texts():
    question_texts = {}

    with QUESTIONS_FILE.open('r', encoding='utf-8') as source:
        for line in source:
            record = json.loads(line)
            library_id = record.get('library_id')

            if not library_id:
                continue

            question = record.get('question') or {}
            question_texts[library_id] = question.get('QuestionText') or ''

    return question_texts


def score_record(record, query, current_topic='', question_text=''):
    text = normalize(record.get('search_text', ''))
    text_tokens = set(tokens(text))
    query_tokens = list(dict.fromkeys(tokens(query)))

    if not query_tokens:
        return 0, []

    matched = [
        word for word in query_tokens
        if word in text_tokens
    ]

    if not matched:
        return 0, []

    score = len(matched) * 10
    reasons = ['terms:' + ','.join(matched)]

    # All concepts present, regardless of order.
    # Example:
    # "Hand Yin" can match "Yin channels of the Hand".
    if len(matched) == len(query_tokens):
        score += 35
        reasons.append('all-query-terms')

    # Exact phrase receives an additional bonus.
    normalized_query = normalize(query)
    if normalized_query and normalized_query in text:
        score += 25
        reasons.append('exact-phrase')

    # Semantic proximity bonus.
    # Reward query concepts that occur close together even when their
    # order differs or small connector words appear between them.
    # Example: "Hand Yin" should strongly match
    # "Yin channels of the hand".
    if len(query_tokens) >= 2 and len(matched) == len(query_tokens):
        words = text.split()
        positions = {
            term: [i for i, word in enumerate(words) if word == term]
            for term in query_tokens
        }

        if all(positions.get(term) for term in query_tokens):
            best_span = None

            def find_span(level, chosen):
                nonlocal best_span

                if level == len(query_tokens):
                    span = max(chosen) - min(chosen)

                    if best_span is None or span < best_span:
                        best_span = span
                    return

                term = query_tokens[level]

                for position in positions[term]:
                    find_span(level + 1, chosen + [position])

            find_span(0, [])

            if best_span is not None:
                if best_span <= 3:
                    score += 40
                    reasons.append('concepts-very-close')
                elif best_span <= 6:
                    score += 25
                    reasons.append('concepts-close')
                elif best_span <= 10:
                    score += 10
                    reasons.append('concepts-near')

    # Give extra weight when the query concepts are present in the
    # original QuestionText itself, rather than only in answers,
    # feedback, provenance, or other searchable fields.
    question_text_normalized = normalize(question_text)
    question_text_tokens = set(tokens(question_text))
    stem_matched = [
        word for word in query_tokens
        if word in question_text_tokens
    ]

    if len(stem_matched) == len(query_tokens):
        score += 50
        reasons.append('all-query-terms-in-question-text')

        stem_words = question_text_normalized.split()
        stem_positions = {
            term: [i for i, word in enumerate(stem_words) if word == term]
            for term in query_tokens
        }

        if all(stem_positions.get(term) for term in query_tokens):
            best_stem_span = min(
                max(combo) - min(combo)
                for combo in __import__('itertools').product(
                    *(stem_positions[term] for term in query_tokens)
                )
            )

            if best_stem_span <= 3:
                score += 40
                reasons.append('question-text-concepts-very-close')
            elif best_stem_span <= 6:
                score += 25
                reasons.append('question-text-concepts-close')
            elif best_stem_span <= 10:
                score += 10
                reasons.append('question-text-concepts-near')

    # Strong semantic bonus for channel-family structures in the
    # original QuestionText.
    #
    # Example:
    #   Query: "Hand Yin"
    #   QuestionText: "Yin channels of the hand"
    #
    # This is stronger than merely finding the words "hand" and "yin"
    # close together, because it identifies the actual relationship
    # between the channel type and the body region.
    channel_structure_bonus = False

    normalized_query_for_channels = normalize(query)

    channel_regions = ('hand', 'foot')
    channel_types = ('yin', 'yang')

    query_region = next(
        (region for region in channel_regions
         if region in query_tokens),
        None
    )

    query_channel_type = next(
        (channel_type for channel_type in channel_types
         if channel_type in query_tokens),
        None
    )

    if query_region and query_channel_type:
        channel_patterns = [
            rf'\b{query_channel_type}\s+channels?\s+of\s+the\s+{query_region}\b',
            rf'\b{query_channel_type}\s+channels?\s+of\s+{query_region}\b',
            rf'\b{query_channel_type}\s+channels?\s+of\s+the\s+{query_region}s\b',
        ]

        for pattern in channel_patterns:
            if __import__('re').search(
                pattern,
                question_text_normalized,
                __import__('re').IGNORECASE
            ):
                channel_structure_bonus = True
                break

    if channel_structure_bonus:
        score += 80
        reasons.append('question-text-channel-structure')

    # Strong preference for the current Study Guide topic.
    normalized_topic = normalize(current_topic)

    if normalized_topic and normalized_topic in topic_names(record):
        score += 50
        reasons.append('current-topic')

    return score, reasons


def retrieve(query, current_topic='', limit=10):
    results = []
    question_texts = load_question_texts()

    with INDEX_FILE.open('r', encoding='utf-8') as source:
        for line in source:
            record = json.loads(line)

            score, reasons = score_record(
                record,
                query,
                current_topic,
                question_texts.get(record.get('library_id'), '')
            )

            if score > 0:
                results.append((score, record, reasons))

    results.sort(
        key=lambda item: (
            -item[0],
            item[1].get('library_id') or ''
        )
    )

    return results[:limit]


def main():
    if len(sys.argv) < 2:
        print('Usage: retrieve.py "query" ["current topic"] [limit]')
        raise SystemExit(1)

    query = sys.argv[1]
    current_topic = sys.argv[2] if len(sys.argv) >= 3 else ''
    limit = int(sys.argv[3]) if len(sys.argv) >= 4 else 10

    results = retrieve(query, current_topic, limit)

    print('Query:', query)
    print('Current topic:', current_topic or '(none)')
    print('Results:', len(results))

    for rank, item in enumerate(results, start=1):
        score, record, reasons = item

        print()
        print('#{} SCORE {}'.format(rank, score))
        print('ID:', record.get('library_id'))
        print('TYPE:', record.get('question_type'))
        print('WHY:', ', '.join(reasons))

        topics = sorted({
            p.get('sectionname')
            for p in record.get('placements', [])
            if p.get('sectionname')
        })

        print(
            'TOPIC:',
            ' | '.join(topics) if topics else '(NO SECTION)'
        )

        print(
            'TEXT:',
            record.get('search_text', '')[:1000]
        )


if __name__ == '__main__':
    main()
