-- phpMyAdmin SQL Dump
-- version 4.9.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Czas generowania: 02 Cze 2020, 12:46
-- Wersja serwera: 10.1.44-MariaDB-cll-lve
-- Wersja PHP: 7.2.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `pijpiwo_degustator`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `error_logs`
--

CREATE TABLE `error_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `error` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `styles_logs`
--

CREATE TABLE `styles_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `recommended_ids` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `unsuitable_ids` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ip_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `untappd`
--

CREATE TABLE `untappd` (
  `id` int(11) NOT NULL,
  `beer_id` int(11) DEFAULT NULL,
  `beer_name` varchar(100) COLLATE utf16_unicode_ci NULL,
  `brewery_name` varchar(100) COLLATE utf16_unicode_ci NULL,
  `beer_abv` float DEFAULT NULL,
  `beer_ibu` int(3) DEFAULT NULL,
  `beer_description` text COLLATE utf16_unicode_ci,
  `beer_style` varchar(100) COLLATE utf16_unicode_ci DEFAULT NULL,
  `checkin_count` int(100) DEFAULT NULL,
  `in_production` int(1) DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  `next_update` datetime NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_unicode_ci;

ALTER TABLE untappd
    ADD CONSTRAINT brewery_and_beer_name UNIQUE(brewery_name, beer_name);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_answers`
--

CREATE TABLE `user_answers` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `e_mail` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `newsletter` tinyint(4) NOT NULL,
  `answers` text COLLATE utf8_unicode_ci NOT NULL,
  `results` text COLLATE utf8_unicode_ci NOT NULL,
  `results_hash` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `error_logs`
--
ALTER TABLE `error_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `styles_logs`
--
ALTER TABLE `styles_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `untappd`
--
ALTER TABLE `untappd`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `user_answers`
--
ALTER TABLE `user_answers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla tabel zrzutów
--

--
-- AUTO_INCREMENT dla tabeli `error_logs`
--
ALTER TABLE `error_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `styles_logs`
--
ALTER TABLE `styles_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `untappd`
--
ALTER TABLE `untappd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `user_answers`
--
ALTER TABLE `user_answers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
