-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Czas generowania: 13 Sie 2021, 19:51
-- Wersja serwera: 5.7.34-log
-- Wersja PHP: 7.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `PHP_Games`
--
CREATE DATABASE IF NOT EXISTS `PHP_Games` DEFAULT CHARACTER SET utf8 COLLATE utf8_polish_ci;
USE `PHP_Games`;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `figura`
--

CREATE TABLE `figura` (
  `id_gry` int(11) NOT NULL,
  `figura_hosta` varchar(60) COLLATE utf8mb4_polish_ci NOT NULL,
  `figura_goscia` varchar(60) COLLATE utf8mb4_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `figura`
--

INSERT INTO `figura` (`id_gry`, `figura_hosta`, `figura_goscia`) VALUES
(1, '<i class=\"fas fa-times\"></i>', '<i class=\"far fa-circle\"></i>'),
(2, '<i class=\"fas fa-times\"></i>', '<i class=\"far fa-circle\"></i>'),
(3, '<i class=\"far fa-circle\"></i>', '<i class=\"fas fa-times\"></i>');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `gra`
--

CREATE TABLE `gra` (
  `id` int(11) NOT NULL,
  `host` varchar(30) COLLATE utf8mb4_polish_ci NOT NULL,
  `gosc` varchar(30) COLLATE utf8mb4_polish_ci DEFAULT NULL,
  `ip_hosta` varchar(32) COLLATE utf8mb4_polish_ci NOT NULL,
  `ip_goscia` varchar(32) COLLATE utf8mb4_polish_ci DEFAULT NULL,
  `tura` tinyint(1) NOT NULL,
  `stan` int(11) DEFAULT NULL,
  `prywatna` tinyint(1) NOT NULL,
  `haslo` varchar(10) COLLATE utf8mb4_polish_ci DEFAULT NULL,
  `czas` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `gra`
--

INSERT INTO `gra` (`id`, `host`, `gosc`, `ip_hosta`, `ip_goscia`, `tura`, `stan`, `prywatna`, `haslo`, `czas`) VALUES
(1, 'Test', NULL, ':1', NULL, 1, 1, 0, NULL, ''),
(2, 'Test2', 'Testowy', '', ':1', 1, 2, 1, '1234', ''),
(3, 'Test3', NULL, ':1', '', 1, 1, 0, NULL, '')


-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `plansza`
--

CREATE TABLE `plansza` (
  `id_gry` int(11) NOT NULL,
  `plansza` varchar(100) DEFAULT NULL,
  `pionek_hosta` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `plansza`
--

INSERT INTO `plansza` (`id_gry`, `plansza`, `pionek_hosta`) VALUES
(2, '-g-g-g-g-gg-g-g-g-g--g-g-g-g-gg-g-g-g-g----------------------h-h-h-h-hh-h-h-h-h--h-h-h-h-hh-h-h-h-h-', 1),
(5, '-g-g-g-g-gg-g-g-g-g--g-g-g-g-gg-g-g-g-g----------------------h-h-h-h-hh-h-h-h-h--h-h-h-h-hh-h-h-h-h-', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rozgrywka`
--

CREATE TABLE `rozgrywka` (
  `id_lobby` int(11) NOT NULL,
  `pole` int(1) NOT NULL DEFAULT '2',
  `pole2` int(1) NOT NULL DEFAULT '2',
  `pole3` int(1) NOT NULL DEFAULT '2',
  `pole4` int(1) NOT NULL DEFAULT '2',
  `pole5` int(1) NOT NULL DEFAULT '2',
  `pole6` int(1) NOT NULL DEFAULT '2',
  `pole7` int(1) NOT NULL DEFAULT '2',
  `pole8` int(1) NOT NULL DEFAULT '2',
  `pole9` int(1) NOT NULL DEFAULT '2'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `rozgrywka`
--

INSERT INTO `rozgrywka` (`id_lobby`, `pole`, `pole2`, `pole3`, `pole4`, `pole5`, `pole6`, `pole7`, `pole8`, `pole9`) VALUES
(1, 1, 2, 0, 0, 2, 2, 2, 2, 2),
(3, 2, 2, 1, 2, 2, 2, 2, 2, 2);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `tictactoe`
--

CREATE TABLE `tictactoe` (
  `id` int(11) NOT NULL,
  `host` varchar(30) COLLATE utf8mb4_polish_ci NOT NULL,
  `gosc` varchar(30) COLLATE utf8mb4_polish_ci DEFAULT NULL,
  `ip_hosta` varchar(32) COLLATE utf8mb4_polish_ci NOT NULL,
  `ip_goscia` varchar(32) COLLATE utf8mb4_polish_ci DEFAULT NULL,
  `tura` tinyint(1) NOT NULL,
  `stan` int(11) DEFAULT NULL,
  `prywatna` tinyint(1) NOT NULL,
  `haslo` varchar(10) COLLATE utf8mb4_polish_ci DEFAULT NULL,
  `czas` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `tictactoe`
--

INSERT INTO `tictactoe` (`id`, `host`, `gosc`, `ip_hosta`, `ip_goscia`, `tura`, `stan`, `prywatna`, `haslo`, `czas`) VALUES
(1, 'Marek', 'Edek', '78.88.146.113', '37.7.11.67', 1, 1, 0, NULL, '2021-02-22 11:33:34'),
(2, 'test', NULL, '78.88.146.113', '176.114.238.102', 0, NULL, 0, NULL, '2021-02-22 08:46:54'),
(3, 'test2', NULL, '78.88.146.113', '77.115.249.95', 1, 1, 1, 'test', '2021-03-31 15:30:11');

-- --------------------------------------------------------

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `figura`
--
ALTER TABLE `figura`
  ADD PRIMARY KEY (`id_gry`);

--
-- Indeksy dla tabeli `gra`
--
ALTER TABLE `gra`
  ADD PRIMARY KEY (`id`);


--
-- Indeksy dla tabeli `plansza`
--
ALTER TABLE `plansza`
  ADD PRIMARY KEY (`id_gry`);


--
-- Indeksy dla tabeli `rozgrywka`
--
ALTER TABLE `rozgrywka`
  ADD PRIMARY KEY (`id_lobby`);

--
-- Indeksy dla tabeli `tictactoe`
--
ALTER TABLE `tictactoe`
  ADD PRIMARY KEY (`id`);



--
-- AUTO_INCREMENT dla tabeli `figura`
--
ALTER TABLE `figura`
  MODIFY `id_gry` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT dla tabeli `gra`
--
ALTER TABLE `gra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;




--
-- AUTO_INCREMENT dla tabeli `plansza`
--
ALTER TABLE `plansza`
  MODIFY `id_gry` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--


--
-- AUTO_INCREMENT dla tabeli `rozgrywka`
--
ALTER TABLE `rozgrywka`
  MODIFY `id_lobby` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT dla tabeli `tictactoe`
--
ALTER TABLE `tictactoe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--

--

--
-- Ograniczenia dla tabeli `figura`
--
ALTER TABLE `figura`
  ADD CONSTRAINT `figura_ibfk_1` FOREIGN KEY (`id_gry`) REFERENCES `tictactoe` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--

--
-- Ograniczenia dla tabeli `plansza`
--
ALTER TABLE `plansza`
  ADD CONSTRAINT `plansza_ibfk_1` FOREIGN KEY (`id_gry`) REFERENCES `gra` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `rozgrywka`
--
ALTER TABLE `rozgrywka`
  ADD CONSTRAINT `rozgrywka_ibfk_1` FOREIGN KEY (`id_lobby`) REFERENCES `tictactoe` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
