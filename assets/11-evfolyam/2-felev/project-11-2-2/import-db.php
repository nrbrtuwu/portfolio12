<?php
function logMessage($message, $type = 'info') {
    $color = match($type) {
        'error' => 'red',
        'success' => 'green',
        'warning' => 'orange',
        default => 'black'
    };
    echo "<div style='color: $color; margin: 5px 0;'>$message</div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'] ?? '127.0.0.1';
    $username = $_POST['username'] ?? 'root';
    $password = $_POST['password'] ?? '';
    $database = 'projektnorbimark';

    try {
        // Create connection
        $conn = new mysqli($host, $username, $password);
        
        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Kapcsolódási hiba: " . $conn->connect_error);
        }
        
        logMessage("Sikeres kapcsolódás a MySQL szerverhez", 'success');
        
        // Create database if it doesn't exist
        $sql = "CREATE DATABASE IF NOT EXISTS $database";
        if ($conn->query($sql) === TRUE) {
            logMessage("Adatbázis sikeresen létrehozva vagy már létezik", 'success');
        } else {
            throw new Exception("Hiba az adatbázis létrehozásakor: " . $conn->error);
        }
        
        // Select the database
        $conn->select_db($database);
        logMessage("Adatbázis kiválasztva: $database", 'info');
        
        // Read SQL file
        $sql = file_get_contents('projektdb.sql');
        logMessage("SQL fájl sikeresen beolvasva", 'success');
        
        // Split SQL into individual queries
        $queries = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($queries as $query) {
            if (empty($query)) continue;
            
            if ($conn->query($query)) {
                if (stripos($query, 'CREATE TABLE') !== false) {
                    preg_match('/CREATE TABLE `?(\w+)`?/', $query, $matches);
                    if (isset($matches[1])) {
                        logMessage("Tábla létrehozva: {$matches[1]}", 'success');
                    }
                } elseif (stripos($query, 'INSERT INTO') !== false) {
                    preg_match('/INSERT INTO `?(\w+)`?/', $query, $matches);
                    if (isset($matches[1])) {
                        logMessage("Adatok beszúrva a táblába: {$matches[1]}", 'success');
                    }
                } elseif (stripos($query, 'ALTER TABLE') !== false) {
                    logMessage("Tábla módosítva: " . substr($query, 0, 100) . "...", 'info');
                }
            } else {
                if ($conn->errno == 1062) { // Duplicate entry
                    logMessage("Figyelmeztetés: Duplikált bejegyzés - " . $conn->error, 'warning');
                } else {
                    logMessage("Hiba a lekérdezés végrehajtásakor: " . $conn->error, 'error');
                }
            }
        }
        
        logMessage("Adatbázis importálás befejeződött", 'success');
        
    } catch (Exception $e) {
        logMessage("Hiba történt: " . $e->getMessage(), 'error');
    } finally {
        if (isset($conn)) {
            $conn->close();
            logMessage("Adatbázis kapcsolat lezárva", 'info');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adatbázis Importálás</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        #log {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <h1>Adatbázis Importálás</h1>
    
    <form method="POST">
        <div class="form-group">
            <label for="host">Szerver IP:</label>
            <input type="text" id="host" name="host" value="127.0.0.1" required>
        </div>
        
        <div class="form-group">
            <label for="username">Felhasználónév:</label>
            <input type="text" id="username" name="username" value="root" required>
        </div>
        
        <div class="form-group">
            <label for="password">Jelszó:</label>
            <input type="password" id="password" name="password">
        </div>
        
        <button type="submit">Importálás indítása</button>
    </form>
    
    <div id="log">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<h3>Importálás napló:</h3>";
        }
        ?>
    </div>
</body>
</html> 