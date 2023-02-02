-- phpMyAdmin SQL Dump
-- version 4.6.0deb1.vivid~ppa.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 25. Mai 2016 um 11:19
-- Server-Version: 5.6.28-0ubuntu0.15.04.1
-- PHP-Version: 5.6.4-4ubuntu6.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `ba`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pdf`
--

CREATE TABLE `pdf` (
  `id` int(10) NOT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `raw` longtext COLLATE utf8_unicode_ci,
  `abstract` text COLLATE utf8_unicode_ci,
  `hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pdf_authors`
--

CREATE TABLE `pdf_authors` (
  `id` int(10) NOT NULL,
  `pdf_id` int(10) NOT NULL,
  `author` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `institution` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pdf_bib`
--

CREATE TABLE `pdf_bib` (
  `id` int(10) NOT NULL,
  `pdf_id` int(10) NOT NULL,
  `raw` text COLLATE utf8_unicode_ci,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `doi` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `author` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `journal` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `publisher` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `year` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pages` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `booktitle` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `abstract` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pdf_location`
--

CREATE TABLE `pdf_location` (
  `id` int(11) NOT NULL,
  `pdf_id` int(11) NOT NULL,
  `lat` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `lng` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pdf_ref`
--

CREATE TABLE `pdf_ref` (
  `id` int(11) NOT NULL,
  `pdf_id` int(10) NOT NULL,
  `reference` text COLLATE utf8_unicode_ci NOT NULL,
  `authors` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titles` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `year` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pdf_words`
--

CREATE TABLE `pdf_words` (
  `id` int(11) NOT NULL,
  `pdf_id` int(11) NOT NULL,
  `word` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `count` int(5) NOT NULL,
  `type` enum('F','A') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `universities`
--

CREATE TABLE `universities` (
  `id` int(10) NOT NULL,
  `country_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `domain` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `pdf`
--
ALTER TABLE `pdf`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `pdf` ADD FULLTEXT KEY `raw` (`raw`);
ALTER TABLE `pdf` ADD FULLTEXT KEY `abstarct` (`abstract`);

--
-- Indizes für die Tabelle `pdf_authors`
--
ALTER TABLE `pdf_authors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pdf_id` (`pdf_id`);

--
-- Indizes für die Tabelle `pdf_bib`
--
ALTER TABLE `pdf_bib`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pdf_id` (`pdf_id`);

--
-- Indizes für die Tabelle `pdf_location`
--
ALTER TABLE `pdf_location`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pdf_id` (`pdf_id`);

--
-- Indizes für die Tabelle `pdf_ref`
--
ALTER TABLE `pdf_ref`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pdf_id` (`pdf_id`);

--
-- Indizes für die Tabelle `pdf_words`
--
ALTER TABLE `pdf_words`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pdf_id` (`pdf_id`);

--
-- Indizes für die Tabelle `universities`
--
ALTER TABLE `universities`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `pdf`
--
ALTER TABLE `pdf`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;
--
-- AUTO_INCREMENT für Tabelle `pdf_authors`
--
ALTER TABLE `pdf_authors`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=573;
--
-- AUTO_INCREMENT für Tabelle `pdf_bib`
--
ALTER TABLE `pdf_bib`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `pdf_location`
--
ALTER TABLE `pdf_location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=389;
--
-- AUTO_INCREMENT für Tabelle `pdf_ref`
--
ALTER TABLE `pdf_ref`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5441;
--
-- AUTO_INCREMENT für Tabelle `pdf_words`
--
ALTER TABLE `pdf_words`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=457104;
--
-- AUTO_INCREMENT für Tabelle `universities`
--
ALTER TABLE `universities`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9364;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `pdf_authors`
--
ALTER TABLE `pdf_authors`
  ADD CONSTRAINT `pdf_authors_ibfk_1` FOREIGN KEY (`pdf_id`) REFERENCES `pdf` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `pdf_bib`
--
ALTER TABLE `pdf_bib`
  ADD CONSTRAINT `pdf_bib_ibfk_1` FOREIGN KEY (`pdf_id`) REFERENCES `pdf` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `pdf_location`
--
ALTER TABLE `pdf_location`
  ADD CONSTRAINT `pdf_location_ibfk_1` FOREIGN KEY (`pdf_id`) REFERENCES `pdf` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `pdf_ref`
--
ALTER TABLE `pdf_ref`
  ADD CONSTRAINT `pdf_ref_ibfk_1` FOREIGN KEY (`pdf_id`) REFERENCES `pdf` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `pdf_words`
--
ALTER TABLE `pdf_words`
  ADD CONSTRAINT `pdf_words_ibfk_1` FOREIGN KEY (`pdf_id`) REFERENCES `pdf` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
