#!/bin/bash
# Batch Daldal Search Script
# Search for multiple patterns at once

echo "======================================"
echo "Daldal Batch Search"
echo "======================================"
echo ""

# Common German prefixes (especially separable verb prefixes)
PREFIXES=("ab" "an" "auf" "aus" "be" "bei" "durch" "ein" "mit" "nach" "über" "um" "unter" "ver" "vor" "zu")

# Common suffixes that could form valid words
SUFFIXES=("samt" "halten" "art" "bar" "fall" "kraft" "land" "los" "rat" "reich" "tag" "teil" "wort" "weg")

# Search prefixes
echo "Searching for words with common prefixes..."
echo "============================================"
for prefix in "${PREFIXES[@]}"; do
    echo ""
    echo "--- Prefix: $prefix ---"
    php daldal-finder.php starting "$prefix" 6 | grep -E "(Found|→)" | head -20
done

echo ""
echo ""
echo "Searching for words with common suffixes..."
echo "============================================"
for suffix in "${SUFFIXES[@]}"; do
    echo ""
    echo "--- Suffix: $suffix ---"
    php daldal-finder.php ending "$suffix" 6 | grep -E "(Found|→)" | head -20
done

echo ""
echo ""
echo "======================================"
echo "Batch search complete!"
echo "Check individual JSON files for full results."
echo "======================================"
