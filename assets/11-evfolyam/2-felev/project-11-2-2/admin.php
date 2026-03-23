<?php
header('Content-Type: text/html; charset=utf-8');
include 'api/adatbazis.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
$pdo->exec("SET NAMES utf8mb4");
$pdo->exec("SET CHARACTER SET utf8mb4");

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

function refreshData($pdo) {
    // Friss adatok lekérdezése az adatbázisból
    $szerzok = $pdo->query("SELECT * FROM szerzők")->fetchAll();
    $mufajok = $pdo->query("SELECT * FROM műfajok")->fetchAll();
    $muvek = $pdo->query("SELECT m.*, s.sz_neve, g.mufaj FROM művek m 
                          JOIN szerzők s ON m.sz_id = s.sz_id 
                          JOIN műfajok g ON m.mufaj_id = g.mufaj_id")->fetchAll();
    
    return ['szerzok' => $szerzok, 'mufajok' => $mufajok, 'muvek' => $muvek];
}

// AJAX kérések kezelése
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Admin registration
    if (isset($_POST['submit_admin'])) {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
        try {
            $stmt->execute([$username, $password]);
            $_SESSION['success_message'] = "Admin sikeresen regisztrálva!";
            header("Location: admin.php");
            exit;
        } catch (PDOException $e) {
            $error = "Hiba: a felhasználónév már létezik vagy adatbázis hiba.";
        }
    }

    // Szerző törlése
    if (isset($_GET['action']) && $_GET['action'] === 'delete_author' && isset($_GET['id'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM szerzők WHERE sz_id = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode(['success' => true]);
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
    
    // Műfaj törlése
    if (isset($_GET['action']) && $_GET['action'] === 'delete_genre' && isset($_GET['id'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM műfajok WHERE mufaj_id = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode(['success' => true]);
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
    
    // Mű törlése
    if (isset($_GET['action']) && $_GET['action'] === 'delete_work' && isset($_GET['id'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM művek WHERE mu_id = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode(['success' => true]);
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    if (isset($_POST['submit_author'])) {
        $name = $_POST['sz_neve'];
        $birth_date = $_POST['sz_szul_ido'];
        $birth_place = $_POST['sz_szul_hely'];
        $death_date = $_POST['sz_halala'];
        $description = $_POST['sz_leiras'];
        $image = $_POST['sz_kep'] ?: '';

        if (isset($_POST['edit_id'])) {
            $stmt = $pdo->prepare("UPDATE szerzők SET sz_neve=?, sz_szul_ido=?, sz_szul_hely=?, 
                                  sz_halala=?, sz_leiras=?, sz_kep=? WHERE sz_id=?");
            $stmt->execute([$name, $birth_date, $birth_place, $death_date, $description, $image, $_POST['edit_id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO szerzők (sz_neve, sz_szul_ido, sz_szul_hely, sz_halala, sz_leiras, sz_kep) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $birth_date, $birth_place, $death_date, $description, $image]);
        }
        
        // Adatok újralekérdezése
        $data = refreshData($pdo);
        $szerzok = $data['szerzok'];
        $mufajok = $data['mufajok'];
        $muvek = $data['muvek'];
    }

    if (isset($_POST['submit_genre'])) {
        $genre = $_POST['mufaj'];
        $stmt = $pdo->prepare("INSERT INTO műfajok (mufaj) VALUES (?)");
        $stmt->execute([$genre]);
        
        // Adatok újralekérdezése
        $data = refreshData($pdo);
        $szerzok = $data['szerzok'];
        $mufajok = $data['mufajok'];
        $muvek = $data['muvek'];
    }

    if (isset($_POST['submit_work'])) {
        $author_id = $_POST['sz_id'];
        $title = $_POST['mu_cim'];
        $genre_id = $_POST['mufaj_id'];
        $description = $_POST['me_leiras'] ?? '';
        $date = $_POST['mu_kelt'];
        $link = $_POST['mu_link'] ?? '';

        try {
            if (isset($_POST['edit_work_id'])) {
                // Update existing work
                $stmt = $pdo->prepare("UPDATE művek SET sz_id=?, mu_cim=?, mufaj_id=?, me_leiras=?, mu_kelt=?, mu_link=? 
                                     WHERE mu_id=?");
                $stmt->execute([$author_id, $title, $genre_id, $description, $date, $link, $_POST['edit_work_id']]);
            } else {
                // Insert new work
                $stmt = $pdo->prepare("INSERT INTO művek (sz_id, mu_cim, mufaj_id, me_leiras, mu_kelt, mu_link) 
                                     VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$author_id, $title, $genre_id, $description, $date, $link]);
            }
            
            // Adatok újralekérdezése
            $data = refreshData($pdo);
            $szerzok = $data['szerzok'];
            $mufajok = $data['mufajok'];
            $muvek = $data['muvek'];
        } catch (PDOException $e) {
            echo "Hiba történt: " . $e->getMessage();
        }
    }

    if (isset($_POST['submit_author']) || isset($_POST['submit_genre']) || isset($_POST['submit_work'])) {
        // Sikeres mentés után átirányítás
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Szerző adatainak lekérése szerkesztéshez
if (isset($_GET['action']) && $_GET['action'] === 'get_author' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM szerzők WHERE sz_id = ?");
    $stmt->execute([$_GET['id']]);
    $author = $stmt->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($author);
    exit;
}

// Mű adatainak lekérése szerkesztéshez
if (isset($_GET['action']) && $_GET['action'] === 'get_work' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM művek WHERE mu_id = ?");
    $stmt->execute([$_GET['id']]);
    $work = $stmt->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($work);
    exit;
}

// Kezdeti adatok lekérdezése
$data = refreshData($pdo);
$szerzok = $data['szerzok'];
$mufajok = $data['mufajok'];
$muvek = $data['muvek'];

// Get available images from /img directory
$images = array_diff(scandir('img'), array('.', '..'));

// AJAX kérés kezelése az adatok frissítéséhez
if (isset($_GET['refresh']) && $_GET['refresh'] === 'true') {
    header('Content-Type: application/json');
    echo json_encode(refreshData($pdo));
    exit;
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Felület</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Dark mode input styles */
        body.dark-mode input[type="url"],
        body.dark-mode input[type="text"],
        body.dark-mode input[type="date"],
        body.dark-mode input[type="password"],
        body.dark-mode select,
        body.dark-mode textarea {
            background-color: #2d2d2d !important;
            border-color: #404040 !important;
            color: #fff !important;
        }

        body.dark-mode input[type="url"]::placeholder,
        body.dark-mode input[type="password"]::placeholder {
            color: #888 !important;
        }

        body.dark-mode input[type="url"]:focus,
        body.dark-mode input[type="password"]:focus {
            background-color: #2d2d2d !important;
            border-color: #0d6efd !important;
            color: #fff !important;
        }

        /* Hide forms during work editing */
        .author-form.hidden,
        .genre-form.hidden {
            display: none !important;
        }
    </style>
</head>
<body>
<div class="theme-switch-wrapper">
    <label class="theme-switch" for="checkbox">
        <input type="checkbox" id="checkbox" />
        <div class="slider"></div>
    </label>
    <em>Sötét mód</em>
</div>
<a href="api/logout.php" style="margin-bottom: 20px;">Kijelentkezés</a>

<div class="container">
    <h1>Admin Felület</h1>

    <ul class="nav-tabs">
        <li data-tab="new">Új rekord hozzáadása</li>
        <li data-tab="edit">Rekordok szerkesztése</li>
        <li data-tab="register">Admin regisztrálása</li>
    </ul>

    <div id="new-content" class="tab-content active">
        <!-- Existing forms for adding new records -->
        <div class="author-form">
            <h2>Szerző hozzáadása</h2>
            <form action="admin.php" method="POST" accept-charset="UTF-8" onsubmit="setTimeout(function() { window.location.reload(); }, 1000)">
                <label for="sz_neve">Szerző neve:</label>
                <input type="text" name="sz_neve" required><br>
                
                <label for="sz_szul_ido">Születési dátum:</label>
                <input type="date" name="sz_szul_ido"><br>
                
                <label for="sz_szul_hely">Születési hely:</label>
                <input type="text" name="sz_szul_hely"><br>
                
                <label for="sz_halala">Halál dátuma:</label>
                <input type="date" name="sz_halala"><br>
                
                <label for="sz_leiras">Leírás:</label>
                <textarea name="sz_leiras"></textarea><br>
                
                <label for="sz_kep">Kép:</label>
                <select name="sz_kep">
                    <option value="">Nincs kép</option>
                    <?php foreach ($images as $image): ?>
                        <option value="img/<?= htmlspecialchars($image) ?>"><?= htmlspecialchars($image) ?></option>
                    <?php endforeach; ?>
                </select><br>

                <button type="submit" name="submit_author">Hozzáadás</button>
            </form>
        </div>

        <hr style="margin-bottom: 20px;" class="author-form">

        <!-- Műfaj hozzáadása -->
        <div class="genre-form">
            <h2>Műfaj hozzáadása</h2>
            <form action="admin.php" method="POST" accept-charset="UTF-8" onsubmit="setTimeout(function() { window.location.reload(); }, 1000)">
                <label for="mufaj">Műfaj neve:</label>
                <input type="text" name="mufaj" required><br>
                <button type="submit" name="submit_genre">Hozzáadás</button>
            </form>
        </div>

        <hr style="margin-bottom: 20px;" class="genre-form">

        <!-- Mű hozzáadása -->
        <div class="work-form">
            <h2>Mű hozzáadása</h2>
            <form action="admin.php" method="POST" accept-charset="UTF-8" onsubmit="setTimeout(function() { window.location.reload(); }, 1000)">
                <label for="sz_id">Szerző:</label>
                <select name="sz_id" required>
                    <?php foreach ($szerzok as $szerzo): ?>
                        <option value="<?= $szerzo['sz_id']; ?>"><?= htmlspecialchars($szerzo['sz_neve']); ?></option>
                    <?php endforeach; ?>
                </select><br>

                <label for="mu_cim">Mű cím:</label>
                <input type="text" name="mu_cim" required><br>

                <label for="mu_kelt">Keletkezés dátuma:</label>
                <input type="date" 
                       name="mu_kelt" 
                       placeholder="éééé. hh. nn."
                       pattern="\d{4}-\d{2}-\d{2}"
                       required><br>

                <label for="mufaj_id">Műfaj:</label>
                <select name="mufaj_id" required>
                    <?php foreach ($mufajok as $mufaj): ?>
                        <option value="<?= $mufaj['mufaj_id']; ?>"><?= htmlspecialchars($mufaj['mufaj']); ?></option>
                    <?php endforeach; ?>
                </select><br>

                <label for="me_leiras">Leírás:</label>
                <textarea name="me_leiras"></textarea><br>

                <label for="mu_link">Link:</label>
                <input type="url" name="mu_link" placeholder="https://example.com" class="form-control"><br>

                <button type="submit" name="submit_work">Hozzáadás</button>
            </form>
        </div>
    </div>

    <div id="edit-content" class="tab-content">
        <h2>Szerzők szerkesztése</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>Név</th>
                    <th>Születési dátum</th>
                    <th>Születési hely</th>
                    <th>Halál dátuma</th>
                    <th>Leírás</th>
                    <th>Kép</th>
                    <th>Műveletek</th>
                </tr>
                <?php foreach ($szerzok as $szerzo): ?>
                <tr>
                    <td><?= htmlspecialchars($szerzo['sz_neve']) ?></td>
                    <td><?= $szerzo['sz_szul_ido'] ?></td>
                    <td><?= htmlspecialchars($szerzo['sz_szul_hely']) ?></td>
                    <td><?= $szerzo['sz_halala'] ?></td>
                    <td><?= htmlspecialchars($szerzo['sz_leiras']) ?></td>
                    <td><?= htmlspecialchars($szerzo['sz_kep']) ?></td>
                    <td class="action-buttons">
                        <button onclick="editAuthor(<?= $szerzo['sz_id'] ?>)" class="edit-btn">Szerkesztés</button>
                        <button onclick="deleteAuthor(<?= $szerzo['sz_id'] ?>)" class="delete-btn">Törlés</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <h2>Műfajok szerkesztése</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>Műfaj</th>
                    <th>Műveletek</th>
                </tr>
                <?php foreach ($mufajok as $mufaj): ?>
                <tr>
                    <td><?= htmlspecialchars($mufaj['mufaj']) ?></td>
                    <td class="action-buttons">
                        <button onclick="deleteGenre(<?= $mufaj['mufaj_id'] ?>)" class="delete-btn">Törlés</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <h2>Művek szerkesztése</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>Cím</th>
                    <th>Szerző</th>
                    <th>Műfaj</th>
                    <th>Keletkezés dátuma</th>
                    <th>Leírás</th>
                    <th>Műveletek</th>
                </tr>
                <?php foreach ($muvek as $mu): ?>
                <tr>
                    <td>
                        <?php if ($mu['mu_link']): ?>
                            <a href="<?= htmlspecialchars($mu['mu_link']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <?= htmlspecialchars($mu['mu_cim']) ?>
                            </a>
                        <?php else: ?>
                            <?= htmlspecialchars($mu['mu_cim']) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($mu['sz_neve']) ?></td>
                    <td><?= htmlspecialchars($mu['mufaj']) ?></td>
                    <td><?= $mu['mu_kelt'] ?></td>
                    <td><?= htmlspecialchars($mu['me_leiras']) ?></td>
                    <td class="action-buttons">
                        <button onclick="editWork(<?= $mu['mu_id'] ?>)" class="edit-btn">Szerkesztés</button>
                        <button onclick="deleteWork(<?= $mu['mu_id'] ?>)" class="delete-btn">Törlés</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <div id="register-content" class="tab-content">
        <h2>Admin regisztrálása</h2>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="admin.php" method="POST" accept-charset="UTF-8">
            <div class="form-group">
                <label for="username">Felhasználónév:</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Jelszó:</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" name="submit_admin" class="btn btn-primary">Regisztráció</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get active tab from localStorage
    const activeTab = localStorage.getItem('activeTab') || 'new';
    
    // Tab switching
    const tabs = document.querySelectorAll('.nav-tabs li');
    const contents = document.querySelectorAll('.tab-content');
    
    function switchTab(tabName) {
        tabs.forEach(t => t.classList.remove('active'));
        contents.forEach(content => content.classList.remove('active'));
        
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
        document.getElementById(tabName + '-content').classList.add('active');
        
        // Save active tab to localStorage
        localStorage.setItem('activeTab', tabName);
    }
    
    // Set initial active tab
    switchTab(activeTab);
    
    // Tab click handlers
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            switchTab(this.dataset.tab);
        });
    });

    // Form submit handlers
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            // Store the current active tab
            const currentTab = document.querySelector('.nav-tabs li.active').dataset.tab;
            localStorage.setItem('activeTab', currentTab);
        });
    });
});

function editAuthor(id) {
    fetch('admin.php?action=get_author&id=' + id)
        .then(response => response.json())
        .then(author => {
            // Címek módosítása
            const h2Elements = document.querySelectorAll('h2');
            h2Elements.forEach(h2 => {
                if (h2.textContent === "Szerző hozzáadása") {
                    h2.textContent = "Szerző szerkesztése";
                }
            });
            
            document.querySelector('button[name="submit_author"]').textContent = "Szerkesztés";
            
            // Form adatok kitöltése
            document.querySelector('input[name="sz_neve"]').value = author.sz_neve;
            document.querySelector('input[name="sz_szul_ido"]').value = author.sz_szul_ido;
            document.querySelector('input[name="sz_szul_hely"]').value = author.sz_szul_hely;
            document.querySelector('input[name="sz_halala"]').value = author.sz_halala;
            document.querySelector('textarea[name="sz_leiras"]').value = author.sz_leiras;
            document.querySelector('select[name="sz_kep"]').value = author.sz_kep;
            
            // Edit ID hozzáadása
            let editInput = document.querySelector('input[name="edit_id"]');
            if (!editInput) {
                editInput = document.createElement('input');
                editInput.type = 'hidden';
                editInput.name = 'edit_id';
                document.querySelector('form').appendChild(editInput);
            }
            editInput.value = author.sz_id;
            
            // Váltás a new tabra
            document.querySelector('[data-tab="new"]').click();
        });
}

function deleteAuthor(id) {
    if (confirm('Biztosan törölni szeretné ezt a szerzőt?')) {
        // Store current tab before delete
        const currentTab = document.querySelector('.nav-tabs li.active').dataset.tab;
        localStorage.setItem('activeTab', currentTab);
        
        fetch('admin.php?action=delete_author&id=' + id, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
}

function deleteGenre(id) {
    if (confirm('Biztosan törölni szeretné ezt a műfajt?')) {
        const currentTab = document.querySelector('.nav-tabs li.active').dataset.tab;
        localStorage.setItem('activeTab', currentTab);
        
        fetch('admin.php?action=delete_genre&id=' + id, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
}

function deleteWork(id) {
    if (confirm('Biztosan törölni szeretné ezt a művet?')) {
        const currentTab = document.querySelector('.nav-tabs li.active').dataset.tab;
        localStorage.setItem('activeTab', currentTab);
        
        fetch('admin.php?action=delete_work&id=' + id, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
}

function editWork(id) {
    fetch('admin.php?action=get_work&id=' + id)
        .then(response => response.json())
        .then(work => {
            // Hide author and genre forms
            document.querySelectorAll('.author-form, .genre-form').forEach(el => {
                el.classList.add('hidden');
            });

            // Címek módosítása
            const h2Elements = document.querySelectorAll('h2');
            h2Elements.forEach(h2 => {
                if (h2.textContent === "Mű hozzáadása") {
                    h2.textContent = "Mű szerkesztése";
                }
            });
            
            document.querySelector('button[name="submit_work"]').textContent = "Szerkesztés";
            
            // Form adatok kitöltése
            document.querySelector('select[name="sz_id"]').value = work.sz_id;
            document.querySelector('input[name="mu_cim"]').value = work.mu_cim;
            document.querySelector('input[name="mu_kelt"]').value = work.mu_kelt;
            document.querySelector('select[name="mufaj_id"]').value = work.mufaj_id;
            document.querySelector('textarea[name="me_leiras"]').value = work.me_leiras || '';
            document.querySelector('input[name="mu_link"]').value = work.mu_link || '';
            
            // Edit ID hozzáadása
            const workForm = document.querySelector('.work-form form');
            let editInput = workForm.querySelector('input[name="edit_work_id"]');
            if (!editInput) {
                editInput = document.createElement('input');
                editInput.type = 'hidden';
                editInput.name = 'edit_work_id';
                workForm.appendChild(editInput);
            }
            editInput.value = work.mu_id;
            
            // Váltás a new tabra
            document.querySelector('[data-tab="new"]').click();
        });
}

// Add form submit handler to show hidden forms after submission
document.querySelector('.work-form form').addEventListener('submit', function() {
    // Show author and genre forms after form submission
    setTimeout(() => {
        document.querySelectorAll('.author-form, .genre-form').forEach(el => {
            el.classList.remove('hidden');
        });
        // Reset the work form heading
        const workFormH2 = document.querySelector('.work-form h2');
        if (workFormH2) {
            workFormH2.textContent = "Mű hozzáadása";
        }
        // Reset the submit button text
        const submitButton = document.querySelector('button[name="submit_work"]');
        if (submitButton) {
            submitButton.textContent = "Hozzáadás";
        }
    }, 100);
});

// Dark mode handling
const toggleSwitch = document.querySelector('.theme-switch input[type="checkbox"]');
const currentTheme = localStorage.getItem('theme');

if (currentTheme) {
    document.documentElement.setAttribute('data-theme', currentTheme);
    document.body.classList.toggle('dark-mode', currentTheme === 'dark');
    
    if (currentTheme === 'dark') {
        toggleSwitch.checked = true;
    }
}

function switchTheme(e) {
    if (e.target.checked) {
        document.body.classList.add('dark-mode');
        localStorage.setItem('theme', 'dark');
    } else {
        document.body.classList.remove('dark-mode');
        localStorage.setItem('theme', 'light');
    }    
}

toggleSwitch.addEventListener('change', switchTheme, false);
</script>

</body>
</html>
