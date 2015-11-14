-- phpMyAdmin SQL Dump
-- version 4.5.0.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 14, 2015 at 08:00 PM
-- Server version: 10.0.21-MariaDB-log
-- PHP Version: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `manager`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `address_id` int(11) NOT NULL,
  `address_street` varchar(512) NOT NULL,
  `address_number` varchar(32) NOT NULL COMMENT 'storing as text to allow suffixes etc.',
  `address_postalcode` varchar(64) NOT NULL,
  `address_city` varchar(512) NOT NULL,
  `address_province` varchar(512) NOT NULL,
  `address_country` char(2) NOT NULL COMMENT 'ISO 3166-1 alpha2'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `capability`
--

CREATE TABLE `capability` (
  `capability_id` int(11) NOT NULL,
  `capability_name` varchar(16) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `code` int(3) NOT NULL,
  `alpha2` varchar(2) NOT NULL,
  `alpha3` varchar(3) NOT NULL,
  `langCS` varchar(45) NOT NULL,
  `langDE` varchar(45) NOT NULL,
  `langEN` varchar(45) NOT NULL,
  `langES` varchar(45) NOT NULL,
  `langFR` varchar(45) NOT NULL,
  `langIT` varchar(45) NOT NULL,
  `langNL` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='https://github.com/armetiz/SQL-Countries-ISO-3166-1';

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`code`, `alpha2`, `alpha3`, `langCS`, `langDE`, `langEN`, `langES`, `langFR`, `langIT`, `langNL`) VALUES
(4, 'AF', 'AFG', 'Afghanistán', 'Afghanistan', 'Afghanistan', 'Afganistán', 'Afghanistan', 'Afghanistan', 'Afghanistan'),
(8, 'AL', 'ALB', 'Albánie', 'Albanien', 'Albania', 'Albania', 'Albanie', 'Albania', 'Albanië'),
(10, 'AQ', 'ATA', 'Antarctica', 'Antarktis', 'Antarctica', 'Antartida', 'Antarctique', 'Antartide', 'Antarctica'),
(12, 'DZ', 'DZA', 'Alžírsko', 'Algerien', 'Algeria', 'Argelia', 'Algérie', 'Algeria', 'Algerije'),
(16, 'AS', 'ASM', 'Americká Samoa', 'Amerikanisch-Samoa', 'American Samoa', 'Samoa americana', 'Samoa Américaines', 'Samoa Americane', 'Amerikaans Samoa'),
(20, 'AD', 'AND', 'Andorra', 'Andorra', 'Andorra', 'Andorra', 'Andorre', 'Andorra', 'Andorra'),
(24, 'AO', 'AGO', 'Angola', 'Angola', 'Angola', 'Angola', 'Angola', 'Angola', 'Angola'),
(28, 'AG', 'ATG', 'Antigua a Barbuda', 'Antigua und Barbuda', 'Antigua and Barbuda', 'Antigua y Barbuda', 'Antigua-et-Barbuda', 'Antigua e Barbuda', 'Antigua en Barbuda'),
(31, 'AZ', 'AZE', 'Azerbajdžán', 'Aserbaidschan', 'Azerbaijan', 'Azerbaiyán', 'Azerbaïdjan', 'Azerbaijan', 'Azerbeidzjan'),
(32, 'AR', 'ARG', 'Argentina', 'Argentinien', 'Argentina', 'Argentina', 'Argentine', 'Argentina', 'Argentinië'),
(36, 'AU', 'AUS', 'Austrálie', 'Australien', 'Australia', 'Australia', 'Australie', 'Australia', 'Australië'),
(40, 'AT', 'AUT', 'Rakousko', 'Österreich', 'Austria', 'Austria', 'Autriche', 'Austria', 'Oostenrijk'),
(44, 'BS', 'BHS', 'Bahamy', 'Bahamas', 'Bahamas', 'Bahamas', 'Bahamas', 'Bahamas', 'Bahama''s'),
(48, 'BH', 'BHR', 'Bahrajn', 'Bahrain', 'Bahrain', 'Bahrain', 'Bahreïn', 'Bahrain', 'Bahrein'),
(50, 'BD', 'BGD', 'Bangladéš', 'Bangladesch', 'Bangladesh', 'Bangladesh', 'Bangladesh', 'Bangladesh', 'Bangladesh'),
(51, 'AM', 'ARM', 'Arménie', 'Armenien', 'Armenia', 'Armenia', 'Arménie', 'Armenia', 'Armenië'),
(52, 'BB', 'BRB', 'Barbados', 'Barbados', 'Barbados', 'Barbados', 'Barbade', 'Barbados', 'Barbados'),
(56, 'BE', 'BEL', 'Belgie', 'Belgien', 'Belgium', 'Bélgica', 'Belgique', 'Belgio', 'België'),
(60, 'BM', 'BMU', 'Bermuda', 'Bermuda', 'Bermuda', 'Bermuda', 'Bermudes', 'Bermuda', 'Bermuda'),
(64, 'BT', 'BTN', 'Bhután', 'Bhutan', 'Bhutan', 'Bhutan', 'Bhoutan', 'Bhutan', 'Bhutan'),
(68, 'BO', 'BOL', 'Bolívie', 'Bolivien', 'Bolivia', 'Bolivia', 'Bolivie', 'Bolivia', 'Bolivia'),
(70, 'BA', 'BIH', 'Bosna a Hercegovina', 'Bosnien und Herzegowina', 'Bosnia and Herzegovina', 'Bosnia y Herzegovina', 'Bosnie-Herzégovine', 'Bosnia Erzegovina', 'Bosnië-Herzegovina'),
(72, 'BW', 'BWA', 'Botswana', 'Botswana', 'Botswana', 'Botswana', 'Botswana', 'Botswana', 'Botswana'),
(74, 'BV', 'BVT', 'Bouvet Island', 'Bouvetinsel', 'Bouvet Island', 'Isla Bouvet', 'Île Bouvet', 'Isola di Bouvet', 'Bouvet'),
(76, 'BR', 'BRA', 'Brazílie', 'Brasilien', 'Brazil', 'Brasil', 'Brésil', 'Brasile', 'Brazilië'),
(84, 'BZ', 'BLZ', 'Belize', 'Belize', 'Belize', 'Belize', 'Belize', 'Belize', 'Belize'),
(86, 'IO', 'IOT', 'Britské Indickooceánské teritorium', 'Britisches Territorium im Indischen Ozean', 'British Indian Ocean Territory', 'Territorio Oceánico de la India Británica', 'Territoire Britannique de l''Océan Indien', 'Territori Britannici dell''Oceano Indiano', 'British Indian Ocean Territory'),
(90, 'SB', 'SLB', 'Šalamounovy ostrovy', 'Salomonen', 'Solomon Islands', 'Islas Salomón', 'Îles Salomon', 'Isole Solomon', 'Salomonseilanden'),
(92, 'VG', 'VGB', 'Britské Panenské ostrovy', 'Britische Jungferninseln', 'British Virgin Islands', 'Islas Vírgenes Británicas', 'Îles Vierges Britanniques', 'Isole Vergini Britanniche', 'Britse Maagdeneilanden'),
(96, 'BN', 'BRN', 'Brunej', 'Brunei Darussalam', 'Brunei Darussalam', 'Brunei Darussalam', 'Brunéi Darussalam', 'Brunei Darussalam', 'Brunei'),
(100, 'BG', 'BGR', 'Bulharsko', 'Bulgarien', 'Bulgaria', 'Bulgaria', 'Bulgarie', 'Bulgaria', 'Bulgarije'),
(104, 'MM', 'MMR', 'Myanmar', 'Myanmar', 'Myanmar', 'Mianmar', 'Myanmar', 'Myanmar', 'Myanmar'),
(108, 'BI', 'BDI', 'Burundi', 'Burundi', 'Burundi', 'Burundi', 'Burundi', 'Burundi', 'Burundi'),
(112, 'BY', 'BLR', 'Bělorusko', 'Belarus', 'Belarus', 'Belarus', 'Bélarus', 'Bielorussia', 'Wit-Rusland'),
(116, 'KH', 'KHM', 'Kambodža', 'Kambodscha', 'Cambodia', 'Camboya', 'Cambodge', 'Cambogia', 'Cambodja'),
(120, 'CM', 'CMR', 'Kamerun', 'Kamerun', 'Cameroon', 'Camerún', 'Cameroun', 'Camerun', 'Kameroen'),
(124, 'CA', 'CAN', 'Kanada', 'Kanada', 'Canada', 'Canadá', 'Canada', 'Canada', 'Canada'),
(132, 'CV', 'CPV', 'Ostrovy Zeleného mysu', 'Kap Verde', 'Cape Verde', 'Cabo Verde', 'Cap-vert', 'Capo Verde', 'Kaapverdië'),
(136, 'KY', 'CYM', 'Kajmanské ostrovy', 'Kaimaninseln', 'Cayman Islands', 'Islas Caimán', 'Îles Caïmanes', 'Isole Cayman', 'Caymaneilanden'),
(140, 'CF', 'CAF', 'Středoafrická republika', 'Zentralafrikanische Republik', 'Central African', 'República Centroafricana', 'République Centrafricaine', 'Repubblica Centroafricana', 'Centraal-Afrikaanse Republiek'),
(144, 'LK', 'LKA', 'Srí Lanka', 'Sri Lanka', 'Sri Lanka', 'Sri Lanka', 'Sri Lanka', 'Sri Lanka', 'Sri Lanka'),
(148, 'TD', 'TCD', 'Čad', 'Tschad', 'Chad', 'Chad', 'Tchad', 'Ciad', 'Tsjaad'),
(152, 'CL', 'CHL', 'Chile', 'Chile', 'Chile', 'Chile', 'Chili', 'Cile', 'Chili'),
(156, 'CN', 'CHN', 'Čína', 'China', 'China', 'China', 'Chine', 'Cina', 'China'),
(158, 'TW', 'TWN', 'Tchajwan', 'Taiwan', 'Taiwan', 'Taiwán', 'Taïwan', 'Taiwan', 'Taiwan'),
(162, 'CX', 'CXR', 'Christmas Island', 'Weihnachtsinsel', 'Christmas Island', 'Isla Navidad', 'Île Christmas', 'Isola di Natale', 'Christmaseiland'),
(166, 'CC', 'CCK', 'Kokosové ostrovy', 'Kokosinseln', 'Cocos (Keeling) Islands', 'Islas Cocos (Keeling)', 'Îles Cocos (Keeling)', 'Isole Cocos', 'Cocoseilanden'),
(170, 'CO', 'COL', 'Kolumbie', 'Kolumbien', 'Colombia', 'Colombia', 'Colombie', 'Colombia', 'Colombia'),
(174, 'KM', 'COM', 'Komory', 'Komoren', 'Comoros', 'Comoros', 'Comores', 'Comore', 'Comoren'),
(175, 'YT', 'MYT', 'Mayotte', 'Mayotte', 'Mayotte', 'Mayote', 'Mayotte', 'Mayotte', 'Mayotte'),
(178, 'CG', 'COG', 'Konžská republika Kongo', 'Republik Kongo', 'Republic of the Congo', 'Congo', 'République du Congo', 'Repubblica del Congo', 'Republiek Congo'),
(180, 'CD', 'COD', 'Demokratická republika Kongo Kongo', 'Demokratische Republik Kongo', 'The Democratic Republic Of The Congo', 'República Democrática del Congo', 'République Démocratique du Congo', 'Repubblica Democratica del Congo', 'Democratische Republiek Congo'),
(184, 'CK', 'COK', 'Cookovy ostrovy', 'Cookinseln', 'Cook Islands', 'Islas Cook', 'Îles Cook', 'Isole Cook', 'Cookeilanden'),
(188, 'CR', 'CRI', 'Kostarika', 'Costa Rica', 'Costa Rica', 'Costa Rica', 'Costa Rica', 'Costa Rica', 'Costa Rica'),
(191, 'HR', 'HRV', 'Chorvatsko', 'Kroatien', 'Croatia', 'Croacia', 'Croatie', 'Croazia', 'Kroatië'),
(192, 'CU', 'CUB', 'Kuba', 'Kuba', 'Cuba', 'Cuba', 'Cuba', 'Cuba', 'Cuba'),
(196, 'CY', 'CYP', 'Kypr', 'Zypern', 'Cyprus', 'Chipre', 'Chypre', 'Cipro', 'Cyprus'),
(203, 'CZ', 'CZE', 'Česko', 'Tschechische Republik', 'Czech Republic', 'Chequia', 'République Tchèque', 'Repubblica Ceca', 'Tsjechië'),
(204, 'BJ', 'BEN', 'Benin', 'Benin', 'Benin', 'Benin', 'Bénin', 'Benin', 'Benin'),
(208, 'DK', 'DNK', 'Dánsko', 'Dänemark', 'Denmark', 'Dinamarca', 'Danemark', 'Danimarca', 'Denemarken'),
(212, 'DM', 'DMA', 'Dominika', 'Dominica', 'Dominica', 'Dominica', 'Dominique', 'Dominica', 'Dominica'),
(214, 'DO', 'DOM', 'Dominikánská republika', 'Dominikanische Republik', 'Dominican Republic', 'República Dominicana', 'République Dominicaine', 'Repubblica Dominicana', 'Dominicaanse Republiek'),
(218, 'EC', 'ECU', 'Ekvádor', 'Ecuador', 'Ecuador', 'Ecuador', 'Équateur', 'Ecuador', 'Ecuador'),
(222, 'SV', 'SLV', 'Salvador', 'El Salvador', 'El Salvador', 'El Salvador', 'El Salvador', 'El Salvador', 'El Salvador'),
(226, 'GQ', 'GNQ', 'Rovníková Guinea', 'Äquatorialguinea', 'Equatorial Guinea', 'Guinea Ecuatorial', 'Guinée Équatoriale', 'Guinea Equatoriale', 'Equatoriaal Guinea'),
(231, 'ET', 'ETH', 'Etiopie', 'Äthiopien', 'Ethiopia', 'Etiopía', 'Éthiopie', 'Etiopia', 'Ethiopië'),
(232, 'ER', 'ERI', 'Eritrea', 'Eritrea', 'Eritrea', 'Eritrea', 'Érythrée', 'Eritrea', 'Eritrea'),
(233, 'EE', 'EST', 'Estonsko', 'Estland', 'Estonia', 'Estonia', 'Estonie', 'Estonia', 'Estland'),
(234, 'FO', 'FRO', 'Faerské ostrovy', 'Färöer', 'Faroe Islands', 'Islas Faroe', 'Îles Féroé', 'Isole Faroe', 'Faeröer'),
(238, 'FK', 'FLK', 'Falklandské ostrovy', 'Falklandinseln', 'Falkland Islands', 'Islas Malvinas', 'Îles (malvinas) Falkland', 'Isole Falkland', 'Falklandeilanden'),
(239, 'GS', 'SGS', 'Jižní Georgie a Jižní Sandwichovy ostrovy', 'Südgeorgien und die Südlichen Sandwichinseln', 'South Georgia and the South Sandwich Islands', 'Georgia del Sur e Islas Sandwich del Sur', 'Géorgie du Sud et les Îles Sandwich du Sud', 'Sud Georgia e Isole Sandwich', 'Zuid-Georgië en de Zuidelijke Sandwicheilande'),
(242, 'FJ', 'FJI', 'Fidži', 'Fidschi', 'Fiji', 'Fiji', 'Fidji', 'Fiji', 'Fiji'),
(246, 'FI', 'FIN', 'Finsko', 'Finnland', 'Finland', 'Finlandia', 'Finlande', 'Finlandia', 'Finland'),
(248, 'AX', 'ALA', 'Åland Islands', 'Åland-Inseln', 'Åland Islands', 'IslasÅland', 'Îles Åland', 'Åland Islands', 'Åland Islands'),
(250, 'FR', 'FRA', 'Francie', 'Frankreich', 'France', 'Francia', 'France', 'Francia', 'Frankrijk'),
(254, 'GF', 'GUF', 'Francouzská Guayana', 'Französisch-Guayana', 'French Guiana', 'Guinea Francesa', 'Guyane Française', 'Guyana Francese', 'Frans-Guyana'),
(258, 'PF', 'PYF', 'Francouzská Polynésie', 'Französisch-Polynesien', 'French Polynesia', 'Polinesia Francesa', 'Polynésie Française', 'Polinesia Francese', 'Frans-Polynesië'),
(260, 'TF', 'ATF', 'Francouzská jižní teritoria', 'Französische Süd- und Antarktisgebiete', 'French Southern Territories', 'Territorios Sureños de Francia', 'Terres Australes Françaises', 'Territori Francesi del Sud', 'Franse Zuidelijke en Antarctische gebieden'),
(262, 'DJ', 'DJI', 'Džibutsko', 'Dschibuti', 'Djibouti', 'Djibouti', 'Djibouti', 'Gibuti', 'Djibouti'),
(266, 'GA', 'GAB', 'Gabon', 'Gabun', 'Gabon', 'Gabón', 'Gabon', 'Gabon', 'Gabon'),
(268, 'GE', 'GEO', 'Gruzínsko', 'Georgien', 'Georgia', 'Georgia', 'Géorgie', 'Georgia', 'Georgië'),
(270, 'GM', 'GMB', 'Gambie', 'Gambia', 'Gambia', 'Gambia', 'Gambie', 'Gambia', 'Gambia'),
(275, 'PS', 'PSE', 'Palestinská území', 'Palästinensische Autonomiegebiete', 'Occupied Palestinian Territory', 'Palestina', 'Territoire Palestinien Occupé', 'Territori Palestinesi Occupati', 'Palestina'),
(276, 'DE', 'DEU', 'Německo', 'Deutschland', 'Germany', 'Alemania', 'Allemagne', 'Germania', 'Duitsland'),
(288, 'GH', 'GHA', 'Ghana', 'Ghana', 'Ghana', 'Ghana', 'Ghana', 'Ghana', 'Ghana'),
(292, 'GI', 'GIB', 'Gibraltar', 'Gibraltar', 'Gibraltar', 'Gibraltar', 'Gibraltar', 'Gibilterra', 'Gibraltar'),
(296, 'KI', 'KIR', 'Kiribati', 'Kiribati', 'Kiribati', 'Kiribati', 'Kiribati', 'Kiribati', 'Kiribati'),
(300, 'GR', 'GRC', 'Řecko', 'Griechenland', 'Greece', 'Grecia', 'Grèce', 'Grecia', 'Griekenland'),
(304, 'GL', 'GRL', 'Grónsko', 'Grönland', 'Greenland', 'Groenlandia', 'Groenland', 'Groenlandia', 'Groenland'),
(308, 'GD', 'GRD', 'Grenada', 'Grenada', 'Grenada', 'Granada', 'Grenade', 'Grenada', 'Grenada'),
(312, 'GP', 'GLP', 'Guadeloupe', 'Guadeloupe', 'Guadeloupe', 'Guadalupe', 'Guadeloupe', 'Guadalupa', 'Guadeloupe'),
(316, 'GU', 'GUM', 'Guam', 'Guam', 'Guam', 'Guam', 'Guam', 'Guam', 'Guam'),
(320, 'GT', 'GTM', 'Guatemala', 'Guatemala', 'Guatemala', 'Guatemala', 'Guatemala', 'Guatemala', 'Guatemala'),
(324, 'GN', 'GIN', 'Guinea', 'Guinea', 'Guinea', 'Guinea', 'Guinée', 'Guinea', 'Guinee'),
(328, 'GY', 'GUY', 'Guyana', 'Guyana', 'Guyana', 'Guayana', 'Guyana', 'Guyana', 'Guyana'),
(332, 'HT', 'HTI', 'Haiti', 'Haiti', 'Haiti', 'Haití', 'Haïti', 'Haiti', 'Haiti'),
(334, 'HM', 'HMD', 'Heardův ostrov a McDonaldovy ostrovy', 'Heard und McDonaldinseln', 'Heard Island and McDonald Islands', 'Islas Heard e Islas McDonald', 'Îles Heard et Mcdonald', 'Isola Heard e Isole McDonald', 'Heard- en McDonaldeilanden'),
(336, 'VA', 'VAT', 'Vatikán', 'Vatikanstadt', 'Vatican City State', 'Estado Vaticano', 'Saint-Siège (état de la Cité du Vatican)', 'Città del Vaticano', 'Vaticaanstad'),
(340, 'HN', 'HND', 'Honduras', 'Honduras', 'Honduras', 'Honduras', 'Honduras', 'Honduras', 'Honduras'),
(344, 'HK', 'HKG', 'Hong Kong', 'Hongkong', 'Hong Kong', 'Hong Kong', 'Hong-Kong', 'Hong Kong', 'Hongkong'),
(348, 'HU', 'HUN', 'Maďarsko', 'Ungarn', 'Hungary', 'Hungría', 'Hongrie', 'Ungheria', 'Hongarije'),
(352, 'IS', 'ISL', 'Island', 'Island', 'Iceland', 'Islandia', 'Islande', 'Islanda', 'IJsland'),
(356, 'IN', 'IND', 'Indie', 'Indien', 'India', 'India', 'Inde', 'India', 'India'),
(360, 'ID', 'IDN', 'Indonésie', 'Indonesien', 'Indonesia', 'Indonesia', 'Indonésie', 'Indonesia', 'Indonesië'),
(364, 'IR', 'IRN', 'Írán', 'Islamische Republik Iran', 'Islamic Republic of Iran', 'Irán', 'République Islamique d''Iran', 'Iran', 'Iran'),
(368, 'IQ', 'IRQ', 'Irák', 'Irak', 'Iraq', 'Irak', 'Iraq', 'Iraq', 'Irak'),
(372, 'IE', 'IRL', 'Irsko', 'Irland', 'Ireland', 'Irlanda', 'Irlande', 'Eire', 'Ierland'),
(376, 'IL', 'ISR', 'Izrael', 'Israel', 'Israel', 'Israel', 'Israël', 'Israele', 'Israël'),
(380, 'IT', 'ITA', 'Itálie', 'Italien', 'Italy', 'Italia', 'Italie', 'Italia', 'Italië'),
(384, 'CI', 'CIV', 'Pobřeží slonoviny', 'Côte d''Ivoire', 'Côte d''Ivoire', 'Costa de Marfil', 'Côte d''Ivoire', 'Costa d''Avorio', 'Ivoorkust'),
(388, 'JM', 'JAM', 'Jamajka', 'Jamaika', 'Jamaica', 'Jamaica', 'Jamaïque', 'Giamaica', 'Jamaica'),
(392, 'JP', 'JPN', 'Japonsko', 'Japan', 'Japan', 'Japón', 'Japon', 'Giappone', 'Japan'),
(398, 'KZ', 'KAZ', 'Kazachstán', 'Kasachstan', 'Kazakhstan', 'Kazajstán', 'Kazakhstan', 'Kazakhistan', 'Kazachstan'),
(400, 'JO', 'JOR', 'Jordánsko', 'Jordanien', 'Jordan', 'Jordania', 'Jordanie', 'Giordania', 'Jordanië'),
(404, 'KE', 'KEN', 'Keňa', 'Kenia', 'Kenya', 'Kenia', 'Kenya', 'Kenya', 'Kenia'),
(408, 'KP', 'PRK', 'Severní Korea', 'Demokratische Volksrepublik Korea', 'Democratic People''s Republic of Korea', 'Corea', 'République Populaire Démocratique de Corée', 'Corea del Nord', 'Noord-Korea'),
(410, 'KR', 'KOR', 'Jižní Korea', 'Republik Korea', 'Republic of Korea', 'Corea', 'République de Corée', 'Corea del Sud', 'Zuid-Korea'),
(414, 'KW', 'KWT', 'Kuvajt', 'Kuwait', 'Kuwait', 'Kuwait', 'Koweït', 'Kuwait', 'Koeweit'),
(417, 'KG', 'KGZ', 'Kyrgyzstán', 'Kirgisistan', 'Kyrgyzstan', 'Kirgistán', 'Kirghizistan', 'Kirghizistan', 'Kirgizië'),
(418, 'LA', 'LAO', 'Laos', 'Demokratische Volksrepublik Laos', 'Lao People''s Democratic Republic', 'Laos', 'République Démocratique Populaire Lao', 'Laos', 'Laos'),
(422, 'LB', 'LBN', 'Libanon', 'Libanon', 'Lebanon', 'Líbano', 'Liban', 'Libano', 'Libanon'),
(426, 'LS', 'LSO', 'Lesotho', 'Lesotho', 'Lesotho', 'Lesoto', 'Lesotho', 'Lesotho', 'Lesotho'),
(428, 'LV', 'LVA', 'Lotyšsko', 'Lettland', 'Latvia', 'Letonia', 'Lettonie', 'Lettonia', 'Letland'),
(430, 'LR', 'LBR', 'Libérie', 'Liberia', 'Liberia', 'Liberia', 'Libéria', 'Liberia', 'Liberia'),
(434, 'LY', 'LBY', 'Libye', 'Libysch-Arabische Dschamahirija', 'Libyan Arab Jamahiriya', 'Libia', 'Jamahiriya Arabe Libyenne', 'Libia', 'Libië'),
(438, 'LI', 'LIE', 'Lichtenštejnsko', 'Liechtenstein', 'Liechtenstein', 'Liechtenstein', 'Liechtenstein', 'Liechtenstein', 'Liechtenstein'),
(440, 'LT', 'LTU', 'Litva', 'Litauen', 'Lithuania', 'Lituania', 'Lituanie', 'Lituania', 'Litouwen'),
(442, 'LU', 'LUX', 'Lucembursko', 'Luxemburg', 'Luxembourg', 'Luxemburgo', 'Luxembourg', 'Lussemburgo', 'Groothertogdom Luxemburg'),
(446, 'MO', 'MAC', 'Macao', 'Macao', 'Macao', 'Macao', 'Macao', 'Macao', 'Macao'),
(450, 'MG', 'MDG', 'Madagaskar', 'Madagaskar', 'Madagascar', 'Madagascar', 'Madagascar', 'Madagascar', 'Madagaskar'),
(454, 'MW', 'MWI', 'Malawi', 'Malawi', 'Malawi', 'Malawi', 'Malawi', 'Malawi', 'Malawi'),
(458, 'MY', 'MYS', 'Malajsie', 'Malaysia', 'Malaysia', 'Malasia', 'Malaisie', 'Malesia', 'Maleisië'),
(462, 'MV', 'MDV', 'Maledivy', 'Malediven', 'Maldives', 'Maldivas', 'Maldives', 'Maldive', 'Maldiven'),
(466, 'ML', 'MLI', 'Mali', 'Mali', 'Mali', 'Mali', 'Mali', 'Mali', 'Mali'),
(470, 'MT', 'MLT', 'Malta', 'Malta', 'Malta', 'Malta', 'Malte', 'Malta', 'Malta'),
(474, 'MQ', 'MTQ', 'Martinik', 'Martinique', 'Martinique', 'Martinica', 'Martinique', 'Martinica', 'Martinique'),
(478, 'MR', 'MRT', 'Mauretánie', 'Mauretanien', 'Mauritania', 'Mauritania', 'Mauritanie', 'Mauritania', 'Mauritanië'),
(480, 'MU', 'MUS', 'Mauricius', 'Mauritius', 'Mauritius', 'Mauricio', 'Maurice', 'Maurizius', 'Mauritius'),
(484, 'MX', 'MEX', 'Mexiko', 'Mexiko', 'Mexico', 'México', 'Mexique', 'Messico', 'Mexico'),
(492, 'MC', 'MCO', 'Monako', 'Monaco', 'Monaco', 'Mónaco', 'Monaco', 'Monaco', 'Monaco'),
(496, 'MN', 'MNG', 'Mongolsko', 'Mongolei', 'Mongolia', 'Mongolia', 'Mongolie', 'Mongolia', 'Mongolië'),
(498, 'MD', 'MDA', 'Moldavsko', 'Moldawien', 'Republic of Moldova', 'Moldavia', 'République de Moldova', 'Moldavia', 'Republiek Moldavië'),
(500, 'MS', 'MSR', 'Montserrat', 'Montserrat', 'Montserrat', 'Montserrat', 'Montserrat', 'Montserrat', 'Montserrat'),
(504, 'MA', 'MAR', 'Maroko', 'Marokko', 'Morocco', 'Marruecos', 'Maroc', 'Marocco', 'Marokko'),
(508, 'MZ', 'MOZ', 'Mosambik', 'Mosambik', 'Mozambique', 'Mozambique', 'Mozambique', 'Mozambico', 'Mozambique'),
(512, 'OM', 'OMN', 'Omán', 'Oman', 'Oman', 'Omán', 'Oman', 'Oman', 'Oman'),
(516, 'NA', 'NAM', 'Namíbie', 'Namibia', 'Namibia', 'Namibia', 'Namibie', 'Namibia', 'Namibië'),
(520, 'NR', 'NRU', 'Nauru', 'Nauru', 'Nauru', 'Nauru', 'Nauru', 'Nauru', 'Nauru'),
(524, 'NP', 'NPL', 'Nepál', 'Nepal', 'Nepal', 'Nepal', 'Népal', 'Nepal', 'Nepal'),
(528, 'NL', 'NLD', 'Nizozemsko', 'Niederlande', 'Netherlands', 'Holanda', 'Pays-Bas', 'Paesi Bassi', 'Nederland'),
(530, 'AN', 'ANT', 'Nizozemské Antily', 'Niederländische Antillen', 'Netherlands Antilles', 'Antillas Holandesas', 'Antilles Néerlandaises', 'Antille Olandesi', 'Nederlandse Antillen'),
(533, 'AW', 'ABW', 'Aruba', 'Aruba', 'Aruba', 'Aruba', 'Aruba', 'Aruba', 'Aruba'),
(540, 'NC', 'NCL', 'Nová Kaledonie', 'Neukaledonien', 'New Caledonia', 'Nueva Caledonia', 'Nouvelle-Calédonie', 'Nuova Caledonia', 'Nieuw-Caledonië'),
(548, 'VU', 'VUT', 'Vanuatu', 'Vanuatu', 'Vanuatu', 'Vanuatu', 'Vanuatu', 'Vanuatu', 'Vanuatu'),
(554, 'NZ', 'NZL', 'Nový Zéland', 'Neuseeland', 'New Zealand', 'Nueva Zelanda', 'Nouvelle-Zélande', 'Nuova Zelanda', 'Nieuw-Zeeland'),
(558, 'NI', 'NIC', 'Nikaragua', 'Nicaragua', 'Nicaragua', 'Nicaragua', 'Nicaragua', 'Nicaragua', 'Nicaragua'),
(562, 'NE', 'NER', 'Niger', 'Niger', 'Niger', 'Níger', 'Niger', 'Niger', 'Niger'),
(566, 'NG', 'NGA', 'Nigérie', 'Nigeria', 'Nigeria', 'Nigeria', 'Nigéria', 'Nigeria', 'Nigeria'),
(570, 'NU', 'NIU', 'Niue', 'Niue', 'Niue', 'Niue', 'Niué', 'Niue', 'Niue'),
(574, 'NF', 'NFK', 'Norfolk Island', 'Norfolkinsel', 'Norfolk Island', 'Islas Norfolk', 'Île Norfolk', 'Isola Norfolk', 'Norfolkeiland'),
(578, 'NO', 'NOR', 'Norsko', 'Norwegen', 'Norway', 'Noruega', 'Norvège', 'Norvegia', 'Noorwegen'),
(580, 'MP', 'MNP', 'Severomariánské ostrovy', 'Nördliche Marianen', 'Northern Mariana Islands', 'Islas de Norte-Mariana', 'Îles Mariannes du Nord', 'Isole Marianne Settentrionali', 'Noordelijke Marianen'),
(581, 'UM', 'UMI', 'United States Minor Outlying Islands', 'Amerikanisch-Ozeanien', 'United States Minor Outlying Islands', 'Islas Ultramarinas de Estados Unidos', 'Îles Mineures Éloignées des États-Unis', 'Isole Minori degli Stati Uniti d''America', 'United States Minor Outlying Eilanden'),
(583, 'FM', 'FSM', 'Mikronésie', 'Mikronesien', 'Federated States of Micronesia', 'Micronesia', 'États Fédérés de Micronésie', 'Stati Federati della Micronesia', 'Micronesië'),
(584, 'MH', 'MHL', 'Marshallovy ostrovy', 'Marshallinseln', 'Marshall Islands', 'Islas Marshall', 'Îles Marshall', 'Isole Marshall', 'Marshalleilanden'),
(585, 'PW', 'PLW', 'Palau', 'Palau', 'Palau', 'Palau', 'Palaos', 'Palau', 'Palau'),
(586, 'PK', 'PAK', 'Pakistán', 'Pakistan', 'Pakistan', 'Pakistán', 'Pakistan', 'Pakistan', 'Pakistan'),
(591, 'PA', 'PAN', 'Panama', 'Panama', 'Panama', 'Panamá', 'Panama', 'Panamá', 'Panama'),
(598, 'PG', 'PNG', 'Papua Nová Guinea', 'Papua-Neuguinea', 'Papua New Guinea', 'Papúa Nueva Guinea', 'Papouasie-Nouvelle-Guinée', 'Papua Nuova Guinea', 'Papoea-Nieuw-Guinea'),
(600, 'PY', 'PRY', 'Paraguay', 'Paraguay', 'Paraguay', 'Paraguay', 'Paraguay', 'Paraguay', 'Paraguay'),
(604, 'PE', 'PER', 'Peru', 'Peru', 'Peru', 'Perú', 'Pérou', 'Perù', 'Peru'),
(608, 'PH', 'PHL', 'Filipíny', 'Philippinen', 'Philippines', 'Filipinas', 'Philippines', 'Filippine', 'Filippijnen'),
(612, 'PN', 'PCN', 'Pitcairn', 'Pitcairninseln', 'Pitcairn', 'Pitcairn', 'Pitcairn', 'Pitcairn', 'Pitcairneilanden'),
(616, 'PL', 'POL', 'Polsko', 'Polen', 'Poland', 'Polonia', 'Pologne', 'Polonia', 'Polen'),
(620, 'PT', 'PRT', 'Portugalsko', 'Portugal', 'Portugal', 'Portugal', 'Portugal', 'Portogallo', 'Portugal'),
(624, 'GW', 'GNB', 'Guinea-Bissau', 'Guinea-Bissau', 'Guinea-Bissau', 'Guinea-Bissau', 'Guinée-Bissau', 'Guinea-Bissau', 'Guinee-Bissau'),
(626, 'TL', 'TLS', 'Východní Timor', 'Timor-Leste', 'Timor-Leste', 'Timor Leste', 'Timor-Leste', 'Timor Est', 'Oost-Timor'),
(630, 'PR', 'PRI', 'Portoriko', 'Puerto Rico', 'Puerto Rico', 'Puerto Rico', 'Porto Rico', 'Porto Rico', 'Puerto Rico'),
(634, 'QA', 'QAT', 'Katar', 'Katar', 'Qatar', 'Qatar', 'Qatar', 'Qatar', 'Qatar'),
(638, 'RE', 'REU', 'Reunion', 'Réunion', 'Réunion', 'Reunión', 'Réunion', 'Reunion', 'Réunion'),
(642, 'RO', 'ROU', 'Rumunsko', 'Rumänien', 'Romania', 'Rumanía', 'Roumanie', 'Romania', 'Roemenië'),
(643, 'RU', 'RUS', 'Rusko', 'Russische Föderation', 'Russian Federation', 'Rusia', 'Fédération de Russie', 'Federazione Russa', 'Rusland'),
(646, 'RW', 'RWA', 'Rwanda', 'Ruanda', 'Rwanda', 'Ruanda', 'Rwanda', 'Ruanda', 'Rwanda'),
(654, 'SH', 'SHN', 'Svatá Helena', 'St. Helena', 'Saint Helena', 'Santa Helena', 'Sainte-Hélène', 'Sant''Elena', 'Sint-Helena'),
(659, 'KN', 'KNA', 'Svatý Kitts a Nevis', 'St. Kitts und Nevis', 'Saint Kitts and Nevis', 'Santa Kitts y Nevis', 'Saint-Kitts-et-Nevis', 'Saint Kitts e Nevis', 'Saint Kitts en Nevis'),
(660, 'AI', 'AIA', 'Anguilla', 'Anguilla', 'Anguilla', 'Anguilla', 'Anguilla', 'Anguilla', 'Anguilla'),
(662, 'LC', 'LCA', 'Svatá Lucie', 'St. Lucia', 'Saint Lucia', 'Santa Lucía', 'Sainte-Lucie', 'Santa Lucia', 'Saint Lucia'),
(666, 'PM', 'SPM', 'Svatý Pierre a Miquelon', 'St. Pierre und Miquelon', 'Saint-Pierre and Miquelon', 'San Pedro y Miquelon', 'Saint-Pierre-et-Miquelon', 'Saint Pierre e Miquelon', 'Saint-Pierre en Miquelon'),
(670, 'VC', 'VCT', 'Svatý Vincenc a Grenadiny', 'St. Vincent und die Grenadinen', 'Saint Vincent and the Grenadines', 'San Vincente y Las Granadinas', 'Saint-Vincent-et-les Grenadines', 'Saint Vincent e Grenadine', 'Saint Vincent en de Grenadines'),
(674, 'SM', 'SMR', 'San Marino', 'San Marino', 'San Marino', 'San Marino', 'Saint-Marin', 'San Marino', 'San Marino'),
(678, 'ST', 'STP', 'Svatý Tomáš a Princův ostrov', 'São Tomé und Príncipe', 'Sao Tome and Principe', 'Santo Tomé y Príncipe', 'Sao Tomé-et-Principe', 'Sao Tome e Principe', 'Sao Tomé en Principe'),
(682, 'SA', 'SAU', 'Saudská Arábie', 'Saudi-Arabien', 'Saudi Arabia', 'Arabia Saudí', 'Arabie Saoudite', 'Arabia Saudita', 'Saoedi-Arabië'),
(686, 'SN', 'SEN', 'Senegal', 'Senegal', 'Senegal', 'Senegal', 'Sénégal', 'Senegal', 'Senegal'),
(690, 'SC', 'SYC', 'Seychely', 'Seychellen', 'Seychelles', 'Seychelles', 'Seychelles', 'Seychelles', 'Seychellen'),
(694, 'SL', 'SLE', 'Sierra Leone', 'Sierra Leone', 'Sierra Leone', 'Sierra Leona', 'Sierra Leone', 'Sierra Leone', 'Sierra Leone'),
(702, 'SG', 'SGP', 'Singapur', 'Singapur', 'Singapore', 'Singapur', 'Singapour', 'Singapore', 'Singapore'),
(703, 'SK', 'SVK', 'Slovensko', 'Slowakei', 'Slovakia', 'Eslovaquia', 'Slovaquie', 'Slovacchia', 'Slowakije'),
(704, 'VN', 'VNM', 'Vietnam', 'Vietnam', 'Vietnam', 'Vietnam', 'Viet Nam', 'Vietnam', 'Vietnam'),
(705, 'SI', 'SVN', 'Slovinsko', 'Slowenien', 'Slovenia', 'Eslovenia', 'Slovénie', 'Slovenia', 'Slovenië'),
(706, 'SO', 'SOM', 'Somálsko', 'Somalia', 'Somalia', 'Somalia', 'Somalie', 'Somalia', 'Somalië'),
(710, 'ZA', 'ZAF', 'Jižní Afrika', 'Südafrika', 'South Africa', 'Sudáfrica', 'Afrique du Sud', 'Sud Africa', 'Zuid-Afrika'),
(716, 'ZW', 'ZWE', 'Zimbabwe', 'Simbabwe', 'Zimbabwe', 'Zimbabue', 'Zimbabwe', 'Zimbabwe', 'Zimbabwe'),
(724, 'ES', 'ESP', 'Španělsko', 'Spanien', 'Spain', 'España', 'Espagne', 'Spagna', 'Spanje'),
(732, 'EH', 'ESH', 'Západní Sahara', 'Westsahara', 'Western Sahara', 'Sáhara Occidental', 'Sahara Occidental', 'Sahara Occidentale', 'Westelijke Sahara'),
(736, 'SD', 'SDN', 'Súdán', 'Sudan', 'Sudan', 'Sudán', 'Soudan', 'Sudan', 'Sudan'),
(740, 'SR', 'SUR', 'Surinam', 'Suriname', 'Suriname', 'Surinám', 'Suriname', 'Suriname', 'Suriname'),
(744, 'SJ', 'SJM', 'Špicberky a Jan Mayen', 'Svalbard and Jan Mayen', 'Svalbard and Jan Mayen', 'Esvalbard y Jan Mayen', 'Svalbard etÎle Jan Mayen', 'Svalbard e Jan Mayen', 'Svalbard'),
(748, 'SZ', 'SWZ', 'Svazijsko', 'Swasiland', 'Swaziland', 'Suazilandia', 'Swaziland', 'Swaziland', 'Swaziland'),
(752, 'SE', 'SWE', 'Švédsko', 'Schweden', 'Sweden', 'Suecia', 'Suède', 'Svezia', 'Zweden'),
(756, 'CH', 'CHE', 'Švýcarsko', 'Schweiz', 'Switzerland', 'Suiza', 'Suisse', 'Svizzera', 'Zwitserland'),
(760, 'SY', 'SYR', 'Sýrie', 'Arabische Republik Syrien', 'Syrian Arab Republic', 'Siria', 'République Arabe Syrienne', 'Siria', 'Syrië'),
(762, 'TJ', 'TJK', 'Tadžikistán', 'Tadschikistan', 'Tajikistan', 'Tajikistán', 'Tadjikistan', 'Tagikistan', 'Tadzjikistan'),
(764, 'TH', 'THA', 'Thajsko', 'Thailand', 'Thailand', 'Tailandia', 'Thaïlande', 'Tailandia', 'Thailand'),
(768, 'TG', 'TGO', 'Togo', 'Togo', 'Togo', 'Togo', 'Togo', 'Togo', 'Togo'),
(772, 'TK', 'TKL', 'Tokelau', 'Tokelau', 'Tokelau', 'Tokelau', 'Tokelau', 'Tokelau', 'Tokelau -eilanden'),
(776, 'TO', 'TON', 'Tonga', 'Tonga', 'Tonga', 'Tongo', 'Tonga', 'Tonga', 'Tonga'),
(780, 'TT', 'TTO', 'Trinidad a Tobago', 'Trinidad und Tobago', 'Trinidad and Tobago', 'Trinidad y Tobago', 'Trinité-et-Tobago', 'Trinidad e Tobago', 'Trinidad en Tobago'),
(784, 'AE', 'ARE', 'Spojené Arabské Emiráty', 'Vereinigte Arabische Emirate', 'United Arab Emirates', 'EmiratosÁrabes Unidos', 'Émirats Arabes Unis', 'Emirati Arabi Uniti', 'Verenigde Arabische Emiraten'),
(788, 'TN', 'TUN', 'Tunisko', 'Tunesien', 'Tunisia', 'Túnez', 'Tunisie', 'Tunisia', 'Tunesië'),
(792, 'TR', 'TUR', 'Turecko', 'Türkei', 'Turkey', 'Turquía', 'Turquie', 'Turchia', 'Turkije'),
(795, 'TM', 'TKM', 'Turkmenistán', 'Turkmenistan', 'Turkmenistan', 'Turmenistán', 'Turkménistan', 'Turkmenistan', 'Turkmenistan'),
(796, 'TC', 'TCA', 'Turks a ostrovy Caicos', 'Turks- und Caicosinseln', 'Turks and Caicos Islands', 'Islas Turks y Caicos', 'Îles Turks et Caïques', 'Isole Turks e Caicos', 'Turks- en Caicoseilanden'),
(798, 'TV', 'TUV', 'Tuvalu', 'Tuvalu', 'Tuvalu', 'Tuvalu', 'Tuvalu', 'Tuvalu', 'Tuvalu'),
(800, 'UG', 'UGA', 'Uganda', 'Uganda', 'Uganda', 'Uganda', 'Ouganda', 'Uganda', 'Oeganda'),
(804, 'UA', 'UKR', 'Ukrajina', 'Ukraine', 'Ukraine', 'Ucrania', 'Ukraine', 'Ucraina', 'Oekraïne'),
(807, 'MK', 'MKD', 'Makedonie', 'Ehem. jugoslawische Republik Mazedonien', 'The Former Yugoslav Republic of Macedonia', 'Macedonia', 'L''ex-République Yougoslave de Macédoine', 'Macedonia', 'Macedonië'),
(818, 'EG', 'EGY', 'Egypt', 'Ägypten', 'Egypt', 'Egipto', 'Égypte', 'Egitto', 'Egypte'),
(826, 'GB', 'GBR', 'Velká Británie', 'Vereinigtes Königreich von Großbritannien und', 'United Kingdom', 'Reino Unido', 'Royaume-Uni', 'Regno Unito', 'Verenigd Koninkrijk'),
(833, 'IM', 'IMN', 'Ostrov Man', 'Insel Man', 'Isle of Man', 'Isla de Man', 'Île de Man', 'Isola di Man', 'Eiland Man'),
(834, 'TZ', 'TZA', 'Tanzánie', 'Vereinigte Republik Tansania', 'United Republic Of Tanzania', 'Tanzania', 'République-Unie de Tanzanie', 'Tanzania', 'Tanzania'),
(840, 'US', 'USA', 'USA', 'Vereinigte Staaten von Amerika', 'United States', 'Estados Unidos', 'États-Unis', 'Stati Uniti d''America', 'Verenigde Staten'),
(850, 'VI', 'VIR', 'Americké Panenské ostrovy', 'Amerikanische Jungferninseln', 'U.S. Virgin Islands', 'Islas Vírgenes Estadounidenses', 'Îles Vierges des États-Unis', 'Isole Vergini Americane', 'Amerikaanse Maagdeneilanden'),
(854, 'BF', 'BFA', 'Burkina Faso', 'Burkina Faso', 'Burkina Faso', 'Burkina Faso', 'Burkina Faso', 'Burkina Faso', 'Burkina Faso'),
(858, 'UY', 'URY', 'Uruguay', 'Uruguay', 'Uruguay', 'Uruguay', 'Uruguay', 'Uruguay', 'Uruguay'),
(860, 'UZ', 'UZB', 'Uzbekistán', 'Usbekistan', 'Uzbekistan', 'Uzbekistán', 'Ouzbékistan', 'Uzbekistan', 'Oezbekistan'),
(862, 'VE', 'VEN', 'Venezuela', 'Venezuela', 'Venezuela', 'Venezuela', 'Venezuela', 'Venezuela', 'Venezuela'),
(876, 'WF', 'WLF', 'Wallis a Futuna', 'Wallis und Futuna', 'Wallis and Futuna', 'Wallis y Futuna', 'Wallis et Futuna', 'Wallis e Futuna', 'Wallis en Futuna'),
(882, 'WS', 'WSM', 'Samoa', 'Samoa', 'Samoa', 'Samoa', 'Samoa', 'Samoa', 'Samoa'),
(887, 'YE', 'YEM', 'Jemen', 'Jemen', 'Yemen', 'Yemen', 'Yémen', 'Yemen', 'Jemen'),
(891, 'CS', 'SCG', 'Serbia and Montenegro', 'Serbien und Montenegro', 'Serbia and Montenegro', 'Serbia y Montenegro', 'Serbie-et-Monténégro', 'Serbia e Montenegro', 'Servië en Montenegro'),
(894, 'ZM', 'ZMB', 'Zambie', 'Sambia', 'Zambia', 'Zambia', 'Zambie', 'Zambia', 'Zambia');

-- --------------------------------------------------------

--
-- Table structure for table `country_vies`
--

CREATE TABLE `country_vies` (
  `country_vies_id` int(11) NOT NULL,
  `alpha2` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `country_vies`
--

INSERT INTO `country_vies` (`country_vies_id`, `alpha2`) VALUES
(1, 'AT'),
(2, 'BE'),
(3, 'BG'),
(4, 'CY'),
(5, 'CZ'),
(6, 'DE'),
(7, 'DK'),
(8, 'EE'),
(9, 'EL'),
(10, 'ES'),
(11, 'FI'),
(12, 'FR'),
(13, 'GB'),
(14, 'HR'),
(15, 'HU'),
(16, 'IE'),
(17, 'IT'),
(18, 'LT'),
(19, 'LU'),
(20, 'LV'),
(21, 'MT'),
(22, 'NL'),
(23, 'PL'),
(24, 'PT'),
(25, 'RO'),
(26, 'SE'),
(27, 'SI'),
(28, 'SK');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `email`
--

CREATE TABLE `email` (
  `email_id` int(11) NOT NULL,
  `email_address` varchar(256) NOT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT '0',
  `email_verification` char(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `linkxt_service2key`
--

CREATE TABLE `linkxt_service2key` (
  `linkxt_service2key_id` int(11) NOT NULL,
  `key_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `link_address2organisation`
--

CREATE TABLE `link_address2organisation` (
  `link_address2organisation_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `organisation_id` int(11) NOT NULL,
  `address_type` enum('general','billing','visiting','validated','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `link_customer2organisation`
--

CREATE TABLE `link_customer2organisation` (
  `link_customer2organisation_id` int(11) NOT NULL,
  `organisation_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `link_customer2person`
--

CREATE TABLE `link_customer2person` (
  `link_customer2person_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `link_customer2project`
--

CREATE TABLE `link_customer2project` (
  `link_project2organisation_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `link_email2user`
--

CREATE TABLE `link_email2user` (
  `link_email2user_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `link_project2project`
--

CREATE TABLE `link_project2project` (
  `link_project2project_id` int(11) NOT NULL,
  `project_parent_id` int(11) NOT NULL,
  `project_child_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `link_session2user`
--

CREATE TABLE `link_session2user` (
  `link_session2user_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `organisation`
--

CREATE TABLE `organisation` (
  `organisation_id` int(11) NOT NULL,
  `organisation_name` varchar(512) NOT NULL,
  `organisation_type` enum('in_formation','association_unregged','association_regged','foundation','company','other') NOT NULL,
  `organisation_vat` varchar(128) NOT NULL COMMENT 'EU vat number',
  `organisation_nl_kvk` int(11) NOT NULL COMMENT 'Dutch KvK nummer',
  `organisation_country` varchar(2) NOT NULL COMMENT 'ISO 3166-1 alpha2'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

CREATE TABLE `person` (
  `person_id` int(11) NOT NULL,
  `person_first_name` varchar(256) NOT NULL COMMENT 'voornaam',
  `person_initials` varchar(16) NOT NULL COMMENT 'voorletters',
  `person_last_name_prefix` varchar(16) NOT NULL COMMENT 'tussenvoegsel',
  `person_last_name` varchar(256) NOT NULL COMMENT 'achternaam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `project_id` int(11) NOT NULL,
  `project_description_short` varchar(128) NOT NULL,
  `project_description_long` varchar(2048) NOT NULL,
  `project_billing_rate` int(11) NOT NULL COMMENT 'smallest unit in currency (cents)',
  `project_billing_currency` char(3) NOT NULL DEFAULT 'EUR',
  `project_billing_type` enum('inherit','free','timed','fixed') NOT NULL DEFAULT 'timed',
  `project_status` enum('negotiation','planned','running','finished','cancelled') NOT NULL DEFAULT 'planned'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `project_hours`
--

CREATE TABLE `project_hours` (
  `project_hours_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `project_hours_date` date NOT NULL,
  `project_hours_hours` int(2) NOT NULL,
  `project_hours_quarters` int(1) NOT NULL,
  `project_hours_billable` tinyint(1) NOT NULL DEFAULT '1',
  `project_hours_billed` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `session_id` int(11) NOT NULL,
  `session_hash` char(40) NOT NULL,
  `session_start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `session_ip_start` binary(16) NOT NULL,
  `session_ip_last` binary(16) NOT NULL,
  `session_ip_locked` tinyint(1) NOT NULL DEFAULT '0',
  `session_useragent` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `supplier_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_pbkdf2` char(77) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `xt_key`
--

CREATE TABLE `xt_key` (
  `xt_key_id` int(11) NOT NULL,
  `xt_key_val1` varchar(512) NOT NULL,
  `xt_key_val2` varchar(512) NOT NULL,
  `xt_key_val3` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `xt_pre_service`
--

CREATE TABLE `xt_pre_service` (
  `external_service_id` int(11) NOT NULL,
  `external_service_type` varchar(64) NOT NULL,
  `external_service_name` varchar(64) NOT NULL,
  `external_service_url1` varchar(512) DEFAULT NULL,
  `external_service_url2` varchar(512) DEFAULT NULL,
  `external_service_url3` varchar(512) DEFAULT NULL,
  `external_service_url4` varchar(512) DEFAULT NULL,
  `external_service_url5` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `xt_service`
--

CREATE TABLE `xt_service` (
  `xt_service_id` int(11) NOT NULL,
  `xt_service_type` varchar(64) NOT NULL,
  `xt_service_name` varchar(64) NOT NULL,
  `xt_service_url1` varchar(512) DEFAULT NULL,
  `xt_service_url2` varchar(512) DEFAULT NULL,
  `xt_service_url3` varchar(512) DEFAULT NULL,
  `xt_service_url4` varchar(512) DEFAULT NULL,
  `xt_service_url5` varchar(512) DEFAULT NULL,
  `xt_service_method1` enum('OPTIONS','GET','HEAD','POST','PUT','DELETE','TRACE','CONNECT') DEFAULT NULL COMMENT 'Method for URL1',
  `xt_service_method2` enum('OPTIONS','GET','HEAD','POST','PUT','DELETE','TRACE','CONNECT') DEFAULT NULL COMMENT 'Method for URL2',
  `xt_service_method3` enum('OPTIONS','GET','HEAD','POST','PUT','DELETE','TRACE','CONNECT') DEFAULT NULL COMMENT 'Method for URL3',
  `xt_service_method4` enum('OPTIONS','GET','HEAD','POST','PUT','DELETE','TRACE','CONNECT') DEFAULT NULL COMMENT 'Method for URL4',
  `xt_service_method5` enum('OPTIONS','GET','HEAD','POST','PUT','DELETE','TRACE','CONNECT') DEFAULT NULL COMMENT 'Method for URL5',
  `xt_service_cc1` varchar(64) DEFAULT NULL COMMENT 'Content-Type for URL1',
  `xt_service_cc2` varchar(64) DEFAULT NULL COMMENT 'Content-Type for URL2',
  `xt_service_cc3` varchar(64) DEFAULT NULL COMMENT 'Content-Type for URL3',
  `xt_service_cc4` varchar(64) DEFAULT NULL COMMENT 'Content-Type for URL4',
  `xt_service_cc5` varchar(64) DEFAULT NULL COMMENT 'Content-Type for URL5'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `adress_country` (`address_country`);

--
-- Indexes for table `capability`
--
ALTER TABLE `capability`
  ADD PRIMARY KEY (`capability_id`),
  ADD KEY `capability_name` (`capability_name`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`code`),
  ADD UNIQUE KEY `alpha2` (`alpha2`),
  ADD UNIQUE KEY `alpha3` (`alpha3`);

--
-- Indexes for table `country_vies`
--
ALTER TABLE `country_vies`
  ADD PRIMARY KEY (`country_vies_id`),
  ADD KEY `alpha2` (`alpha2`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `email`
--
ALTER TABLE `email`
  ADD PRIMARY KEY (`email_id`);

--
-- Indexes for table `linkxt_service2key`
--
ALTER TABLE `linkxt_service2key`
  ADD PRIMARY KEY (`linkxt_service2key_id`);

--
-- Indexes for table `link_address2organisation`
--
ALTER TABLE `link_address2organisation`
  ADD PRIMARY KEY (`link_address2organisation_id`);

--
-- Indexes for table `link_customer2organisation`
--
ALTER TABLE `link_customer2organisation`
  ADD PRIMARY KEY (`link_customer2organisation_id`),
  ADD UNIQUE KEY `organisation_id` (`organisation_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `link_customer2person`
--
ALTER TABLE `link_customer2person`
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `link_customer2project`
--
ALTER TABLE `link_customer2project`
  ADD PRIMARY KEY (`link_project2organisation_id`),
  ADD KEY `project_id` (`project_id`,`customer_id`),
  ADD KEY `organisation_id` (`customer_id`);

--
-- Indexes for table `link_email2user`
--
ALTER TABLE `link_email2user`
  ADD PRIMARY KEY (`link_email2user_id`);

--
-- Indexes for table `link_project2project`
--
ALTER TABLE `link_project2project`
  ADD PRIMARY KEY (`link_project2project_id`);

--
-- Indexes for table `link_session2user`
--
ALTER TABLE `link_session2user`
  ADD PRIMARY KEY (`link_session2user_id`);

--
-- Indexes for table `organisation`
--
ALTER TABLE `organisation`
  ADD PRIMARY KEY (`organisation_id`),
  ADD KEY `organisation_name` (`organisation_name`(191),`organisation_vat`),
  ADD KEY `organisation_country` (`organisation_country`);

--
-- Indexes for table `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`person_id`),
  ADD KEY `first_name` (`person_first_name`(191),`person_last_name`(191));

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `project_hours`
--
ALTER TABLE `project_hours`
  ADD PRIMARY KEY (`project_hours_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `session_hash` (`session_hash`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `xt_key`
--
ALTER TABLE `xt_key`
  ADD PRIMARY KEY (`xt_key_id`);

--
-- Indexes for table `xt_pre_service`
--
ALTER TABLE `xt_pre_service`
  ADD PRIMARY KEY (`external_service_id`),
  ADD KEY `external_service_type` (`external_service_type`);

--
-- Indexes for table `xt_service`
--
ALTER TABLE `xt_service`
  ADD PRIMARY KEY (`xt_service_id`),
  ADD KEY `external_service_type` (`xt_service_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `capability`
--
ALTER TABLE `capability`
  MODIFY `capability_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `country_vies`
--
ALTER TABLE `country_vies`
  MODIFY `country_vies_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `email`
--
ALTER TABLE `email`
  MODIFY `email_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `linkxt_service2key`
--
ALTER TABLE `linkxt_service2key`
  MODIFY `linkxt_service2key_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `link_address2organisation`
--
ALTER TABLE `link_address2organisation`
  MODIFY `link_address2organisation_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `link_customer2organisation`
--
ALTER TABLE `link_customer2organisation`
  MODIFY `link_customer2organisation_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `link_customer2project`
--
ALTER TABLE `link_customer2project`
  MODIFY `link_project2organisation_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `link_email2user`
--
ALTER TABLE `link_email2user`
  MODIFY `link_email2user_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `link_project2project`
--
ALTER TABLE `link_project2project`
  MODIFY `link_project2project_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `link_session2user`
--
ALTER TABLE `link_session2user`
  MODIFY `link_session2user_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `organisation`
--
ALTER TABLE `organisation`
  MODIFY `organisation_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `person`
--
ALTER TABLE `person`
  MODIFY `person_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `project_hours`
--
ALTER TABLE `project_hours`
  MODIFY `project_hours_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `session`
--
ALTER TABLE `session`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `xt_key`
--
ALTER TABLE `xt_key`
  MODIFY `xt_key_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `xt_pre_service`
--
ALTER TABLE `xt_pre_service`
  MODIFY `external_service_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `xt_service`
--
ALTER TABLE `xt_service`
  MODIFY `xt_service_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `link_customer2organisation`
--
ALTER TABLE `link_customer2organisation`
  ADD CONSTRAINT `link_customer2organisation_ibfk_1` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`organisation_id`);

--
-- Constraints for table `link_customer2person`
--
ALTER TABLE `link_customer2person`
  ADD CONSTRAINT `link_customer2person_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `link_customer2person_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`);

--
-- Constraints for table `link_customer2project`
--
ALTER TABLE `link_customer2project`
  ADD CONSTRAINT `link_customer2project_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`),
  ADD CONSTRAINT `link_customer2project_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Constraints for table `project_hours`
--
ALTER TABLE `project_hours`
  ADD CONSTRAINT `project_hours_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
