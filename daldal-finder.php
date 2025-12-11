#!/usr/bin/env php
<?php
/**
 * Daldal Finder CLI
 * 
 * Command-line interface for the Daldal Finder tool.
 * A Daldal is a word that can be interpreted in multiple ways with the same letters.
 */

require_once __DIR__ . '/DaldalFinder.class.php';

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
