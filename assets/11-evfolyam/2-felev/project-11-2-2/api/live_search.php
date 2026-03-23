<?php
include 'adatbazis.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
$pdo->exec("SET NAMES utf8mb4");
$pdo->exec("SET CHARACTER SET utf8mb4");

if (isset($_GET['term'])) {
    $search_terms = array_map('trim', explode(',', $_GET['term']));
    $query = "SELECT m.mu_id, m.mu_cim, m.me_leiras, m.mu_kelt, m.mu_link, 
              s.sz_neve, s.sz_szul_ido, s.sz_halala, s.sz_leiras, s.sz_kep, 
              g.mufaj 
              FROM művek m
              JOIN szerzők s ON m.sz_id = s.sz_id
              JOIN műfajok g ON m.mufaj_id = g.mufaj_id
              WHERE 1";

    $search_conditions = [];
    $params = [];
    
    foreach ($search_terms as $key => $term) {
        if (!empty($term)) {
            $param_name = ":term" . $key;
            $search_conditions[] = "(
                s.sz_neve LIKE " . $param_name . " 
                OR m.mu_cim LIKE " . $param_name . " 
                OR g.mufaj LIKE " . $param_name . " 
                OR s.sz_szul_ido LIKE " . $param_name . " 
                OR s.sz_halala LIKE " . $param_name . " 
                OR m.me_leiras LIKE " . $param_name . "
                OR s.sz_leiras LIKE " . $param_name . "
            )";
            $params[$param_name] = '%' . $term . '%';
        }
    }
    
    if (!empty($search_conditions)) {
        $query .= " AND (" . implode(" OR ", $search_conditions) . ")";
    }

    $stmt = $pdo->prepare($query);
    
    foreach ($params as $param_name => $param_value) {
        $stmt->bindValue($param_name, $param_value);
    }

    $stmt->execute();
    $works = $stmt->fetchAll();

    $html = '';
    if (count($works) > 0) {
        foreach ($works as $work) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($work['mu_cim']) . '</td>';
            $html .= '<td>' . htmlspecialchars($work['sz_neve']) . '</td>';
            $html .= '<td>' . htmlspecialchars($work['sz_szul_ido']) . '</td>';
            $html .= '<td>' . htmlspecialchars($work['sz_halala']) . '</td>';
            $html .= '<td>' . htmlspecialchars($work['mufaj']) . '</td>';
            $html .= '<td>' . htmlspecialchars($work['me_leiras']) . '</td>';
            $html .= '<td>' . htmlspecialchars($work['mu_kelt']) . '</td>';
            $html .= '<td>' . (htmlspecialchars($work['mu_link']) ? '<a href="' . htmlspecialchars($work['mu_link']) . '" target="_blank" class="btn btn-sm btn-outline-primary">Link</a>' : '') . '</td>';
            $html .= '<td>' . htmlspecialchars($work['sz_leiras']) . '</td>';
            $html .= '<td>' . (htmlspecialchars($work['sz_kep']) ? '<img src="' . htmlspecialchars($work['sz_kep']) . '" alt="Szerző képe" class="img-thumbnail">' : '') . '</td>';
            $html .= '</tr>';
        }
    } else {
        $html = '<tr><td colspan="10" class="text-center">Nincs találat!</td></tr>';
    }

    echo $html;
}
?> 