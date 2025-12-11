<?php
/**
 * Daldal Finder Class
 *
 * Tool to find potential Daldal candidates in German word lists.
 * A Daldal is a word that can be interpreted in multiple ways with the same letters.
 */

class DaldalFinder
{
    private array $wordSet = [];
    private array $wordsByLength = [];
    private bool $silentMode = false;

    public function __construct(string $wordsDirectory = 'AlleDeutschenWoerter', bool $silentMode = false)
    {
        $this->silentMode = $silentMode;
        $this->loadWords($wordsDirectory);
    }

    /**
     * Load all German words from text files
     */
    private function loadWords(string $directory): void
    {
        if (!$this->silentMode) {
            echo "Loading word database...\n";
        }

        $files = [
            $directory . '/Substantive/substantiv_singular_alle.txt',
            $directory . '/Verben/Verben_regelmaesig.txt',
            $directory . '/Verben/Verben_unregelmaeßig_Infinitiv.txt',
            $directory . '/Adjektive/Adjektive.txt',
        ];

        foreach ($files as $file) {
            if (!file_exists($file)) {
                if (!$this->silentMode) {
                    echo "Warning: File not found: $file\n";
                }
                continue;
            }

            $words = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($words as $word) {
                $word = trim($word);
                if (empty($word)) {
                    continue;
                }

                $wordLower = mb_strtolower($word);
                $this->wordSet[$wordLower] = $word;

                $length = mb_strlen($wordLower);
                if (!isset($this->wordsByLength[$length])) {
                    $this->wordsByLength[$length] = [];
                }
                $this->wordsByLength[$length][] = $wordLower;
            }
        }

        if (!$this->silentMode) {
            echo "Loaded " . count($this->wordSet) . " words\n\n";
        }
    }

    /**
     * Check if a word exists in the database (case-insensitive)
     */
    private function wordExists(string $word): bool
    {
        return isset($this->wordSet[mb_strtolower($word)]);
    }

    /**
     * Find words ending with specific letters that are also valid when those letters are removed
     *
     * Example: "samt" -> finds "Arbeitsamt" (which contains "Arbeit" when "samt" is removed)
     */
    public function findWordsEndingWith(string $suffix, int $minLength = 5): array
    {
        $suffix = mb_strtolower($suffix);
        $suffixLen = mb_strlen($suffix);
        $results = [];

        if (!$this->silentMode) {
            echo "Searching for words ending with '$suffix'...\n";
            echo "Checking if base word (without suffix) is also valid...\n\n";
        }

        foreach ($this->wordSet as $wordLower => $originalWord) {
            if (mb_strlen($wordLower) < $minLength) {
                continue;
            }

            if (mb_substr($wordLower, -$suffixLen) === $suffix) {
                $baseWord = mb_substr($wordLower, 0, -$suffixLen);

                if ($this->wordExists($baseWord)) {
                    $results[] = [
                        'full_word' => $originalWord,
                        'base_word' => $this->wordSet[$baseWord],
                        'suffix' => $suffix,
                        'interpretation_1' => $originalWord,
                        'interpretation_2' => $this->wordSet[$baseWord] . ' + ' . $suffix,
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * Find words starting with specific letters that are also valid when those letters are removed
     *
     * Example: "be" -> finds "beinhalten" (which contains "inhalten" when "be" is removed)
     */
    public function findWordsStartingWith(string $prefix, int $minLength = 5): array
    {
        $prefix = mb_strtolower($prefix);
        $prefixLen = mb_strlen($prefix);
        $results = [];

        if (!$this->silentMode) {
            echo "Searching for words starting with '$prefix'...\n";
            echo "Checking if remaining word (without prefix) is also valid...\n\n";
        }

        foreach ($this->wordSet as $wordLower => $originalWord) {
            if (mb_strlen($wordLower) < $minLength) {
                continue;
            }

            if (mb_substr($wordLower, 0, $prefixLen) === $prefix) {
                $remainingWord = mb_substr($wordLower, $prefixLen);

                if ($this->wordExists($remainingWord)) {
                    $results[] = [
                        'full_word' => $originalWord,
                        'remaining_word' => $this->wordSet[$remainingWord],
                        'prefix' => $prefix,
                        'interpretation_1' => $originalWord,
                        'interpretation_2' => $prefix . ' + ' . $this->wordSet[$remainingWord],
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * Print results in a readable format
     */
    public function printResults(array $results): void
    {
        if (empty($results)) {
            echo "No candidates found.\n";
            return;
        }

        echo "Found " . count($results) . " potential Daldal candidates:\n";
        echo str_repeat("=", 70) . "\n\n";

        foreach ($results as $i => $result) {
            echo ($i + 1) . ". " . $result['interpretation_1'] . "\n";
            echo "   → " . $result['interpretation_2'] . "\n\n";
        }
    }

    /**
     * Export results to JSON file
     */
    public function exportToJson(array $results, string $filename): void
    {
        file_put_contents($filename, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "Results exported to: $filename\n";
    }
}
