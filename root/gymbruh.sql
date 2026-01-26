-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Gen 26, 2026 alle 09:55
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gymbruh`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `commento`
--

CREATE TABLE `commento` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `testo` text NOT NULL,
  `data_commento` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dump dei dati per la tabella `commento`
--

INSERT INTO `commento` (`id`, `post_id`, `utente_id`, `testo`, `data_commento`) VALUES
(1, 1, 2, 'Grande Marco!', '2025-12-10 09:24:02'),
(2, 1, 3, 'Complimenti!', '2025-12-10 09:24:02'),
(3, 2, 1, 'Bravo! Continua così!', '2025-12-10 09:24:02'),
(4, 9, 11, 'Grandissimo!', '2026-01-26 09:45:56'),
(5, 9, 11, 'vero', '2026-01-26 09:47:58');

-- --------------------------------------------------------

--
-- Struttura della tabella `follower`
--

CREATE TABLE `follower` (
  `follower_id` int(11) NOT NULL,
  `following_id` int(11) NOT NULL,
  `data_follow` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dump dei dati per la tabella `follower`
--

INSERT INTO `follower` (`follower_id`, `following_id`, `data_follow`) VALUES
(1, 2, '2025-12-10 09:24:37'),
(1, 3, '2025-12-10 09:24:37'),
(2, 3, '2025-12-10 09:24:37');

-- --------------------------------------------------------

--
-- Struttura della tabella `like_post`
--

CREATE TABLE `like_post` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `data_like` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dump dei dati per la tabella `like_post`
--

INSERT INTO `like_post` (`id`, `post_id`, `utente_id`, `data_like`) VALUES
(1, 1, 2, '2025-12-10 09:23:46'),
(2, 1, 3, '2025-12-10 09:23:46'),
(3, 2, 1, '2025-12-10 09:23:46'),
(6, 9, 10, '2026-01-26 09:44:23'),
(8, 9, 11, '2026-01-26 09:55:08'),
(9, 8, 11, '2026-01-26 09:55:10'),
(10, 6, 11, '2026-01-26 09:55:11'),
(11, 5, 11, '2026-01-26 09:55:14'),
(12, 4, 11, '2026-01-26 09:55:16'),
(13, 1, 11, '2026-01-26 09:55:21');

-- --------------------------------------------------------

--
-- Struttura della tabella `list_gym_workout`
--

CREATE TABLE `list_gym_workout` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `muscles` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dump dei dati per la tabella `list_gym_workout`
--

INSERT INTO `list_gym_workout` (`id`, `name`, `muscles`) VALUES
(1, 'Panca piana', 'petto,tricipiti'),
(2, 'Panca inclinata', 'petto,tricipiti,spalle'),
(3, 'Panca declinata', 'petto,tricipiti'),
(4, 'Chest fly', 'petto'),
(5, 'Croci ai cavi', 'petto'),
(6, 'Push-up', 'petto,tricipiti,spalle'),
(7, 'Lat machine', 'schiena,bicipiti'),
(8, 'Pull-up', 'schiena,bicipiti'),
(9, 'Rematore con bilanciere', 'schiena,bicipiti'),
(10, 'Rematore con manubrio', 'schiena,bicipiti'),
(11, 'Pulley basso', 'schiena,bicipiti'),
(12, 'Trazioni alla sbarra', 'schiena,bicipiti'),
(13, 'Squat', 'gambe,glutei'),
(14, 'Affondi', 'gambe,glutei'),
(15, 'Leg press', 'gambe,glutei'),
(16, 'Stacco da terra', 'schiena,glutei,gambe'),
(17, 'Leg curl', 'femorali'),
(18, 'Leg extension', 'quadricipiti'),
(19, 'Calf raise', 'polpacci'),
(20, 'Shoulder press', 'spalle,tricipiti'),
(21, 'Alzate laterali', 'spalle'),
(22, 'Alzate frontali', 'spalle'),
(23, 'Arnold press', 'spalle,tricipiti'),
(24, 'Dips', 'petto,tricipiti,spalle'),
(25, 'Bicipiti con bilanciere', 'bicipiti'),
(26, 'Bicipiti con manubri', 'bicipiti'),
(27, 'Hammer curl', 'bicipiti,avambracci'),
(28, 'Tricipiti al cavo', 'tricipiti'),
(29, 'French press', 'tricipiti'),
(30, 'Tricipiti con manubrio sopra la testa', 'tricipiti'),
(31, 'Crunch', 'addome'),
(32, 'Plank', 'addome,core'),
(33, 'Leg raise', 'addome,anca'),
(34, 'Russian twist', 'addome,obliqui'),
(35, 'Sit-up', 'addome'),
(36, 'Mountain climber', 'addome,core'),
(37, 'Burpees', 'petto,braccia,addome,gambe'),
(38, 'Cable crunch', 'addome'),
(39, 'Hip thrust', 'glutei,addome'),
(40, 'Glute bridge', 'glutei,addome'),
(41, 'Good morning', 'schiena,femorali,glutei'),
(42, 'Face pull', 'spalle,schiena'),
(43, 'Shrug con bilanciere', 'trapezio'),
(44, 'Shrug con manubri', 'trapezio'),
(45, 'Pull-over', 'petto,schiena');

-- --------------------------------------------------------

--
-- Struttura della tabella `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `contenuto` text DEFAULT NULL,
  `workout_id` int(11) NOT NULL,
  `data_pubblicazione` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dump dei dati per la tabella `post`
--

INSERT INTO `post` (`id`, `utente_id`, `contenuto`, `workout_id`, `data_pubblicazione`) VALUES
(1, 1, 'Oggi palestra top!', 1, '2025-12-10 09:23:23'),
(2, 2, '5 km di corsa in strada!', 2, '2025-12-10 09:23:23'),
(3, 3, '20 vasche a crawl!', 3, '2025-12-10 09:23:23'),
(4, 9, 'sddsadsada', 4, '2026-01-21 09:45:43'),
(5, 9, 'd', 5, '2026-01-21 09:53:37'),
(6, 10, 'BENE!', 6, '2026-01-26 09:06:40'),
(8, 10, 'Bellissima nuotata alle piscine di giussano', 9, '2026-01-26 09:26:53'),
(9, 10, 'Corsa alla porada, stavo morendo\r\n', 10, '2026-01-26 09:29:15');

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(50) NOT NULL DEFAULT 'avatar1.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `bio`, `is_admin`, `created_at`, `avatar`) VALUES
(1, 'marco', 'hash1', 'Amo correre e nuotare', 0, '2025-12-10 08:16:27', 'avatar1.png'),
(2, 'lucia', 'hash2', 'Palestra ogni giorno!', 0, '2025-12-10 08:16:27', 'avatar1.png'),
(3, 'giulia', 'hash3', 'Nuoto e cardio', 0, '2025-12-10 08:16:27', 'avatar1.png'),
(4, 'admin', 'hash4', 'Amministratore', 1, '2025-12-10 08:16:27', 'avatar1.png'),
(5, 'caruso_edoardo', '$2y$10$iiDSPd.nifL2S/TIVaLFWu1lOVoMIzhMXCnw9YbyI0zzX4BIbUDQG', 'what the hell ooo m gawd', 0, '2026-01-07 07:24:25', 'avatar1.png'),
(6, 'test12', '$2y$10$aWoWqc64uGSfdppG6cStwuOSX0Awa683Zp7CQPLzjiSJ0Fv9OwxoC', 'Palestra dd', 0, '2026-01-12 08:55:08', 'avatar1.png'),
(7, 'nuovo_utentetest', '$2y$10$eJMXO/nuDCP4gltHUrymMOMVvecjD3hnTa2nnIdAwtnemKA/n49Ba', 'Test dadadada', 0, '2026-01-21 07:31:00', 'avatar1.png'),
(8, 'testab', '$2y$10$XTh0gMnPNiSPzhh2QIj9SuUgaEuvH5CtZVzOh49Dr9gulMkEs476y', '', 0, '2026-01-21 07:49:28', 'avatar1.png'),
(9, 'test123', '$2y$10$XuFNrPv/4CibFVjcJMVaje51evmkDUfOjfyFYyVMYBbk7nqP/fJGK', 'ddddd', 0, '2026-01-21 08:00:46', 'avatar1.png'),
(10, 'colaninnoricky', '$2y$10$VsE2d4OUKkShFOf32FyiUOH6Wyie/0vYISMc/tv1Cq45lg/Fwg4zi', '', 0, '2026-01-26 08:06:08', 'avatar1.png'),
(11, 'nuovotest', '$2y$10$Tm8B/3v4LV4TZnrPihMde.wVZt8zYK21teVqxvVDq52hHhgNaY74u', 'password: nuovotest', 0, '2026-01-26 08:45:47', 'avatar1.png');

-- --------------------------------------------------------

--
-- Struttura della tabella `workouts`
--

CREATE TABLE `workouts` (
  `id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `tipo` enum('palestra','nuoto','corsa') NOT NULL,
  `data` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dump dei dati per la tabella `workouts`
--

INSERT INTO `workouts` (`id`, `utente_id`, `tipo`, `data`) VALUES
(1, 1, 'palestra', '2025-12-10 00:00:00'),
(2, 2, 'corsa', '2025-12-10 00:00:00'),
(3, 3, 'nuoto', '2025-12-10 00:00:00'),
(4, 9, 'palestra', '2026-01-21 09:45:43'),
(5, 9, 'palestra', '2026-01-21 09:53:37'),
(6, 10, 'palestra', '2026-01-26 09:06:40'),
(8, 10, 'nuoto', '2026-01-26 09:25:48'),
(9, 10, 'nuoto', '2026-01-26 09:26:53'),
(10, 10, 'corsa', '2026-01-26 09:29:15');

-- --------------------------------------------------------

--
-- Struttura della tabella `workout_corsa`
--

CREATE TABLE `workout_corsa` (
  `workout_id` int(11) NOT NULL,
  `distanza_km` decimal(5,2) NOT NULL,
  `tempo_secondi` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dump dei dati per la tabella `workout_corsa`
--

INSERT INTO `workout_corsa` (`workout_id`, `distanza_km`, `tempo_secondi`) VALUES
(2, 5.00, 1500),
(10, 10.00, 4740);

-- --------------------------------------------------------

--
-- Struttura della tabella `workout_nuoto`
--

CREATE TABLE `workout_nuoto` (
  `workout_id` int(11) NOT NULL,
  `stile` varchar(50) NOT NULL,
  `distanza_m` int(11) NOT NULL,
  `tempo_secondi` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dump dei dati per la tabella `workout_nuoto`
--

INSERT INTO `workout_nuoto` (`workout_id`, `stile`, `distanza_m`, `tempo_secondi`) VALUES
(3, 'crawl', 100, 60),
(8, 'Array', 0, 0),
(9, 'Delfino', 1000, 160);

-- --------------------------------------------------------

--
-- Struttura della tabella `workout_palestra_esercizi`
--

CREATE TABLE `workout_palestra_esercizi` (
  `id` int(11) NOT NULL,
  `workout_id` int(11) NOT NULL,
  `esercizio_id` int(11) NOT NULL,
  `sets` int(11) NOT NULL,
  `reps` int(11) NOT NULL,
  `peso` float DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dump dei dati per la tabella `workout_palestra_esercizi`
--

INSERT INTO `workout_palestra_esercizi` (`id`, `workout_id`, `esercizio_id`, `sets`, `reps`, `peso`) VALUES
(1, 1, 1, 4, 10, 0),
(2, 1, 2, 3, 12, 0),
(3, 1, 3, 4, 8, 0),
(4, 4, 25, 1, 1, 0),
(5, 4, 4, 1, 1, 0),
(6, 4, 40, 1, 1, 0),
(7, 4, 5, 1, 1, 0),
(8, 5, 19, 1, 1, 0),
(9, 6, 19, 3, 10, 0),
(10, 6, 23, 5, 44, 0);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `commento`
--
ALTER TABLE `commento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `utente_id` (`utente_id`);

--
-- Indici per le tabelle `follower`
--
ALTER TABLE `follower`
  ADD PRIMARY KEY (`follower_id`,`following_id`),
  ADD KEY `following_id` (`following_id`);

--
-- Indici per le tabelle `like_post`
--
ALTER TABLE `like_post`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `post_id` (`post_id`,`utente_id`),
  ADD KEY `utente_id` (`utente_id`);

--
-- Indici per le tabelle `list_gym_workout`
--
ALTER TABLE `list_gym_workout`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utente_id` (`utente_id`),
  ADD KEY `workout_id` (`workout_id`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indici per le tabelle `workouts`
--
ALTER TABLE `workouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utente_id` (`utente_id`);

--
-- Indici per le tabelle `workout_corsa`
--
ALTER TABLE `workout_corsa`
  ADD PRIMARY KEY (`workout_id`);

--
-- Indici per le tabelle `workout_nuoto`
--
ALTER TABLE `workout_nuoto`
  ADD PRIMARY KEY (`workout_id`);

--
-- Indici per le tabelle `workout_palestra_esercizi`
--
ALTER TABLE `workout_palestra_esercizi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workout_id` (`workout_id`),
  ADD KEY `esercizio_id` (`esercizio_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `commento`
--
ALTER TABLE `commento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `like_post`
--
ALTER TABLE `like_post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT per la tabella `list_gym_workout`
--
ALTER TABLE `list_gym_workout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT per la tabella `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT per la tabella `workouts`
--
ALTER TABLE `workouts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT per la tabella `workout_palestra_esercizi`
--
ALTER TABLE `workout_palestra_esercizi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `commento`
--
ALTER TABLE `commento`
  ADD CONSTRAINT `commento_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `commento_ibfk_2` FOREIGN KEY (`utente_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `follower`
--
ALTER TABLE `follower`
  ADD CONSTRAINT `follower_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `follower_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `like_post`
--
ALTER TABLE `like_post`
  ADD CONSTRAINT `like_post_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `like_post_ibfk_2` FOREIGN KEY (`utente_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`utente_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `workouts`
--
ALTER TABLE `workouts`
  ADD CONSTRAINT `workouts_ibfk_1` FOREIGN KEY (`utente_id`) REFERENCES `users` (`id`);

--
-- Limiti per la tabella `workout_corsa`
--
ALTER TABLE `workout_corsa`
  ADD CONSTRAINT `workout_corsa_ibfk_1` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `workout_nuoto`
--
ALTER TABLE `workout_nuoto`
  ADD CONSTRAINT `workout_nuoto_ibfk_1` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `workout_palestra_esercizi`
--
ALTER TABLE `workout_palestra_esercizi`
  ADD CONSTRAINT `workout_palestra_esercizi_ibfk_1` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `workout_palestra_esercizi_ibfk_2` FOREIGN KEY (`esercizio_id`) REFERENCES `list_gym_workout` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
