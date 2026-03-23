-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2024 at 05:38 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `koltok_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `poets`
--

CREATE TABLE `poets` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `death_date` date DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `notable_works` text DEFAULT NULL,
  `biography` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poets`
--

INSERT INTO `poets` (`id`, `name`, `birth_date`, `death_date`, `nationality`, `notable_works`, `biography`) VALUES
(1, 'Petőfi Sándor', '1823-01-01', '1849-07-31', 'Magyar', 'Nemzeti dal, János vitéz', 'Petőfi Sándor (1823–1849) a magyar irodalom egyik legnagyobb alakja, aki a romantika korszakában írta legfontosabb műveit. Legismertebb költői öröksége a szabadság, a szerelem és a nemzeti függetlenség iránti szenvedélyes elköteleződéséből fakad. Petőfi versei, mint a *Nemzeti dal* és a *Tavaszi Szél Vizet Áraszt*, a magyar forradalom szellemiségét és a nép szabadságvágyát hirdették. Rövid élete, amely mindössze 26 évet ölelt fel, tragikus módon a segesvári csatában ért véget, ahol a szabadságharc egyik hőseként tűnt el. Művei és eszméi mindmáig élénken hatnak a magyar kultúrában és történelemben.'),
(2, 'Arany János', '1817-03-02', '1882-10-22', 'Magyar', 'Toldi, Buda halála', 'Arany János (1817–1882) a magyar irodalom egyik legnagyobb költője és epikus mestere, aki a romantika és a realizmus határán alkotott. Legismertebb műve, a *Toldi* című eposz, a magyar nép hősi eszményeit és a nemzeti múlt dicsőségét örökíti meg. Arany költészete mélyen humanista és filozofikus, gyakran foglalkozott a sors kérdéseivel, a közösség és egyén viszonyával. Emellett balladái, mint a *Szép Ilonka* vagy a *A walesi bárdok*, az emberi lélek mélységeit és a tragikumot ábrázolják páratlan erővel. A magyar irodalom klasszikusaként Arany János munkássága a nemzeti identitás fontos részévé vált, és művei máig meghatározóak a magyar kultúrában.'),
(3, 'Ady Endre', '1877-11-22', '1919-01-27', 'Román', 'Új versek, A magyar Ugaron', 'Ady Endre 1877. november 22-én született a bihari Érmindszenten, egy középiskolai tanár családjában. Fiatal korában jogi tanulmányokat folytatott, de hamarosan a költészet vonzotta, és 1903-ban Pestre költözött, ahol a Nyugat című folyóirat köréhez csatlakozott. Ady lírája radikálisan újító volt, a modern magyar irodalom egyik legfontosabb alakjává vált, műveiben a szerelem, a halál, a társadalom kritikája és a nemzeti kérdések kerültek előtérbe. Élete során súlyos betegségekkel küzdött, és a politikai és szellemi válságok mély hatással voltak költészetére. 1919. január 27-én, mindössze 41 évesen halt meg, de öröksége máig meghatározza a magyar irodalmat.'),
(4, 'Babits Mihály', '1883-11-26', '1941-08-04', 'Magyar', 'Versek, Tartarus', 'Babits Mihály 1883. november 26-án született Szekszárdon, és már fiatalon a magyar irodalom egyik legnagyobb alakjává vált. Az egyetemen filozófiát és latin nyelvet tanult, majd a Nyugat című folyóirat köréhez csatlakozott, amely a magyar modernizmus központjává vált. Költészete a filozófia, a vallás és a létezés mély kérdéseit boncolgatta, míg prózai és esszéi a művészet, az irodalom és a társadalom szoros összefonódását vizsgálták. Babits 1927-től a magyar irodalomtörténet egyik legnagyobb hatású műfordítója is volt, különösen a klasszikusokat és a modern angolszász irodalmat ültette át magyarra. 1941. augusztus 4-én, 57 éves korában hunyt el, de öröksége a magyar irodalomban mindmáig meghatározó.'),
(5, 'Kosztolányi Dezső', '1885-03-29', '1936-11-03', 'Szerb', 'Névtelen virágok, Bájolás', 'Kosztolányi Dezső 1885. március 29-én született Szabadkán, és már fiatalon kifejezte irodalmi érdeklődését. Pályafutása során a Nyugat című folyóirat munkatársaként vált ismertté, de számos más irodalmi folyóiratban is publikált. Kosztolányi költészete a magyar modernizmus egyik legjelentősebb ágát képviselte, és írásaiban az emberi létezés kérdéseit, a társadalmi problémákat és az egyéni drámákat is elemzett. Regényei és novellái mellett fontos szerepet játszott a műfordításban is, többek között Thomas Mann és Rainer Maria Rilke műveit ültette át magyarra. 1936. november 3-án hunyt el Budapesten, de életműve mindmáig alapvető része a magyar irodalomnak.');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `link` text NOT NULL,
  `icon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `title`, `link`, `icon`) VALUES
(1, 'Vscode', 'https://code.visualstudio.com/', 'icons/vsc.png'),
(2, 'Discord', 'https://discord.com/', 'icons/discord.png'),
(3, 'WireGuard', 'https://www.wireguard.com/', 'icons/wireguard.png'),
(4, 'FileBrowser', 'https://filebrowser.org/', 'icons/fb.png');

-- --------------------------------------------------------

--
-- Table structure for table `sources`
--

CREATE TABLE `sources` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `link` text NOT NULL,
  `icon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sources`
--

INSERT INTO `sources` (`id`, `title`, `link`, `icon`) VALUES
(1, 'ChatGPT', 'https://chatgpt.com/', 'icons/chatgpt.png'),
(2, 'Google', 'https://google.hu', 'icons/google.png'),
(3, 'Netacad', 'https://www.netacad.com/', 'icons/netacad.png');

-- --------------------------------------------------------

--
-- Table structure for table `timeline_events`
--

CREATE TABLE `timeline_events` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timeline_events`
--

INSERT INTO `timeline_events` (`id`, `date`, `title`, `description`) VALUES
(11, '2024-11-10', 'Adatbázis', 'Elkezdtük a PHP alapú weblapot fejleszteni.'),
(12, '2024-11-08', 'Történelem', 'Elkezdtük és be is fejeztük a prezentációt.'),
(13, '2024-12-07', 'Adatbázis', 'Befejeztük a PHP alapú weblap fejlesztését.'),
(14, '2024-11-20', 'Magyar', 'Létrehoztuk a magyar projekthez a költők adatbázisát.'),
(15, '2024-12-01', 'Hálózat', 'Lemodelleztük a kért hálózatot világháborús tematikával.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `weboldal` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `weboldal`, `image`) VALUES
(1, 'Bodor Norbert', 'https://nrbrt.hu', 'img/nrbrt.png'),
(2, 'Véber-Jurassa Márk', 'https://markveber.hu', 'img/markveber.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `poets`
--
ALTER TABLE `poets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sources`
--
ALTER TABLE `sources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timeline_events`
--
ALTER TABLE `timeline_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `poets`
--
ALTER TABLE `poets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sources`
--
ALTER TABLE `sources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `timeline_events`
--
ALTER TABLE `timeline_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
