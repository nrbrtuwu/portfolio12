-- phpMyAdmin SQL Dump
-- version 5.2.2-1.fc41
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 23, 2025 at 03:51 PM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 8.3.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projektnorbimark`
--

-- --------------------------------------------------------

--
-- Table structure for table `műfajok`
--

CREATE TABLE `műfajok` (
  `mufaj_id` int(11) NOT NULL,
  `mufaj` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `műfajok`
--

INSERT INTO `műfajok` (`mufaj_id`, `mufaj`) VALUES
(1, 'Líra'),
(3, 'Elbeszélés'),
(10, 'Regény'),
(11, 'Szonett'),
(12, 'Szociográfia');

-- --------------------------------------------------------

--
-- Table structure for table `művek`
--

CREATE TABLE `művek` (
  `mu_id` int(11) NOT NULL,
  `sz_id` int(11) DEFAULT NULL,
  `mu_cim` varchar(255) NOT NULL,
  `mufaj_id` int(11) DEFAULT NULL,
  `me_leiras` text DEFAULT NULL,
  `mu_kelt` date NOT NULL,
  `mu_link` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `művek`
--

INSERT INTO `művek` (`mu_id`, `sz_id`, `mu_cim`, `mufaj_id`, `me_leiras`, `mu_kelt`, `mu_link`) VALUES
(9, 2, 'A köpönyeg', 3, 'A köpönyeg egy rövid szatirikus elbeszélés, amely egy jelentéktelen pétervári hivatalnok életét mutatja be, akinek sorsa egy új köpönyeg megszerzése és elvesztése után tragikus fordulatot vesz.\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n', '2025-02-25', 'https://mek.oszk.hu/00300/00397/00397.htm'),
(19, 2, 'Holt lelkek', 10, 'Szatirikus regény, amely az orosz társadalom visszásságait figurázza ki Csicsikov, a főhős kalandjain keresztül.', '1842-05-21', 'https://mek.oszk.hu/00300/00391/html/index.htm'),
(20, 15, 'A gólyakalifa', 10, 'Babits Mihály regénye, amely egy tudathasadásos fiatalember történetét meséli el.', '1916-01-01', 'https://mek.oszk.hu/00600/00600/html/index.htm'),
(21, 15, 'Húsvét előtt', 1, 'Babits Mihály híres verse, amely a háború borzalmait idézi.', '1916-03-26', 'https://epa.oszk.hu/00000/00022/00195/06090.htm'),
(22, 16, 'Édes Anna', 10, 'Kosztolányi Dezső regénye a cselédlány tragikus sorsáról.', '1926-01-01', 'https://mek.oszk.hu/04700/04772/04772.htm'),
(23, 16, 'Esti Kornél', 3, 'Kosztolányi elbeszéléskötete, amely egy titokzatos férfi életét mutatja be.', '1933-01-01', 'https://mek.oszk.hu/00700/00744/00744.htm'),
(24, 17, 'Anna örök', 11, 'Juhász Gyula megható szerelmi szonettje.', '1928-01-01', 'https://mek.oszk.hu/17900/17934/17934.pdf'),
(25, 17, 'Milyen volt...', 1, 'Egy nosztalgikus vers a szerelem emlékéről.', '1930-01-01', 'https://mek.oszk.hu/00700/00709/html/vs191201.htm#01'),
(26, 18, 'Góg és Magóg fia vagyok én...', 1, 'Ady Endre programversének tekinthető költemény.', '1906-01-01', 'https://mek.oszk.hu/00500/00588/html/vers0101.htm'),
(27, 18, 'Párizsban járt az ősz', 1, 'Egy szimbolista vers a múlandóságról.', '1906-10-06', 'https://mek.oszk.hu/05500/05552/html/av0045.html'),
(28, 19, 'Esti sugárkoszorú', 11, 'Tóth Árpád egyik legszebb szerelmes szonettje.', '1923-01-01', 'https://www.arcanum.com/hu/online-kiadvanyok/Verstar-verstar-otven-kolto-osszes-verse-2/toth-arpad-1CABE/versek-1CAC3/esti-sugarkoszoru-1CF62/'),
(29, 19, 'Lélektől lélekig', 1, 'Melankolikus vers az emberi kapcsolatok mélységeiről.', '1928-01-01', 'https://mek.oszk.hu/01100/01112/01112.htm#178'),
(30, 20, 'Puszták népe', 12, 'Illyés Gyula szociográfiai műve a magyar parasztság életéről.', '1936-01-01', 'https://epa.oszk.hu/00000/00022/00605/19136.htm'),
(31, 20, 'Egy mondat a zsarnokságról', 1, 'Egy híres politikai vers a diktatúrák természetéről.', '1950-01-01', 'https://mek.oszk.hu/04300/04340/04340.htm#34');

-- --------------------------------------------------------

--
-- Table structure for table `szerzők`
--

CREATE TABLE `szerzők` (
  `sz_id` int(11) NOT NULL,
  `sz_neve` varchar(255) NOT NULL,
  `sz_szul_ido` date DEFAULT NULL,
  `sz_szul_hely` varchar(255) DEFAULT NULL,
  `sz_halala` date DEFAULT NULL,
  `sz_leiras` text NOT NULL,
  `sz_kep` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `szerzők`
--

INSERT INTO `szerzők` (`sz_id`, `sz_neve`, `sz_szul_ido`, `sz_szul_hely`, `sz_halala`, `sz_leiras`, `sz_kep`) VALUES
(2, 'Nikolai Gogol', '1809-04-01', 'Sorochyntsi', '1852-02-21', 'Nikolai Gogol orosz író volt, szatirikus művek szerzője. Gogol művei groteszkek és társadalmi kritikát tartalmaznak.', 'img/gogol.png'),
(15, 'Babits Mihály', '1883-11-26', 'Szekszárdcigány', '1941-08-04', 'Klasszikus műveltségű, filozofikus költő, a Nyugat vezéralakja.', 'img/babits.jpg'),
(16, 'Kosztolányi Dezső', '1885-03-29', 'Szabadka', '1936-11-03', 'Érzékeny, melankolikus költő, prózaíró, a Nyugat kiemelkedő alakja.', 'img/kosztolanyi.jpg'),
(17, 'Juhász Gyula', '1883-04-04', 'Szeged', '1937-04-06', 'Melankolikus, nosztalgikus lírájú költő, a magyar szonett mestere.', 'img/juhasz.jpg'),
(18, 'Ady Endre', '1877-11-22', 'Érmindszent', '1919-01-27', 'Forradalmi, szimbolista költő, a Nyugat egyik vezető alakja.', 'img/ady.jpg'),
(19, 'Tóth Árpád', '1886-04-14', 'Arad', '1928-11-07', 'Finom lírájú, melankolikus költő, a Nyugat második nemzedékének tagja.', 'img/toth.jpg'),
(20, 'Illyés Gyula', '1902-11-02', 'Felsőrácegrespuszta', '1983-04-15', 'Népi író, költő, politikai és társadalmi kérdésekre reflektált.', 'img/illyes.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`) VALUES
(3, 'admin', '$2y$10$4JxssaYTpN9XG74CsBBza.mA1Nd/gSXGbKpg4XlrnjkRF6yO6TETC');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `műfajok`
--
ALTER TABLE `műfajok`
  ADD PRIMARY KEY (`mufaj_id`);

--
-- Indexes for table `művek`
--
ALTER TABLE `művek`
  ADD PRIMARY KEY (`mu_id`),
  ADD KEY `idx_sz_id` (`sz_id`),
  ADD KEY `idx_mufaj_id` (`mufaj_id`);

--
-- Indexes for table `szerzők`
--
ALTER TABLE `szerzők`
  ADD PRIMARY KEY (`sz_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `műfajok`
--
ALTER TABLE `műfajok`
  MODIFY `mufaj_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `művek`
--
ALTER TABLE `művek`
  MODIFY `mu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `szerzők`
--
ALTER TABLE `szerzők`
  MODIFY `sz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `művek`
--
ALTER TABLE `művek`
  ADD CONSTRAINT `művek_ibfk_1` FOREIGN KEY (`sz_id`) REFERENCES `szerzők` (`sz_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `művek_ibfk_2` FOREIGN KEY (`mufaj_id`) REFERENCES `műfajok` (`mufaj_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
