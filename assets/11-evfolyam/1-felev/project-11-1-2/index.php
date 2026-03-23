<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "koltok_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Poets adatainak lekérése
$sql = "SELECT name, birth_date, death_date, nationality, notable_works, biography FROM poets";
$result = $conn->query($sql);

$poets = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $poets[] = $row;
    }
}

// Users adatainak lekérése
$sqlUsers = "SELECT name, weboldal, image FROM users";
$resultUsers = $conn->query($sqlUsers);

$users = [];
if ($resultUsers->num_rows > 0) {
    while ($row = $resultUsers->fetch_assoc()) {
        $users[] = $row;
    }
}

// Sources adatainak lekérése
$sqlSources = "SELECT title, link, icon FROM sources";
$resultSources = $conn->query($sqlSources);

$sources = [];
if ($resultSources->num_rows > 0) {
    while ($row = $resultSources->fetch_assoc()) {
        $sources[] = $row;
    }
}

// Programs adatainak lekérése
$sqlPrograms = "SELECT title, link, icon FROM programs";
$resultPrograms = $conn->query($sqlPrograms);

$programs = [];
if ($resultPrograms->num_rows > 0) {
    while ($row = $resultPrograms->fetch_assoc()) {
        $programs[] = $row;
    }
}

// Timeline adatainak lekérése
$sqlTimeline = "SELECT date, title, description FROM timeline_events ORDER BY date ASC";
$resultTimeline = $conn->query($sqlTimeline);

$timeline = [];
if ($resultTimeline->num_rows > 0) {
    while ($row = $resultTimeline->fetch_assoc()) {
        $timeline[] = $row;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projekt Weblap</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="navbar">
    <nav>
        <!-- Hamburger Menü Ikon -->
        <div class="hamburger" id="hamburger">
            <i class="fas fa-bars"></i>
        </div>
        <ul id="navList">
            <li><a href="#home" onclick="showSection('landing')" id="landingnav">Kezdőlap</a></li>
            <li><a href="#about" onclick="showSection('about')" id="aboutnav">Tagok</a></li>
            <li><a href="#networking" onclick="showSection('networking')" id="networkingnav">Hálózat</a></li>
            <li><a href="#history" onclick="showSection('history')" id="historynav">Töri</a></li>
            <li><a href="#poets" onclick="showSection('poets')" id="poetsnav">Magyar</a></li>
            <li><a href="#timeline" onclick="showSection('timeline')" id="timelinenav">Idővonal</a></li>
            <li><a href="#sources" onclick="showSection('sources')" id="sourcesnav">Források</a></li>
        </ul>
    </nav>
</div>



    <div id="landing" class="section active">
        <h1>Üdvözöllek a weboldalon!</h1>
        <p>Ezt a weboldalt egy iskolai projektünk céljából hoztuk létre, reméljük tetszik.</p>
    </div>

    <div id="about" class="section">
        <h1>Tagok</h1>
        <div class="members-container">
            <?php foreach ($users as $user): ?>
                <div class="member">
                    <img src="<?= htmlspecialchars($user['image']) ?>">
                    <div class="anyaad">
                        <a href="<?= htmlspecialchars($user['weboldal']) ?>" target="_blank"><?= htmlspecialchars($user['name']) ?></a>
                    </div>
                    
                </div>
            <?php endforeach; ?>
        </div>
    </div>


    <div id="networking" class="section">
        <div class="slider-container">
            <button class="prev" onclick="changeSlide(-1)">&#10094;</button>
            <div class="slider">
                <!-- Első kép és letöltés gomb -->
                <div class="slide">
                    <img src="img/halo.png" alt="halo projekt">
                    <a href="sources/halozat.pkt" download="halozat.pkt" class="download-btn"><i class="fa-solid fa-download"></i></a>
                </div>
                <!-- Második kép és letöltés gomb -->
                <div class="slide">
                    <img src="img/fizikai.png" alt="fizikai projekt">
                    <a href="sources/halozat.pkt" download="halozat.pkt" class="download-btn"><i class="fa-solid fa-download"></i></a>
                </div>
                <!-- További képek hozzáadhatóak ide -->
            </div>
            <button class="next" onclick="changeSlide(1)">&#10095;</button>
        </div>
    </div>

    <div id="history" class="section">
        <iframe src="https://docs.google.com/presentation/d/e/2PACX-1vSObWorUybbvvctOp5lXNDAkEladBVG3FvMgY1dJNEtWRVLu-CQp_I00goDznKPmg/embed?start=true&loop=true&delayms=5000" frameborder="0" width="1270" height="750" allowfullscreen="true" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>
        <a href="https://docs.google.com/presentation/d/e/2PACX-1vSObWorUybbvvctOp5lXNDAkEladBVG3FvMgY1dJNEtWRVLu-CQp_I00goDznKPmg/embed?start=true&loop=true&delayms=5000" target="_blank">Prezentáció megnyitása</a>
    </div>

    <div id="poets" class="section">
        <h2>Költők Információi</h2>
        <div class="search-box">
            <input type="text" id="search" class="search-input search" placeholder="Keresés költő névre..." onkeyup="filterTable()">
            <button class="search-button">
                <i class="fas fa-search" onclick="filterTable()"></i>
            </button>
        </div>
        
        <table id="poetsTable">
            <thead>
                <tr>
                    <th>Név</th>
                    <th>Születési idő</th>
                    <th>Halálozási idő</th>
                    <th>Származás</th>
                    <th>Jelentős művek</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($poets as $poet): ?>
                    <tr class="poet-row" onclick="toggleBiography(this)">
                        <td>
                            <span class="arrow">&#9662;</span> <!-- Down arrow icon -->
                            <?= htmlspecialchars($poet['name']) ?>
                        </td>
                        <td><?= htmlspecialchars($poet['birth_date']) ?></td>
                        <td><?= htmlspecialchars($poet['death_date']) ?></td>
                        <td><?= htmlspecialchars($poet['nationality']) ?></td>
                        <td><?= htmlspecialchars($poet['notable_works']) ?></td>
                    </tr>
                    <tr class="biography-row">
                        <td colspan="5"><?= htmlspecialchars($poet['biography']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="timeline" class="section">
        <ul class="timeline">
            <?php foreach ($timeline as $event): ?>
                <li class="timeline-item">
                    <div class="timeline-date"><?= htmlspecialchars($event['date']) ?></div>
                    <div class="timeline-content">
                        <h3><?= htmlspecialchars($event['title']) ?></h3>
                        <p><?= htmlspecialchars($event['description']) ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>


    <div id="sources" class="section">
        <div class="row">

            <div class="item">
                <h1>Források</h1>
                <?php foreach ($sources as $source): ?>
                    <div class="source-item">
                        <div class="source-content">
                            <img src="<?= htmlspecialchars($source['icon']) ?>" alt="<?= htmlspecialchars($source['title']) ?> logo" class="source-icon">
                            <a href="<?= htmlspecialchars($source['link']) ?>" target="_blank"><?= htmlspecialchars($source['title']) ?></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="item">
                <h1>Programok</h1>
                <?php foreach ($programs as $program): ?>
                    <div class="source-item">
                        <div class="source-content">
                            <img src="<?= htmlspecialchars($program['icon']) ?>" alt="<?= htmlspecialchars($program['title']) ?> logo" class="source-icon">
                            <a href="<?= htmlspecialchars($program['link']) ?>" target="_blank"><?= htmlspecialchars($program['title']) ?></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
        
    </div>

    <footer>
        <p>&copy; 2024 Bodor Norbert Bence - Véber-Jurassa Márk</p>
    </footer>

    <script>
        function showSection(sectionId) {
            // Hide all sections and remove the 'active' class
            const sections = document.getElementsByClassName('section');
            for (let i = 0; i < sections.length; i++) {
                sections[i].classList.remove('active');
                sections[i].style.display = 'none';
            }

            // Show the selected section and add 'active' class
            const activeSection = document.getElementById(sectionId);
            activeSection.style.display = 'block';
            setTimeout(() => {
                activeSection.classList.add('active');
            }, 10);

            // Remove the 'anyad' class from all nav items
            const navItems = document.querySelectorAll("nav ul li a");
            navItems.forEach(item => item.classList.remove("anyad"));

            // Add the 'anyad' class to the currently clicked nav item
            const nav = document.getElementById(sectionId + "nav");
            if (nav) {
                nav.classList.add("anyad");
            }
        }

        function filterTable() {
            const input = document.getElementById('search');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('poetsTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[0];
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    tr[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? '' : 'none';
                }
            }
        }

        window.onload = function() {
            if (!location.hash || location.hash === "#home") {
                location.hash = '#home';
                showSection('landing');
            } else {
                const section = location.hash.substring(1);
                showSection(section);
            }
        }

        function toggleBiography(row) {
            // Find the next row, which is the biography row
            const bioRow = row.nextElementSibling;

            // Close any open biography row
            const openBioRows = document.querySelectorAll('.biography-row');
            openBioRows.forEach(openRow => {
                if (openRow !== bioRow) {
                    openRow.style.display = "none";
                    openRow.previousElementSibling.classList.remove("active"); // Remove active class
                }
            });

            // Toggle display of the clicked row's biography
            bioRow.style.display = (bioRow.style.display === "table-row") ? "none" : "table-row";

            // Toggle the active class on the clicked row
            row.classList.toggle("active");
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Hamburger gomb eseménykezelője
            document.getElementById('hamburger').addEventListener('click', function(event) {
                const navList = document.getElementById('navList');
                navList.classList.toggle('show');
                
                // Megakadályozza, hogy az esemény továbbterjedjen (elkerülve, hogy a kattintás kívülre esését is érzékelje)
                event.stopPropagation();
            });

            // Figyelje a kattintásokat az egész dokumentumon, és ha kívül kattintunk, zárja be a menüt
            document.addEventListener('click', function(event) {
                const navList = document.getElementById('navList');
                const hamburger = document.getElementById('hamburger');

                // Ellenőrizzük, hogy nem a hamburger gombra vagy a menü elemeire kattintottak-e
                if (!hamburger.contains(event.target) && !navList.contains(event.target)) {
                    // Ha nem, akkor elrejtjük a menüt
                    navList.classList.remove('show');
                }
            });
        });

        let currentIndex = 0;
        const slides = document.querySelectorAll(".slide");

        function showSlide(index) {
            if (index >= slides.length) {
                currentIndex = 0;
            } else if (index < 0) {
                currentIndex = slides.length - 1;
            } else {
                currentIndex = index;
            }
            // Minden képet eltüntetünk
            slides.forEach(slide => slide.style.display = "none");
            // Az aktuális képet megjelenítjük
            slides[currentIndex].style.display = "block";
        }

        function changeSlide(direction) {
            showSlide(currentIndex + direction);
        }

        // Kezdő lépés megjelenítése
        showSlide(currentIndex);
    </script>
</body>
</html>
