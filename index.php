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
            transition: transform 0.2s;
            border-left: 4px solid #667eea;
        }
        .result-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
                    <h3 class="mb-3">
                        <i class="bi bi-list-check"></i> 
                        Gefunden: <?= count($results) ?> Daldal-Kandidaten
                    </h3>
                    
                    <div class="row g-3">
                        <?php foreach ($results as $index => $result): ?>
                            <div class="col-12">
                                <div class="card result-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
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
        function quickSearch(type, term) {
            document.getElementById('search_type').value = type;
            document.getElementById('search_term').value = term;
            document.querySelector('form').submit();
        }
    </script>
</body>
</html>
