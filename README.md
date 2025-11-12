# Daldal Finder

A PHP tool to discover potential **Daldal** candidates in German word lists.

## What is a Daldal?

A Daldal is a German word or phrase that consists of the exact same letters in the same order but can be interpreted in multiple ways with different meanings. See [WARP.md](WARP.md) for a comprehensive definition.

### Examples

- **Arbeitsamt** → "Arbeit" + "samt" vs. "Arbeitsamt" (employment office)
- **mitnichten** → "mit" + "Nichten" vs. "mitnichten" (by no means)
- **beinhalten** → "Bein" + "halten" vs. "beinhalten" (to contain)
- **beigesetzt** → "beige" + "setzt" vs. "beigesetzt" (buried)

## Installation

No installation required! Just make sure you have PHP 8.0+ installed:

```bash
php --version
```

## Usage

The tool provides two main search modes:

### 1. Find words ending with specific letters

Find all words that end with certain letters AND are also valid words when those letters are removed:

```bash
php daldal-finder.php ending <suffix> [minLength]
```

**Examples:**

```bash
# Find words ending with "samt"
php daldal-finder.php ending samt

# Find words ending with "halten" (minimum length 8)
php daldal-finder.php ending halten 8
```

### 2. Find words starting with specific letters

Find all words that start with certain letters AND are also valid words when those letters are removed:

```bash
php daldal-finder.php starting <prefix> [minLength]
```

**Examples:**

```bash
# Find words starting with "mit"
php daldal-finder.php starting mit

# Find words starting with "be" (minimum length 6)
php daldal-finder.php starting be 6
```

### Parameters

- `<suffix>` or `<prefix>`: The letters to search for (required)
- `[minLength]`: Minimum length of the full word (optional, default: 5)

## Output

The tool will:

1. Load the German word database (~26,000 words)
2. Search for matching candidates
3. Display results in the console
4. Export results to a JSON file (e.g., `daldal-ending-samt.json`)

### Console Output Example

```
Found 9 potential Daldal candidates:
======================================================================

1. Arbeitsamt
   → Arbeit + samt

2. Gesundheitsamt
   → Gesundheit + samt
```

### JSON Export

Results are also saved to a JSON file for further processing:

```json
[
  {
    "full_word": "Arbeitsamt",
    "base_word": "Arbeit",
    "suffix": "samt",
    "interpretation_1": "Arbeitsamt",
    "interpretation_2": "Arbeit + samt"
  }
]
```

## Word Database

The tool uses word lists from the `AlleDeutschenWoerter/` directory:

- **Substantive** (Nouns): `substantiv_singular_alle.txt`
- **Verben** (Verbs): `Verben_regelmaesig.txt`, `Verben_unregelmaeßig_Infinitiv.txt`
- **Adjektive** (Adjectives): `Adjektive.txt`

## Help

View the help message:

```bash
php daldal-finder.php help
```

## Ideas for Finding Daldals

### Common prefixes to try:
- `be` (behalten, beige, etc.)
- `mit` (mitnichten, Mitglied, etc.)
- `ver` (verhalten, etc.)
- `ab` (abhalten, etc.)
- `an` (anhalten, etc.)

### Common suffixes to try:
- `samt` (Arbeitsamt, etc.)
- `halten` (beinhalten, etc.)
- `art` (words ending in -art)
- `bahn` (words ending in -bahn)
- `bar` (words ending in -bar)

### Search strategies:
1. Try common German prefixes (separable verb prefixes)
2. Try short words that could be prepositions or adjectives
3. Look for compound nouns that might split into word + word
4. Check words that are both standalone words and parts of compounds

## Advanced Usage

You can process the JSON output with other tools:

```bash
# Count results
php daldal-finder.php starting mit | grep "Found"

# Search multiple patterns
for prefix in mit be ver ab; do
  php daldal-finder.php starting $prefix
done
```

## Requirements

- PHP 8.0 or higher
- mbstring extension (for UTF-8 support)

## License

See the German word database license in `AlleDeutschenWoerter/LICENSE`.
