<?php
include 'api/adatbazis.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
$pdo->exec("SET NAMES utf8mb4");
$pdo->exec("SET CHARACTER SET utf8mb4");

// Szűrő értékek előkészítése
$search_mode = isset($_GET['search_mode']) ? $_GET['search_mode'] : 'simple';
$simple_search = isset($_GET['simple_search']) ? $_GET['simple_search'] : '';

$filter_author = isset($_GET['author']) ? $_GET['author'] : '';
$filter_genre = isset($_GET['genre']) ? $_GET['genre'] : '';
$filter_birth_from = isset($_GET['birth_from']) ? $_GET['birth_from'] : '';
$filter_birth_to = isset($_GET['birth_to']) ? $_GET['birth_to'] : '';
$filter_death_from = isset($_GET['death_from']) ? $_GET['death_from'] : '';
$filter_death_to = isset($_GET['death_to']) ? $_GET['death_to'] : '';

// Lekérdezés az összes szerző és műfaj lekérésére a legördülőkhöz
$authors = $pdo->query("SELECT * FROM szerzők")->fetchAll();
$genres = $pdo->query("SELECT * FROM műfajok")->fetchAll();

// Alap lekérdezés
$query = "SELECT s.sz_id, s.sz_neve, s.sz_szul_ido, s.sz_halala, s.sz_leiras, s.sz_kep
          FROM szerzők s
          WHERE 1";

// Szűrési feltételek
$authorConditions = [];
$params = [];

if ($search_mode === 'simple' && !empty($simple_search)) {
    $search_terms = array_map('trim', explode(',', $simple_search));
    
    // First get all works that match the search terms
    $workQuery = "SELECT DISTINCT m.sz_id 
                 FROM művek m 
                 WHERE ";
    $workConditions = [];
    $workParams = [];
    
    foreach ($search_terms as $key => $term) {
        if (!empty($term)) {
            $workConditions[] = "(
                m.mu_cim LIKE ? OR 
                m.me_leiras LIKE ?
            )";
            $workParams[] = '%' . $term . '%';
            $workParams[] = '%' . $term . '%';
        }
    }
    
    if (!empty($workConditions)) {
        $workQuery .= implode(" OR ", $workConditions);
        $workStmt = $pdo->prepare($workQuery);
        $workStmt->execute($workParams);
        $matchingAuthorIds = $workStmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    // Now build the author conditions
    foreach ($search_terms as $key => $term) {
        if (!empty($term)) {
            $param_name = ":term" . $key;
            $authorConditions[] = "(
                s.sz_neve LIKE ? OR 
                s.sz_szul_ido LIKE ? OR 
                s.sz_halala LIKE ? OR 
                s.sz_leiras LIKE ? OR
                s.sz_id IN (SELECT sz_id FROM művek WHERE mu_cim LIKE ? OR me_leiras LIKE ?)
            )";
            $params[] = '%' . $term . '%';
            $params[] = '%' . $term . '%';
            $params[] = '%' . $term . '%';
            $params[] = '%' . $term . '%';
            $params[] = '%' . $term . '%';
            $params[] = '%' . $term . '%';
        }
    }
} else {
    // Advanced search conditions for authors
    if ($filter_author != '') {
        $authorConditions[] = "s.sz_id = ?";
        $params[] = $filter_author;
    }
    
    // Szűrés születési dátumra (tól-ig)
    if ($filter_birth_from != '' && $filter_birth_to != '') {
        $authorConditions[] = "s.sz_szul_ido BETWEEN ? AND ?";
        $params[] = $filter_birth_from;
        $params[] = $filter_birth_to;
    } elseif ($filter_birth_from != '') {
        $authorConditions[] = "s.sz_szul_ido >= ?";
        $params[] = $filter_birth_from;
    } elseif ($filter_birth_to != '') {
        $authorConditions[] = "s.sz_szul_ido <= ?";
        $params[] = $filter_birth_to;
    }

    // Szűrés halálozási dátumra (tól-ig)
    if ($filter_death_from != '' && $filter_death_to != '') {
        $authorConditions[] = "s.sz_halala BETWEEN ? AND ?";
        $params[] = $filter_death_from;
        $params[] = $filter_death_to;
    } elseif ($filter_death_from != '') {
        $authorConditions[] = "s.sz_halala >= ?";
        $params[] = $filter_death_from;
    } elseif ($filter_death_to != '') {
        $authorConditions[] = "s.sz_halala <= ?";
        $params[] = $filter_death_to;
    }
}

// Add author conditions to query
if (!empty($authorConditions)) {
    $query .= " AND (" . implode(" OR ", $authorConditions) . ")";
}

// Lekérdezés előkészítése szerzőkre
$stmt = $pdo->prepare($query);

// Lekérdezés végrehajtása szerzőkre
$stmt->execute($params);
$filteredAuthors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Művek lekérdezése minden szűrt szerzőhöz
$authorWorks = [];
if (!empty($filteredAuthors)) {
    $authorIds = array_column($filteredAuthors, 'sz_id');
    $placeholders = implode(',', array_fill(0, count($authorIds), '?'));
    
    $workQuery = "SELECT m.mu_id, m.sz_id, m.mu_cim, m.me_leiras, m.mu_kelt, m.mu_link, g.mufaj 
                 FROM művek m
                 JOIN műfajok g ON m.mufaj_id = g.mufaj_id
                 WHERE m.sz_id IN ($placeholders)";
    
    // Add work search conditions for simple search
    if ($search_mode === 'simple' && !empty($simple_search)) {
        $search_terms = array_map('trim', explode(',', $simple_search));
        $workConditions = [];
        $workParams = [];
        
        // Add author IDs as parameters
        foreach ($authorIds as $id) {
            $workParams[] = $id;
        }
        
        foreach ($search_terms as $key => $term) {
            if (!empty($term)) {
                $workConditions[] = "(m.mu_cim LIKE ? OR m.me_leiras LIKE ?)";
                $workParams[] = '%' . $term . '%';
                $workParams[] = '%' . $term . '%';
            }
        }
        
        if (!empty($workConditions)) {
            $workQuery .= " AND (" . implode(" OR ", $workConditions) . ")";
        }
    }
    // Add genre filter if specified for advanced search
    elseif ($filter_genre != '' && $search_mode === 'advanced') {
        $workQuery .= " AND g.mufaj_id = ?";
        $workParams = array_merge($authorIds, [$filter_genre]);
    } else {
        $workParams = $authorIds;
    }
    
    $workStmt = $pdo->prepare($workQuery);
    $workStmt->execute($workParams);
    $allWorks = $workStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group works by author ID
    foreach ($allWorks as $work) {
        $authorWorks[$work['sz_id']][] = $work;
    }
}
?>

<!DOCTYPE html>
<html lang="hu" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Művek Listázása és Szűrése</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            transition: background-color 0.3s, color 0.3s;
        }
        body.dark-mode {
            background-color: #1a1a1a !important;
            color: #fff !important;
        }
        .dark-mode .container {
            background-color: transparent;
        }
        .dark-mode .card {
            background-color: #2d2d2d !important;
            border-color: #404040 !important;
        }
        .dark-mode .table {
            color: #fff !important;
        }
        .dark-mode .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }
        .dark-mode .table-hover > tbody > tr:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
        }
        .dark-mode .form-control,
        .dark-mode .form-select {
            background-color: #1a1a1a !important;
            border-color: #404040 !important;
            color: #fff !important;
        }
        .dark-mode .form-control::placeholder {
            color: #888 !important;
        }
        .dark-mode .form-control:focus,
        .dark-mode .form-select:focus {
            background-color: #1a1a1a !important;
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }
        .dark-mode .btn-primary {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }
        .dark-mode .btn-outline-primary {
            color: #0d6efd !important;
            border-color: #0d6efd !important;
        }
        .dark-mode .btn-outline-primary:hover {
            color: #fff !important;
            background-color: #0d6efd !important;
        }
        .dark-mode .form-check-input {
            background-color: #1a1a1a !important;
            border-color: #404040 !important;
        }
        .dark-mode .form-check-input:checked {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }
        .dark-mode .form-label,
        .dark-mode .card-title {
            color: #fff !important;
        }
        .dark-mode .table thead th {
            background-color: #2d2d2d !important;
            border-color: #404040 !important;
            color: #fff !important;
        }
        .dark-mode .table td {
            border-color: #404040 !important;
        }
        .dark-mode .img-thumbnail {
            background-color: #2d2d2d !important;
            border-color: #404040 !important;
        }
        /* Switch styles */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
            border: 2px solid transparent;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #2196F3;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        .dark-mode .slider {
            border-color: white;
        }
        .search-container {
            margin-bottom: 20px;
        }
        .filter-form {
            margin-bottom: 20px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .card {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table td, 
        .table th {
            vertical-align: middle;
            text-align: center;
        }
        .img-thumbnail {
            max-width: 100px;
            height: auto;
        }
        .btn-outline-primary {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        /* Collapsible table styles */
        .author-row {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .author-row:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        .dark-mode .author-row:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .works-container {
            display: none;
            padding: 0;
        }
        .works-container.show {
            display: table-row;
        }
        .works-table {
            margin: 0;
            width: 100%;
        }
        .works-table th, 
        .works-table td {
            width: 25%;
            padding: 12px 15px;
            text-align: center;
            vertical-align: middle;
        }
        .works-table tbody tr {
            height: 60px;
        }
        .works-table th {
            background-color: rgba(0, 0, 0, 0.03);
        }
        .dark-mode .works-table th {
            background-color: rgba(255, 255, 255, 0.05);
        }
        .works-cell {
            padding: 0 !important;
        }
        .expand-icon {
            display: inline-block;
            width: 24px;
            height: 24px;
            line-height: 24px;
            text-align: center;
            transition: transform 0.2s;
        }
        .expanded .expand-icon {
            transform: rotate(90deg);
        }
        .works-container .p-3 {
            padding: 0 !important;
        }
        .expanded {
            background-color: rgba(0, 123, 255, 0.03);
        }
        .dark-mode .expanded {
            background-color: rgba(255, 255, 255, 0.03);
        }
        /* Update the description column style */
        .table th:nth-child(5),  /* Szerző Leírása column header */
        .table td:nth-child(5) { /* Szerző Leírása column cells */
            width: 250px;
            word-wrap: break-word;
        }
        
        /* Add tooltip on hover */
        .table td:nth-child(5):hover {
            white-space: normal;
            overflow: visible;
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Művek Listázása és Szűrése</h1>
            <div class="d-flex align-items-center gap-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="darkModeToggle">
                    <label class="form-check-label" for="darkModeToggle">Sötét mód</label>
                </div>
                <a href="login.php" class="btn btn-primary">Admin felület</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Keresés</h5>
                    <div class="search-mode-switch">
                        <label class="switch">
                            <input type="checkbox" id="searchModeToggle" <?= $search_mode === 'advanced' ? 'checked' : '' ?>>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                <div id="simpleSearch" class="search-container" <?= $search_mode === 'advanced' ? 'style="display: none;"' : '' ?>>
                    <form id="simpleSearchForm" method="GET" action="index.php">
                        <input type="hidden" name="search_mode" value="simple">
                        <div class="input-group">
                            <input type="text" name="simple_search" class="form-control" 
                                placeholder="Keresés (használj vesszőt több kifejezéshez)" 
                                value="<?= htmlspecialchars($simple_search) ?>"
                                autocomplete="off">
                        </div>
                    </form>
                </div>

                <div id="advancedSearch" class="filter-form" <?= $search_mode === 'advanced' ? '' : 'style="display: none;"' ?>>
                    <form method="GET" action="index.php" class="row g-3">
                        <input type="hidden" name="search_mode" value="advanced">
                        <div class="col-md-6">
                            <label for="author" class="form-label">Szerző:</label>
                            <select name="author" class="form-select">
                                <option value="">Minden szerző</option>
                                <?php foreach ($authors as $author): ?>
                                    <option value="<?= $author['sz_id']; ?>" <?= $filter_author == $author['sz_id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($author['sz_neve']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="genre" class="form-label">Műfaj:</label>
                            <select name="genre" class="form-select">
                                <option value="">Minden műfaj</option>
                                <?php foreach ($genres as $genre): ?>
                                    <option value="<?= $genre['mufaj_id']; ?>" <?= $filter_genre == $genre['mufaj_id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($genre['mufaj']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="birth_from" class="form-label">Születési dátum (tól):</label>
                            <input type="date" name="birth_from" class="form-control" value="<?= htmlspecialchars($filter_birth_from); ?>">
                        </div>

                        <div class="col-md-3">
                            <label for="birth_to" class="form-label">Születési dátum (ig):</label>
                            <input type="date" name="birth_to" class="form-control" value="<?= htmlspecialchars($filter_birth_to); ?>">
                        </div>

                        <div class="col-md-3">
                            <label for="death_from" class="form-label">Halálozási dátum (tól):</label>
                            <input type="date" name="death_from" class="form-control" value="<?= htmlspecialchars($filter_death_from); ?>">
                        </div>

                        <div class="col-md-3">
                            <label for="death_to" class="form-label">Halálozási dátum (ig):</label>
                            <input type="date" name="death_to" class="form-control" value="<?= htmlspecialchars($filter_death_to); ?>">
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Szűrés</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="text-center"></th>
                        <th class="text-center">Szerző</th>
                        <th class="text-center">Születési Dátum</th>
                        <th class="text-center">Halálozási Dátum</th>
                        <th class="text-center">Szerző Leírása</th>
                        <th class="text-center">Szerző Képe</th>
                    </tr>
                </thead>
                <tbody id="resultsBody">
                    <?php if (count($filteredAuthors) > 0): ?>
                        <?php foreach ($filteredAuthors as $author): ?>
                            <tr class="author-row" data-author-id="<?= $author['sz_id'] ?>">
                                <td>
                                    <span class="expand-icon">▶</span>
                                </td>
                                <td><?= htmlspecialchars($author['sz_neve']); ?></td>
                                <td><?= htmlspecialchars($author['sz_szul_ido']); ?></td>
                                <td><?= htmlspecialchars($author['sz_halala']); ?></td>
                                <td><?= htmlspecialchars($author['sz_leiras']); ?></td>
                                <td><?= $author['sz_kep'] ? '<img src="' . htmlspecialchars($author['sz_kep']) . '" alt="Szerző képe" class="img-thumbnail">' : ''; ?></td>
                            </tr>
                            <tr class="works-container" id="works-<?= $author['sz_id'] ?>">
                                <td colspan="6" class="works-cell p-0">
                                    <div class="p-3">
                                        <?php if (isset($authorWorks[$author['sz_id']]) && !empty($authorWorks[$author['sz_id']])): ?>
                                            <table class="table table-sm works-table">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Mű Cím</th>
                                                        <th class="text-center">Műfaj</th>
                                                        <th class="text-center">Keletkezés Dátuma</th>
                                                        <th class="text-center">Mű Leírása</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($authorWorks[$author['sz_id']] as $work): ?>
                                                        <tr>
                                                            <td>
                                                                <?php if ($work['mu_link']): ?>
                                                                    <a href="<?= htmlspecialchars($work['mu_link']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                        <?= htmlspecialchars($work['mu_cim']) ?>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <?= htmlspecialchars($work['mu_cim']) ?>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= htmlspecialchars($work['mufaj']); ?></td>
                                                            <td><?= htmlspecialchars($work['mu_kelt']); ?></td>
                                                            <td><?= htmlspecialchars($work['me_leiras']); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php else: ?>
                                            <p class="text-muted text-center py-3">Nincs mű hozzárendelve ehhez a szerzőhöz.</p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Nincs találat!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchModeToggle = document.getElementById('searchModeToggle');
            const simpleSearch = document.getElementById('simpleSearch');
            const advancedSearch = document.getElementById('advancedSearch');
            const darkModeToggle = document.getElementById('darkModeToggle');
            const html = document.documentElement;
            const simpleSearchInput = document.querySelector('input[name="simple_search"]');
            const simpleSearchForm = document.getElementById('simpleSearchForm');
            let searchTimeout;
            
            // Function to attach click handlers to author rows
            function attachAuthorRowHandlers() {
                const authorRows = document.querySelectorAll('.author-row');
                authorRows.forEach(row => {
                    const authorId = row.getAttribute('data-author-id');
                    const worksContainer = document.getElementById('works-' + authorId);
                    
                    // Remove existing click handler if any
                    row.removeEventListener('click', handleAuthorRowClick);
                    // Add click handler
                    row.addEventListener('click', handleAuthorRowClick);
                });
            }

            // Author row click handler function
            function handleAuthorRowClick() {
                const authorId = this.getAttribute('data-author-id');
                const worksContainer = document.getElementById('works-' + authorId);
                
                // Close any currently open rows except the one being clicked
                const allAuthorRows = document.querySelectorAll('.author-row');
                const allWorksContainers = document.querySelectorAll('.works-container');
                
                allAuthorRows.forEach(row => {
                    if (row !== this) {
                        row.classList.remove('expanded');
                    }
                });
                
                allWorksContainers.forEach(container => {
                    if (container !== worksContainer) {
                        container.classList.remove('show');
                    }
                });

                // Toggle the clicked row
                this.classList.toggle('expanded');
                worksContainer.classList.toggle('show');
            }

            // Function to expand author row
            function expandAuthorRow(authorId) {
                // Close any currently open rows first
                const allAuthorRows = document.querySelectorAll('.author-row');
                const allWorksContainers = document.querySelectorAll('.works-container');
                
                allAuthorRows.forEach(row => {
                    row.classList.remove('expanded');
                });
                
                allWorksContainers.forEach(container => {
                    container.classList.remove('show');
                });

                // Now expand the target row
                const row = document.querySelector(`.author-row[data-author-id="${authorId}"]`);
                const worksContainer = document.getElementById('works-' + authorId);
                if (row && worksContainer) {
                    row.classList.add('expanded');
                    worksContainer.classList.add('show');
                }
            }

            // Attach handlers on initial load
            attachAuthorRowHandlers();
            
            // Handle instant search
            simpleSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const formData = new FormData(simpleSearchForm);
                    const searchParams = new URLSearchParams(formData);
                    
                    // Update URL without page refresh
                    window.history.pushState({}, '', 'index.php?' + searchParams.toString());
                    
                    // Fetch results
                    fetch('index.php?' + searchParams.toString())
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newResults = doc.getElementById('resultsBody').innerHTML;
                            document.getElementById('resultsBody').innerHTML = newResults;
                            
                            // Reattach event listeners and auto-expand matching rows
                            const authorRows = document.querySelectorAll('.author-row');
                            const searchTerms = simpleSearchInput.value.toLowerCase().split(',').map(term => term.trim());
                            
                            authorRows.forEach(row => {
                                const authorId = row.getAttribute('data-author-id');
                                const worksContainer = document.getElementById('works-' + authorId);
                                const worksTable = worksContainer.querySelector('.works-table');
                                
                                // Remove existing click handler and add new one
                                row.removeEventListener('click', handleAuthorRowClick);
                                row.addEventListener('click', handleAuthorRowClick);
                                
                                if (worksTable && searchTerms.some(term => term !== '')) {
                                    const workRows = worksTable.querySelectorAll('tbody tr');
                                    let hasMatchingWork = false;
                                    
                                    workRows.forEach(workRow => {
                                        const workTitle = workRow.querySelector('td:first-child').textContent.toLowerCase();
                                        const workDesc = workRow.querySelector('td:last-child').textContent.toLowerCase();
                                        const isMatch = searchTerms.some(term => 
                                            term && (workTitle.includes(term) || workDesc.includes(term))
                                        );
                                        
                                        // Show/hide individual work rows
                                        workRow.style.display = isMatch ? '' : 'none';
                                        if (isMatch) hasMatchingWork = true;
                                    });
                                    
                                    // Auto-expand if there are matching works
                                    if (hasMatchingWork) {
                                        expandAuthorRow(authorId);
                                    }
                                }
                            });
                        });
                }, 300);
            });
            
            // Handle refreshes - redirect to clean URL if this was a refresh
            if (window.performance && window.performance.navigation.type === 1) {
                localStorage.setItem('searchMode', searchModeToggle.checked ? 'advanced' : 'simple');
                
                if (window.location.search !== '') {
                    window.location.href = 'index.php';
                    return;
                }
            }
            
            // On initial clean page load, use localStorage preference
            if (window.location.search === '') {
                const savedMode = localStorage.getItem('searchMode');
                
                if (savedMode === 'simple') {
                    searchModeToggle.checked = false;
                    simpleSearch.style.display = 'block';
                    advancedSearch.style.display = 'none';
                } else if (savedMode === 'advanced') {
                    searchModeToggle.checked = true;
                    simpleSearch.style.display = 'none';
                    advancedSearch.style.display = 'block';
                }
            } else {
                localStorage.setItem('searchMode', searchModeToggle.checked ? 'advanced' : 'simple');
            }
            
            // Check for saved dark mode preference
            if (localStorage.getItem('darkMode') === 'true') {
                document.body.classList.add('dark-mode');
                html.setAttribute('data-bs-theme', 'dark');
                darkModeToggle.checked = true;
            }

            // Toggle dark mode
            darkModeToggle.addEventListener('change', function() {
                if (this.checked) {
                    document.body.classList.add('dark-mode');
                    html.setAttribute('data-bs-theme', 'dark');
                    localStorage.setItem('darkMode', 'true');
                } else {
                    document.body.classList.remove('dark-mode');
                    html.setAttribute('data-bs-theme', 'light');
                    localStorage.setItem('darkMode', 'false');
                }
            });

            // Toggle search modes
            searchModeToggle.addEventListener('change', function() {
                const mode = this.checked ? 'advanced' : 'simple';
                
                if (mode === 'advanced') {
                    simpleSearch.style.display = 'none';
                    advancedSearch.style.display = 'block';
                } else {
                    simpleSearch.style.display = 'block';
                    advancedSearch.style.display = 'none';
                }
                
                localStorage.setItem('searchMode', mode);
            });
        });
    </script>
</body>
</html>

