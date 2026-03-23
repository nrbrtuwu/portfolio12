<?php
include 'adatbazis.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
$pdo->exec("SET NAMES utf8mb4");
$pdo->exec("SET CHARACTER SET utf8mb4");

$search_mode = $_POST['search_mode'] ?? 'simple';
$simple_search = $_POST['simple_search'] ?? '';

$query = "SELECT m.mu_id, m.mu_cim, m.me_leiras, m.mu_kelt, m.mu_link, 
          s.sz_neve, s.sz_szul_ido, s.sz_halala, s.sz_leiras, s.sz_kep, 
          g.mufaj 
          FROM művek m
          JOIN szerzők s ON m.sz_id = s.sz_id
          JOIN műfajok g ON m.mufaj_id = g.mufaj_id
          WHERE 1";

$params = [];

if ($search_mode === 'simple' && !empty($simple_search)) {
    $search_terms = array_map('trim', explode(',', $simple_search));
    $search_conditions = [];
    
    foreach ($search_terms as $index => $term) {
        if (!empty($term)) {
            $param_name_base = ":term{$index}";
            $condition_parts = [];
            
            // Create separate parameter names for each field
            $condition_parts[] = "s.sz_neve LIKE {$param_name_base}_name";
            $condition_parts[] = "m.mu_cim LIKE {$param_name_base}_title";
            $condition_parts[] = "g.mufaj LIKE {$param_name_base}_genre";
            $condition_parts[] = "s.sz_szul_ido LIKE {$param_name_base}_birth";
            $condition_parts[] = "s.sz_halala LIKE {$param_name_base}_death";
            $condition_parts[] = "m.me_leiras LIKE {$param_name_base}_desc";
            $condition_parts[] = "s.sz_leiras LIKE {$param_name_base}_author_desc";
            
            $search_conditions[] = "(" . implode(" OR ", $condition_parts) . ")";
            
            // Add parameters with unique names
            $params["{$param_name_base}_name"] = "%{$term}%";
            $params["{$param_name_base}_title"] = "%{$term}%";
            $params["{$param_name_base}_genre"] = "%{$term}%";
            $params["{$param_name_base}_birth"] = "%{$term}%";
            $params["{$param_name_base}_death"] = "%{$term}%";
            $params["{$param_name_base}_desc"] = "%{$term}%";
            $params["{$param_name_base}_author_desc"] = "%{$term}%";
        }
    }
    
    if (!empty($search_conditions)) {
        $query .= " AND (" . implode(" AND ", $search_conditions) . ")";  // Changed OR to AND
    }
} else {
    // Handle advanced search parameters
    if (!empty($_POST['author'])) {
        $query .= " AND s.sz_id = :author";
        $params[':author'] = $_POST['author'];
    }
    
    if (!empty($_POST['genre'])) {
        $query .= " AND g.mufaj_id = :genre";
        $params[':genre'] = $_POST['genre'];
    }
    
    // Add other advanced search conditions as needed
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($results); 