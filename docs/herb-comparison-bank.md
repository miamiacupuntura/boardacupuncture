# Herbal comparison question bank

This module turns the question-and-answer and comparison-chart material in *Chinese Herbal Medicines: Comparisons and Characteristics* (second edition) into a separate study experience. It does not alter the existing short comparison cards or the 63-formula practice bank.

## Student experience

- **Questions and explanations:** follows the 17 chapters and the question numbering of the source.
- **Recall flashcards:** asks the same questions without showing the answer first and stores `Again`, `Hard`, or `Got it` only in the current browser.
- **Bar-chart comparisons:** recreates the 117 source figures as accessible HTML. Bar height represents relative action strength; color represents herb temperature.
- **English and Spanish:** English extraction is authoritative. Spanish content is a separate automatically generated layer so terminology can be reviewed without changing the source bank.
- **Continuous bilingual reading:** switching English/Spanish preserves the chapter, study mode, search, current question or chart, relative reading position, and flashcard reveal state.

## Generated artifacts

| File | Purpose |
| --- | --- |
| `study-guide/comparisons/herb-comparison-bank-data.json` | 17 chapters, 249 questions, 117 charts |
| `study-guide/comparisons/herb-comparison-bank-translations-es.json` | Spanish question, answer, memory-cue, and chart-title layer |
| `study-guide/comparisons/herb-comparison-bank.html` | English entry point |
| `study-guide/comparisons/herb-comparison-bank-es.html` | Spanish entry point |
| `study-guide/comparisons/herb-comparison-bank.js` | filtering, modes, flashcard progress, chart rendering |
| `study-guide/comparisons/herb-comparison-bank.css` | responsive presentation and chart system |

The uploaded PDF is an input to the generator and is intentionally not copied into the repository or release artifact.

## Regeneration

```bash
python3 tools/generate_herb_comparison_bank.py /absolute/path/to/source.pdf
python3 tools/translate_herb_comparison_bank_es.py
node tools/validate_herb_comparison_bank.mjs study-guide/comparisons/herb-comparison-bank-data.json
```

The translation command keeps its incremental cache in `output/herb-comparison-translation-cache-es.json`. That cache is a development artifact and must not be committed.

## Validation contract

The release build fails unless the bank contains exactly:

- 17 sequential chapters;
- 249 questions with continuous chapter-local numbering and non-empty answers;
- 117 uniquely numbered charts;
- normalized chart bars with valid strength, temperature, and color values;
- both language entry points and the Spanish translation layer;
- a discoverable link from the Single Herbs Comparisons landing page.

## Editorial review order

Review Spanish content chapter by chapter rather than editing the generated English JSON. Corrections should ultimately live in a small override layer keyed by question ID (`chapter-question`) so that regenerating the extraction remains safe. Priority review order:

1. question wording and punctuation;
2. herb names and biomedical/TCM terminology;
3. the short memory cue;
4. full explanation prose;
5. translated chart titles.
