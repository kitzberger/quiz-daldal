#!/usr/bin/env php
<?php
/**
 * Daldal Finder
 * 
 * Tool to find potential Daldal candidates in German word lists.
 * A Daldal is a word that can be interpreted in multiple ways with the same letters.
 */

class DaldalFinder
{
    private array $wordSet = [];
    private array $wordsByLength = [];

    public function __construct(string $wordsDirectory = 'AlleDeutschenWoerter')
    {
        $this->loadWords($wordsDirectory);
    }

    /**
     * Load all German words from text files
     */
    private function loadWords(string $directory): void
    {
        echo "Loading word database...\n";
        
        $files = [
            $directory . '/Substantive/substantiv_singular_alle.txt',
            $directory . '/Verben/Verben_regelmaesig.txt',
            $directory . '/Verben/Verben_unregelmaeßig_Infinitiv.txt',
            $directory . '/Adjektive/Adjektive.txt',
        ];

        foreach ($files as $file) {
            if (!file_exists($file)) {
                echo "Warning: File not found: $file\n";
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

        echo "Loaded " . count($this->wordSet) . " words\n\n";
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

        echo "Searching for words ending with '$suffix'...\n";
        echo "Checking if base word (without suffix) is also valid...\n\n";

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

        echo "Searching for words starting with '$prefix'...\n";
        echo "Checking if remaining word (without prefix) is also valid...\n\n";

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

// CLI Interface
function showUsage(): void
{
    echo <<<USAGE
Daldal Finder - Find potential Daldal candidates in German words

Usage:
  php daldal-finder.php [command] [options]

Commands:
  ending <suffix> [minLength]    Find words ending with <suffix>
                                 Example: php daldal-finder.php ending samt
                                 
  starting <prefix> [minLength]  Find words starting with <prefix>
                                 Example: php daldal-finder.php starting mit
                                 
  help                           Show this help message

Options:
  minLength                      Minimum word length (default: 5)

Examples:
  php daldal-finder.php ending samt 6
  php daldal-finder.php starting be 7
  php daldal-finder.php starting mit

USAGE;
}

// Main execution
if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

if ($argc < 2) {
    showUsage();
    exit(1);
}

$command = $argv[1];

switch ($command) {
    case 'ending':
        if ($argc < 3) {
            echo "Error: Missing suffix argument\n\n";
            showUsage();
            exit(1);
        }
        
        $suffix = $argv[2];
        $minLength = isset($argv[3]) ? (int)$argv[3] : 5;
        
        $finder = new DaldalFinder();
        $results = $finder->findWordsEndingWith($suffix, $minLength);
        $finder->printResults($results);
        
        if (!empty($results)) {
            $filename = "daldal-ending-{$suffix}.json";
            $finder->exportToJson($results, $filename);
        }
        break;

    case 'starting':
        if ($argc < 3) {
            echo "Error: Missing prefix argument\n\n";
            showUsage();
            exit(1);
        }
        
        $prefix = $argv[2];
        $minLength = isset($argv[3]) ? (int)$argv[3] : 5;
        
        $finder = new DaldalFinder();
        $results = $finder->findWordsStartingWith($prefix, $minLength);
        $finder->printResults($results);
        
        if (!empty($results)) {
            $filename = "daldal-starting-{$prefix}.json";
            $finder->exportToJson($results, $filename);
        }
        break;

    case 'help':
    case '--help':
    case '-h':
        showUsage();
        break;

    default:
        echo "Error: Unknown command '$command'\n\n";
        showUsage();
        exit(1);
}
