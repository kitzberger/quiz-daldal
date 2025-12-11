<?php
/**
 * Daldal Finder Web Interface
 * 
 * A web interface to discover potential Daldal candidates in German word lists.
 */

require_once __DIR__ . '/DaldalFinder.class.php';

$results = [];
$searchType = '';
$searchTerm = '';
$minLength = 5;
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchType = $_POST['search_type'] ?? '';
    $searchTerm = trim($_POST['search_term'] ?? '');
    $minLength = (int)($_POST['min_length'] ?? 5);
    
    if (empty($searchTerm)) {
        $error = 'Bitte geben Sie einen Suchbegriff ein.';
    } else {
        try {
            // Enable silent mode to suppress console output
            $finder = new DaldalFinder('AlleDeutschenWoerter', true);
            
            if ($searchType === 'ending') {
                $results = $finder->findWordsEndingWith($searchTerm, $minLength);
            } elseif ($searchType === 'starting') {
                $results = $finder->findWordsStartingWith($searchTerm, $minLength);
            }
        } catch (Exception $e) {
            $error = 'Fehler: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daldal Finder - Entdecke deutsche Worträtsel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        .main-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 2rem;
            max-width: 900px;
            margin: 0 auto;
        }
        .result-card {
            transition: all 0.2s;
            border-left: 4px solid #667eea;
        }
        .result-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            background-color: #f8f9ff;
            border-left-color: #764ba2;
        }
        .interpretation-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
        .hero-section {
            text-align: center;
            margin-bottom: 2rem;
        }
        .hero-section h1 {
            color: #667eea;
            font-weight: bold;
        }
        .search-box {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
        .example-pills .badge {
            cursor: pointer;
            margin: 0.25rem;
        }
        .breadcrumb-trail {
            background: #fff;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 2rem;
            border: 2px solid #667eea;
        }
        .single-trail {
            background: #f8f9fa;
            border-radius: 0.3rem;
            padding: 0.75rem;
            margin-bottom: 0.75rem;
            border-left: 3px solid #667eea;
        }
        .single-trail:last-child {
            margin-bottom: 0;
        }
        .trail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .breadcrumb-item {
            display: inline-block;
            margin: 0.25rem;
        }
        .breadcrumb-badge {
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .breadcrumb-badge:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .result-card {
            cursor: pointer;
        }
        .result-card:active {
            transform: translateX(5px) scale(0.98);
        }
        .clear-trail-btn {
            font-size: 0.8rem;
        }
        .suffix-buttons {
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid #e9ecef;
        }
        .suffix-btn {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            margin: 0.2rem;
        }
        .result-card-body {
            position: relative;
        }
        .btn-outline-orange {
            border-color: #fd7e14;
            color: #fd7e14;
        }
        .btn-outline-orange:hover {
            background-color: #fd7e14;
            border-color: #fd7e14;
            color: #fff;
        }
        .breadcrumb-item+.breadcrumb-item {
            padding-left: 8px !important;
        }
        .breadcrumb-item+.breadcrumb-item::before {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-container">
            <!-- Hero Section -->
            <div class="hero-section">
                <h1><i class="bi bi-puzzle"></i> Daldal Finder</h1>
                <p class="lead text-muted">Entdecke deutsche Wörter mit mehrfacher Bedeutung</p>
            </div>

            <!-- Breadcrumb Trails -->
            <div id="breadcrumbTrails" class="breadcrumb-trail" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="bi bi-signpost-2"></i> Suchpfade</h6>
                    <button class="btn btn-sm btn-outline-danger clear-trail-btn" onclick="clearAllTrails()">
                        <i class="bi bi-trash"></i> Alle löschen
                    </button>
                </div>
                <div id="trailsContainer"></div>
            </div>

            <!-- Info Box -->
            <div class="alert alert-info" role="alert">
                <h5 class="alert-heading"><i class="bi bi-info-circle"></i> Was ist ein Daldal?</h5>
                <p class="mb-0">Ein Daldal ist ein deutsches Wort, das aus denselben Buchstaben in derselben Reihenfolge besteht, 
                aber auf mindestens zwei verschiedene Arten interpretiert werden kann.</p>
                <hr>
                <small><strong>Beispiel:</strong> "Arbeitsamt" → "Arbeit" + "samt" vs. "Arbeitsamt" (Behörde)</small>
            </div>

            <!-- Search Form -->
            <div class="search-box">
                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="search_type" class="form-label fw-bold">Suchtyp</label>
                            <select class="form-select" name="search_type" id="search_type" required>
                                <option value="ending" <?= $searchType === 'ending' ? 'selected' : '' ?>>
                                    <i class="bi bi-arrow-left"></i> Wörter die enden mit...
                                </option>
                                <option value="starting" <?= $searchType === 'starting' ? 'selected' : '' ?>>
                                    <i class="bi bi-arrow-right"></i> Wörter die beginnen mit...
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search_term" class="form-label fw-bold">Suchbegriff</label>
                            <input type="text" class="form-control" name="search_term" id="search_term" 
                                   value="<?= htmlspecialchars($searchTerm) ?>" 
                                   placeholder="z.B. samt, mit, be..." required>
                        </div>
                        <div class="col-md-2">
                            <label for="min_length" class="form-label fw-bold">Min. Länge</label>
                            <input type="number" class="form-control" name="min_length" id="min_length" 
                                   value="<?= $minLength ?>" min="3" max="20">
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-search"></i> Suchen
                        </button>
                    </div>

                    <!-- Example suggestions -->
                    <div class="mt-3 example-pills">
                        <small class="text-muted d-block mb-2">Beliebte Suchbegriffe:</small>
                        <span class="badge bg-secondary" onclick="quickSearch('ending', 'samt')">samt</span>
                        <span class="badge bg-secondary" onclick="quickSearch('starting', 'mit')">mit</span>
                        <span class="badge bg-secondary" onclick="quickSearch('starting', 'be')">be</span>
                        <span class="badge bg-secondary" onclick="quickSearch('starting', 'ver')">ver</span>
                        <span class="badge bg-secondary" onclick="quickSearch('ending', 'bar')">bar</span>
                        <span class="badge bg-secondary" onclick="quickSearch('ending', 'los')">los</span>
                    </div>
                </form>
            </div>

            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Results Section -->
            <?php if (!empty($results)): ?>
                <div class="results-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="mb-0">
                            <i class="bi bi-list-check"></i>
                            Gefunden: <?= count($results) ?> Daldal-Kandidaten
                        </h3>
                        <small class="text-muted">
                            <i class="bi bi-hand-index"></i> Wähle Suffix oder ganzes Wort
                        </small>
                    </div>
                    
                    <div class="row g-3">
                        <?php foreach ($results as $index => $result): ?>
                            <div class="col-12">
                                <div class="card result-card">
                                    <div class="card-body result-card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div style="flex: 1;">
                                                <h5 class="card-title mb-2">
                                                    <span class="badge bg-primary me-2"><?= $index + 1 ?></span>
                                                    <strong><?= htmlspecialchars($result['full_word']) ?></strong>
                                                </h5>
                                                <div class="interpretations">
                                                    <span class="badge interpretation-badge bg-success">
                                                        <i class="bi bi-1-circle"></i> <?= htmlspecialchars($result['interpretation_1']) ?>
                                                    </span>
                                                    <br class="d-block d-md-none">
                                                    <span class="badge interpretation-badge bg-info mt-2 mt-md-0">
                                                        <i class="bi bi-2-circle"></i> <?= htmlspecialchars($result['interpretation_2']) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="suffix-buttons" onclick="event.stopPropagation();">
                                            <small class="text-muted d-block mb-1">
                                                <i class="bi bi-arrow-right-circle"></i> Weitersuchen mit:
                                            </small>
                                            <div class="mb-2">
                                                <span class="badge bg-secondary me-1" style="font-size: 0.7rem;">Suffix (letzte Buchstaben)</span>
                                                <?php
                                                $word = $result['full_word'];
                                                $wordLen = mb_strlen($word);
                                                for ($len = 5; $len >= 2; $len--) {
                                                    if ($wordLen >= $len + 1) {
                                                        $suffix = mb_substr($word, -$len);
                                                        echo '<button class="btn btn-outline-primary btn-sm suffix-btn" onclick="searchByPrefix(\'' . htmlspecialchars($suffix, ENT_QUOTES) . '\')">'
                                                            . '<i class="bi bi-skip-end"></i> ' . $len . ': "' . htmlspecialchars($suffix) . '"'
                                                            . '</button> ';
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <div class="mb-2">
                                                <span class="badge bg-secondary me-1" style="font-size: 0.7rem;">Präfix (erste Buchstaben)</span>
                                                <?php
                                                for ($len = 2; $len <= 5; $len++) {
                                                    if ($wordLen >= $len + 1) {
                                                        $prefix = mb_substr($word, 0, $len);
                                                        echo '<button class="btn btn-outline-success btn-sm suffix-btn" onclick="searchBySuffix(\'' . htmlspecialchars($prefix, ENT_QUOTES) . '\')">'
                                                            . '<i class="bi bi-skip-start"></i> ' . $len . ': "' . htmlspecialchars($prefix) . '"'
                                                            . '</button> ';
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <div>
                                                <?php
                                                // Extract left and right word stems from interpretations
                                                $leftStem = '';
                                                $rightStem = '';

                                                if (isset($result['base_word'])) {
                                                    // For suffix results: base_word is left, suffix is right
                                                    $leftStem = $result['base_word'];
                                                    $rightStem = $result['suffix'];
                                                } elseif (isset($result['remaining_word'])) {
                                                    // For prefix results: prefix is left, remaining_word is right
                                                    $leftStem = $result['prefix'];
                                                    $rightStem = $result['remaining_word'];
                                                }

                                                if ($leftStem) {
                                                    echo '<button class="btn btn-outline-orange btn-sm suffix-btn" onclick="searchBySuffix(\'' . htmlspecialchars($leftStem, ENT_QUOTES) . '\')">'
                                                        . '<i class="bi bi-arrow-left-square"></i> Endet mit: "' . htmlspecialchars($leftStem) . '"'
                                                        . '</button> ';
                                                }

                                                if ($rightStem) {
                                                    echo '<button class="btn btn-outline-info btn-sm suffix-btn" onclick="searchByPrefix(\'' . htmlspecialchars($rightStem, ENT_QUOTES) . '\')">'
                                                        . '<i class="bi bi-arrow-right-square"></i> Beginnt mit: "' . htmlspecialchars($rightStem) . '"'
                                                        . '</button>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)): ?>
                <div class="alert alert-warning" role="alert">
                    <i class="bi bi-search"></i> Keine Daldal-Kandidaten gefunden für "<?= htmlspecialchars($searchTerm) ?>".
                    Versuchen Sie einen anderen Suchbegriff.
                </div>
            <?php endif; ?>

            <!-- Footer -->
            <footer class="mt-5 pt-4 border-top text-center text-muted">
                <p class="mb-0">
                    <small>
                        <i class="bi bi-github"></i> 
                        Daldal Finder | Ein Werkzeug zum Entdecken deutscher Worträtsel
                    </small>
                </p>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Check if current search had results
        const hasResults = <?= !empty($results) ? 'true' : 'false' ?>;
        const lastSearchTerm = '<?= htmlspecialchars($searchTerm, ENT_QUOTES) ?>';

        // LocalStorage key for breadcrumb trails
        const TRAILS_KEY = 'daldalTrails';
        let currentTrailIndex = -1;

        // Load and display breadcrumb trails on page load
        document.addEventListener('DOMContentLoaded', function() {
            displayBreadcrumbTrails();
        });

        // Quick search function
        function quickSearch(type, term) {
            isManualSearch = true; // Mark as new trail
            document.getElementById('search_type').value = type;
            document.getElementById('search_term').value = term;
            document.querySelector('form').submit();
        }

        // Get all trails from localStorage
        function getTrails() {
            const trails = localStorage.getItem(TRAILS_KEY);
            return trails ? JSON.parse(trails) : [];
        }

        // Save all trails to localStorage
        function saveTrails(trails) {
            localStorage.setItem(TRAILS_KEY, JSON.stringify(trails));
        }

        // Add item to current trail
        function addToTrail(word, isNewSearch = false) {
            let trails = getTrails();

            // If this is a new search (manual form submission), create a new trail
            if (isNewSearch) {
                // Check if word is already the start of the last trail
                if (trails.length > 0 && trails[trails.length - 1][0] === word) {
                    currentTrailIndex = trails.length - 1;
                } else {
                    trails.push([word]);
                    currentTrailIndex = trails.length - 1;
                }
            } else {
                // Continuing from a click - add to current trail
                if (currentTrailIndex === -1 || currentTrailIndex >= trails.length) {
                    trails.push([word]);
                    currentTrailIndex = trails.length - 1;
                } else {
                    // Add to current trail if not already the last item
                    const currentTrail = trails[currentTrailIndex];
                    if (currentTrail.length === 0 || currentTrail[currentTrail.length - 1] !== word) {
                        currentTrail.push(word);
                    }
                }
            }

            saveTrails(trails);
            displayBreadcrumbTrails();
        }

        // Display all breadcrumb trails
        function displayBreadcrumbTrails() {
            const trails = getTrails();
            const trailsContainer = document.getElementById('breadcrumbTrails');
            const itemsContainer = document.getElementById('trailsContainer');

            if (trails.length === 0) {
                trailsContainer.style.display = 'none';
                return;
            }

            trailsContainer.style.display = 'block';
            itemsContainer.innerHTML = '';

            trails.forEach((trail, trailIndex) => {
                const trailDiv = document.createElement('div');
                trailDiv.className = 'single-trail';
                if (trailIndex === currentTrailIndex) {
                    trailDiv.style.borderLeftColor = '#764ba2';
                    trailDiv.style.backgroundColor = '#f0f0ff';
                }

                // Trail header
                const headerDiv = document.createElement('div');
                headerDiv.className = 'trail-header';
                headerDiv.innerHTML = `
                    <small class="text-muted">
                        <i class="bi bi-map"></i> Pfad ${trailIndex + 1}
                    </small>
                    <button class="btn btn-sm btn-outline-secondary"
                            style="font-size: 0.7rem; padding: 0.1rem 0.3rem;"
                            onclick="deleteTrail(${trailIndex})">
                        <i class="bi bi-x"></i>
                    </button>
                `;
                trailDiv.appendChild(headerDiv);

                // Trail items
                const itemsDiv = document.createElement('div');
                trail.forEach((word, index) => {
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-primary breadcrumb-badge breadcrumb-item';
                    // Use number icons 1-9, then just show the number for 10+
                    const iconHtml = index < 9 ? `<i class="bi bi-${index + 1}-circle"></i>` : `<strong>${index + 1}.</strong>`;
                    badge.innerHTML = `${iconHtml} ${escapeHtml(word)}`;
                    badge.onclick = function() {
                        currentTrailIndex = trailIndex;
                        searchWordInTrail(word, trailIndex);
                    };
                    itemsDiv.appendChild(badge);

                    // Add arrow between items
                    if (index < trail.length - 1) {
                        const arrow = document.createElement('span');
                        arrow.className = 'breadcrumb-item text-muted';
                        arrow.innerHTML = '<i class="bi bi-arrow-right"></i>';
                        itemsDiv.appendChild(arrow);
                    }
                });
                trailDiv.appendChild(itemsDiv);
                itemsContainer.appendChild(trailDiv);
            });
        }

        // Delete a specific trail
        function deleteTrail(trailIndex) {
            let trails = getTrails();
            trails.splice(trailIndex, 1);
            if (currentTrailIndex === trailIndex) {
                currentTrailIndex = trails.length > 0 ? trails.length - 1 : -1;
            } else if (currentTrailIndex > trailIndex) {
                currentTrailIndex--;
            }
            saveTrails(trails);
            displayBreadcrumbTrails();
        }

        // Clear all trails
        function clearAllTrails() {
            if (confirm('Möchten Sie alle Suchpfade wirklich löschen?')) {
                localStorage.removeItem(TRAILS_KEY);
                currentTrailIndex = -1;
                displayBreadcrumbTrails();
            }
        }

        // Search for a word from breadcrumb (continue in same trail)
        function searchWordInTrail(word, trailIndex) {
            currentTrailIndex = trailIndex;
            // Don't add to trail again, just search
            document.getElementById('search_type').value = 'starting';
            document.getElementById('search_term').value = word;
            document.querySelector('form').submit();
        }

        // Search by suffix (ending)
        function searchBySuffix(suffix) {
            // Add to trail (continue current trail)
            addToTrail(suffix, false);

            // Search for words ending with this suffix
            document.getElementById('search_type').value = 'ending';
            document.getElementById('search_term').value = suffix;
            // Don't trigger form submit handler's addToTrail
            skipFormHandler = true;
            document.querySelector('form').submit();
        }

        // Search by prefix (starting)
        function searchByPrefix(prefix) {
            // Add to trail (continue current trail)
            addToTrail(prefix, false);

            // Search for words starting with this prefix
            document.getElementById('search_type').value = 'starting';
            document.getElementById('search_term').value = prefix;
            // Don't trigger form submit handler's addToTrail
            skipFormHandler = true;
            document.querySelector('form').submit();
        }

        // Select result and continue search
        function selectResult(word) {
            searchWord(word);
        }

        // Escape HTML for safe display
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Detect if this is a manual search (new trail) or continuation
        let isManualSearch = false;
        let skipFormHandler = false;

        // Mark when user types in search box
        document.getElementById('search_term').addEventListener('input', function() {
            isManualSearch = true;
        });

        // Add current search term to trail after page loads (only if results found)
        if (hasResults && lastSearchTerm) {
            // This runs after page load with results
            // Check if we need to add to trail (not already there from button click)
            const trails = getTrails();
            let shouldAdd = true;

            // Check if last term in current trail is already this search term
            if (currentTrailIndex >= 0 && currentTrailIndex < trails.length) {
                const currentTrail = trails[currentTrailIndex];
                if (currentTrail.length > 0 && currentTrail[currentTrail.length - 1] === lastSearchTerm) {
                    shouldAdd = false;
                }
            }

            if (shouldAdd) {
                // Determine if this was a manual search or continuation
                // If no trails exist or we have multiple trails, this might be manual
                const wasManualSearch = trails.length === 0 ||
                    (currentTrailIndex === -1 && trails.length > 0);
                addToTrail(lastSearchTerm, wasManualSearch);
            }
        }
    </script>
</body>
</html>
