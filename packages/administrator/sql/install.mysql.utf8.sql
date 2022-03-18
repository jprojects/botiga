-- botiga
-- Copyright © 2010 - All rights reserved.
-- License: GNU/GPL
--
-- botiga table(s) definition
--
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botiga_brands`
--

CREATE TABLE IF NOT EXISTS `#__botiga_brands` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL DEFAULT '0',
  `image` varchar(150) NOT NULL,
  `header` varchar(150) NOT NULL,
  `factusol_codfte` int(11) DEFAULT '0'  COMMENT 'Codi fabricant de Factusol',
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botiga_categories`
--

CREATE TABLE IF NOT EXISTS `#__botiga_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catid` int(11) NOT NULL,
  `factusol_codfam` varchar(10) NOT NULL COMMENT 'Codi familia Factusol',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botiga_comandes`
--

CREATE TABLE IF NOT EXISTS `#__botiga_comandes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniqid` varchar(50) NOT NULL DEFAULT '',
  `data` datetime NOT NULL,
  `userid` int(11) NOT NULL,
  `sessid` varchar(150) NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '0' COMMENT '1-Pendent;2-Pendent pagar;3-Pagada;4-Pagada al 50%',
  `subtotal` float(10,2) NOT NULL DEFAULT '0.00',
  `shipment` float(10,2) NOT NULL DEFAULT '0.00',
  `discount` float(10,2) NOT NULL DEFAULT '0.00' COMMENT 'total descomptes inclos cupo si aplicable',
  `idCoupon` int(11) NOT NULL DEFAULT '0',
  `iva_percent` int(5) NOT NULL DEFAULT '0',
  `iva_total` float(10,2) NOT NULL DEFAULT '0.00',
  `re_percent` float(10,2) NOT NULL DEFAULT '0.00',
  `re_total` float(10,2) NOT NULL DEFAULT '0.00' COMMENT 'recarrec equivalencia',
  `total` float(10,2) NOT NULL DEFAULT '0.00',
  `processor` varchar(50) NOT NULL DEFAULT '',
  `observa` varchar(250) NOT NULL DEFAULT '' COMMENT 'Observacions del client',
  `mail_empresa` varchar(100) NOT NULL DEFAULT '',
  `nom_empresa` varchar(100) NOT NULL DEFAULT '',
  `nombre` varchar(50) NOT NULL DEFAULT '',
  `cif` varchar(50) NOT NULL DEFAULT '',
  `telefon` varchar(50) NOT NULL DEFAULT '',
  `adreca` varchar(100) NOT NULL DEFAULT '',
  `cp` varchar(50) NOT NULL DEFAULT '',
  `poblacio` varchar(50) NOT NULL DEFAULT '',
  `pais` varchar(50) NOT NULL DEFAULT '',
  `provincia` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `browser` varchar(100) NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botiga_comandesDetall`
--

CREATE TABLE IF NOT EXISTS `#__botiga_comandesDetall` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idComanda` int(11) NOT NULL,
  `idItem` int(11) NOT NULL,
  `price` float(10,2) NOT NULL DEFAULT '0.00',
  `qty` int(5) NOT NULL DEFAULT '0',
  `dte_linia` float(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botigas_rebuts`
--

CREATE TABLE IF NOT EXISTS `#__botiga_rebuts` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `data` datetime NOT NULL,
  `userid` int(11) NOT NULL DEFAULT '0',
  `import` float(10,2) NOT NULL DEFAULT '0.00',
  `idComanda` int(11) NOT NULL DEFAULT '0',
  `formaPag` varchar(1) NOT NULL DEFAULT '' COMMENT 'P->PayPal; C->Targeta; T->Transferencia',
  `payment_status` varchar(10) NOT NULL DEFAULT '',
  `titular` varchar(100) NOT NULL DEFAULT '',
  `iban` varchar(50) NOT NULL DEFAULT '',
  `paypal` varchar(50) NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Estructura de tabla para la tabla `#__botiga_items`
--

CREATE TABLE IF NOT EXISTS `#__botiga_items` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `catid` varchar(150) NOT NULL DEFAULT '0',
  `name` varchar(150) NOT NULL DEFAULT '',
  `barcode` varchar(150) NOT NULL DEFAULT '',
  `child` int(11) NOT NULL DEFAULT '0',
  `usergroup` int(11) NOT NULL DEFAULT '1',
  `brand` int(11) NOT NULL DEFAULT '0',
  `s_description` text NOT NULL,
  `description` text NOT NULL,
  `image1` varchar(150) NOT NULL DEFAULT '',
  `images` text NOT NULL,
  `pdf` varchar(150) NOT NULL DEFAULT '',
  `price` text NOT NULL,
  `pvp` float(10,2) NOT NULL,
  `garantia` varchar(150) NOT NULL DEFAULT '',
  `envio` varchar(150) NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL DEFAULT '',
  `ref` text NOT NULL,
  `extres` varchar(15) DEFAULT NULL,
  `pes` varchar(50) NOT NULL DEFAULT '',
  `mida` varchar(50) NOT NULL DEFAULT '',
  `stock` int(11) NOT NULL DEFAULT '0',
  `factusol_codart` varchar(13) DEFAULT NULL,
  `aws` tinyint(1) DEFAULT '0',
  `aws_sincronitzat` tinyint(1) DEFAULT '0',
  `sincronitzat` tinyint(1) DEFAULT '0',
  `esborrableDespresDeSincro` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botiga_items_prices`
--

CREATE TABLE IF NOT EXISTS `#__botiga_items_prices` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `itemId` int(11) NOT NULL DEFAULT '0',
  `usergroup` int(11) NOT NULL DEFAULT '1',
  `price` float(10,2) NOT NULL,
  `sincronitzat` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botiga_shipments`
--

CREATE TABLE IF NOT EXISTS `#__botiga_shipments` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL DEFAULT '',
  `usergroup` int(11) DEFAULT NULL DEFAULT '1',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `conditional` tinyint(1) NOT NULL DEFAULT '0'  COMMENT '1->Mismos paises; T->Distintos paises',
  `country` text NOT NULL,
  `min` varchar(50) NOT NULL DEFAULT '',
  `max` varchar(50) NOT NULL DEFAULT '',
  `operator` char(7) NOT NULL DEFAULT '',
  `total` float(10,2) NOT NULL DEFAULT '0',
  `free` int(5) NOT NULL DEFAULT '0'  COMMENT 'Gratuito a partir de esta cantidad',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botiga_discounts`
--

CREATE TABLE IF NOT EXISTS `#__botiga_discounts` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL DEFAULT '',
  `type` tinyint(1) DEFAULT NULL DEFAULT '0',
  `usergroup` int(11) NOT NULL DEFAULT '1',
  `idItem` int(11) DEFAULT NULL,
  `min` int(5) NOT NULL,
  `max` int(5) NOT NULL,
  `box_items` int(5) NOT NULL,
  `total` float(10,2) NOT NULL DEFAULT '0',
  `message` varchar(150) NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botiga_documents`
--

CREATE TABLE IF NOT EXISTS `#__botiga_documents` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL DEFAULT '',
  `idItem` int(11) DEFAULT NULL,
  `filename` varchar(150) NOT NULL DEFAULT '',
  `language` char(7) NOT NULL DEFAULT '',
  `listed` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botiga_countries`
--

CREATE TABLE IF NOT EXISTS `#__botiga_countries` (
  `country_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `country_name` char(64) DEFAULT NULL,
  `country_code` char(3) DEFAULT NULL,
  `ordering` int(2) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`country_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botiga_favorites`
--

CREATE TABLE IF NOT EXISTS `#__botiga_favorites` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `itemid` char(64) DEFAULT NULL,
  `userid` char(3) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botiga_coupons`
--

CREATE TABLE IF NOT EXISTS `#__botiga_coupons` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0- Percent; 1 - Resta',
  `coupon` varchar(50) NOT NULL,
  `valor` float(10,2) NOT NULL,
  `finishDate` date NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botiga_users`
--

CREATE TABLE IF NOT EXISTS `#__botiga_users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usergroup` int(11) DEFAULT NULL,
  `nom_empresa` varchar(255) DEFAULT NULL,
  `mail_empresa` varchar(255) DEFAULT NULL,
  `nombre` varchar(150) DEFAULT '',
  `cif` varchar(50) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `userid` int(11) DEFAULT '0',
  `adreca` varchar(100) DEFAULT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `poblacio` varchar(50) DEFAULT NULL,
  `provincia` varchar(50) DEFAULT NULL,
  `pais` varchar(50) DEFAULT NULL,
  `telefon` varchar(50) DEFAULT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `validate` tinyint(1) NOT NULL DEFAULT '0',
  `remarketing` tinyint(1) NOT NULL DEFAULT '1',
  `dte_linia` float(10,2) NOT NULL DEFAULT '0.00',
  `params` text NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------


--
-- Estructura de tabla para la tabla `#__botiga_user_address`
--

CREATE TABLE `#__botiga_user_address` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(150) NOT NULL DEFAULT '',
  `adreca` varchar(150) NOT NULL DEFAULT '',
  `cp` varchar(50) NOT NULL DEFAULT '',
  `poblacio` varchar(50) NOT NULL DEFAULT '',
  `provincia` varchar(50) NOT NULL DEFAULT '',
  `pais` varchar(50) NOT NULL DEFAULT '',
  `telefon` varchar(50) NOT NULL DEFAULT '',
  `ref_externa` varchar(50) NOT NULL DEFAULT '',
  `activa` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botiga_savedCarts`
--

CREATE TABLE IF NOT EXISTS `#__botiga_savedCarts` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idComanda` int(11) DEFAULT NULL,
  `data` datetime NOT NULL,
  `userid` int(11) DEFAULT '0',
  `cart` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `afi_botiga_countries`
--

INSERT INTO `#__botiga_countries` (`country_id`, `country_name`, `country_code`, `ordering`, `published`) VALUES
(1, 'Afghanistan', 'AFG', 0, 1),
(2, 'Albania', 'ALB', 0, 1),
(3, 'Algeria', 'DZA', 0, 1),
(4, 'American Samoa', 'ASM', 0, 1),
(5, 'Andorra', 'AND', 0, 1),
(6, 'Angola', 'AGO', 0, 1),
(7, 'Anguilla', 'AIA', 0, 1),
(8, 'Antarctica', 'ATA', 0, 1),
(9, 'Antigua and Barbuda', 'ATG', 0, 1),
(10, 'Argentina', 'ARG', 0, 1),
(11, 'Armenia', 'ARM', 0, 1),
(12, 'Aruba', 'ABW', 0, 1),
(13, 'Australia', 'AUS', 0, 1),
(14, 'Austria', 'AUT', 0, 1),
(15, 'Azerbaijan', 'AZE', 0, 1),
(16, 'Bahamas', 'BHS', 0, 1),
(17, 'Bahrain', 'BHR', 0, 1),
(18, 'Bangladesh', 'BGD', 0, 1),
(19, 'Barbados', 'BRB', 0, 1),
(20, 'Belarus', 'BLR', 0, 1),
(21, 'Belgium', 'BEL', 0, 1),
(22, 'Belize', 'BLZ', 0, 1),
(23, 'Benin', 'BEN', 0, 1),
(24, 'Bermuda', 'BMU', 0, 1),
(25, 'Bhutan', 'BTN', 0, 1),
(26, 'Bolivia', 'BOL', 0, 1),
(27, 'Bosnia and Herzegowina', 'BIH', 0, 1),
(28, 'Botswana', 'BWA', 0, 1),
(29, 'Bouvet Island', 'BVT', 0, 1),
(30, 'Brazil', 'BRA', 0, 1),
(31, 'British Indian Ocean Territory', 'IOT', 0, 1),
(32, 'Brunei Darussalam', 'BRN', 0, 1),
(33, 'Bulgaria', 'BGR', 0, 1),
(34, 'Burkina Faso', 'BFA', 0, 1),
(35, 'Burundi', 'BDI', 0, 1),
(36, 'Cambodia', 'KHM', 0, 1),
(37, 'Cameroon', 'CMR', 0, 1),
(38, 'Canada', 'CAN', 0, 1),
(39, 'Cape Verde', 'CPV', 0, 1),
(40, 'Cayman Islands', 'CYM', 0, 1),
(41, 'Central African Republic', 'CAF', 0, 1),
(42, 'Chad', 'TCD', 0, 1),
(43, 'Chile', 'CHL', 0, 1),
(44, 'China', 'CHN', 0, 1),
(45, 'Christmas Island', 'CXR', 0, 1),
(46, 'Cocos (Keeling) Islands', 'CCK', 0, 1),
(47, 'Colombia', 'COL', 0, 1),
(48, 'Comoros', 'COM', 0, 1),
(49, 'Congo', 'COG', 0, 1),
(50, 'Cook Islands', 'COK', 0, 1),
(51, 'Costa Rica', 'CRI', 0, 1),
(52, 'Cote D\'Ivoire', 'CIV', 0, 1),
(53, 'Croatia', 'HRV', 0, 1),
(54, 'Cuba', 'CUB', 0, 1),
(55, 'Cyprus', 'CYP', 0, 1),
(56, 'Czech Republic', 'CZE', 0, 1),
(57, 'Denmark', 'DNK', 0, 1),
(58, 'Djibouti', 'DJI', 0, 1),
(59, 'Dominica', 'DMA', 0, 1),
(60, 'Dominican Republic', 'DOM', 0, 1),
(61, 'East Timor', 'TMP', 0, 1),
(62, 'Ecuador', 'ECU', 0, 1),
(63, 'Egypt', 'EGY', 0, 1),
(64, 'El Salvador', 'SLV', 0, 1),
(65, 'Equatorial Guinea', 'GNQ', 0, 1),
(66, 'Eritrea', 'ERI', 0, 1),
(67, 'Estonia', 'EST', 0, 1),
(68, 'Ethiopia', 'ETH', 0, 1),
(69, 'Falkland Islands (Malvinas)', 'FLK', 0, 1),
(70, 'Faroe Islands', 'FRO', 0, 1),
(71, 'Fiji', 'FJI', 0, 1),
(72, 'Finland', 'FIN', 0, 1),
(73, 'France', 'FRA', 0, 1),
(75, 'French Guiana', 'GUF', 0, 1),
(76, 'French Polynesia', 'PYF', 0, 1),
(77, 'French Southern Territories', 'ATF', 0, 1),
(78, 'Gabon', 'GAB', 0, 1),
(79, 'Gambia', 'GMB', 0, 1),
(80, 'Georgia', 'GEO', 0, 1),
(81, 'Germany', 'DEU', 0, 1),
(82, 'Ghana', 'GHA', 0, 1),
(83, 'Gibraltar', 'GIB', 0, 1),
(84, 'Greece', 'GRC', 0, 1),
(85, 'Greenland', 'GRL', 0, 1),
(86, 'Grenada', 'GRD', 0, 1),
(87, 'Guadeloupe', 'GLP', 0, 1),
(88, 'Guam', 'GUM', 0, 1),
(89, 'Guatemala', 'GTM', 0, 1),
(90, 'Guinea', 'GIN', 0, 1),
(91, 'Guinea-bissau', 'GNB', 0, 1),
(92, 'Guyana', 'GUY', 0, 1),
(93, 'Haiti', 'HTI', 0, 1),
(94, 'Heard and Mc Donald Islands', 'HMD', 0, 1),
(95, 'Honduras', 'HND', 0, 1),
(96, 'Hong Kong', 'HKG', 0, 1),
(97, 'Hungary', 'HUN', 0, 1),
(98, 'Iceland', 'ISL', 0, 1),
(99, 'India', 'IND', 0, 1),
(100, 'Indonesia', 'IDN', 0, 1),
(101, 'Iran (Islamic Republic of)', 'IRN', 0, 1),
(102, 'Iraq', 'IRQ', 0, 1),
(103, 'Ireland', 'IRL', 0, 1),
(104, 'Israel', 'ISR', 0, 1),
(105, 'Italy', 'ITA', 0, 1),
(106, 'Jamaica', 'JAM', 0, 1),
(107, 'Japan', 'JPN', 0, 1),
(108, 'Jordan', 'JOR', 0, 1),
(109, 'Kazakhstan', 'KAZ', 0, 1),
(110, 'Kenya', 'KEN', 0, 1),
(111, 'Kiribati', 'KIR', 0, 1),
(112, 'Korea, Democratic People\'s Republic of', 'PRK', 0, 1),
(113, 'Korea, Republic of', 'KOR', 0, 1),
(114, 'Kuwait', 'KWT', 0, 1),
(115, 'Kyrgyzstan', 'KGZ', 0, 1),
(116, 'Lao People\'s Democratic Republic', 'LAO', 0, 1),
(117, 'Latvia', 'LVA', 0, 1),
(118, 'Lebanon', 'LBN', 0, 1),
(119, 'Lesotho', 'LSO', 0, 1),
(120, 'Liberia', 'LBR', 0, 1),
(121, 'Libya', 'LBY', 0, 1),
(122, 'Liechtenstein', 'LIE', 0, 1),
(123, 'Lithuania', 'LTU', 0, 1),
(124, 'Luxembourg', 'LUX', 0, 1),
(125, 'Macau', 'MAC', 0, 1),
(126, 'Macedonia, The Former Yugoslav Republic of', 'MKD', 0, 1),
(127, 'Madagascar', 'MDG', 0, 1),
(128, 'Malawi', 'MWI', 0, 1),
(129, 'Malaysia', 'MYS', 0, 1),
(130, 'Maldives', 'MDV', 0, 1),
(131, 'Mali', 'MLI', 0, 1),
(132, 'Malta', 'MLT', 0, 1),
(133, 'Marshall Islands', 'MHL', 0, 1),
(134, 'Martinique', 'MTQ', 0, 1),
(135, 'Mauritania', 'MRT', 0, 1),
(136, 'Mauritius', 'MUS', 0, 1),
(137, 'Mayotte', 'MYT', 0, 1),
(138, 'Mexico', 'MEX', 0, 1),
(139, 'Micronesia, Federated States of', 'FSM', 0, 1),
(140, 'Moldova, Republic of', 'MDA', 0, 1),
(141, 'Monaco', 'MCO', 0, 1),
(142, 'Mongolia', 'MNG', 0, 1),
(143, 'Montserrat', 'MSR', 0, 1),
(144, 'Morocco', 'MAR', 0, 1),
(145, 'Mozambique', 'MOZ', 0, 1),
(146, 'Myanmar', 'MMR', 0, 1),
(147, 'Namibia', 'NAM', 0, 1),
(148, 'Nauru', 'NRU', 0, 1),
(149, 'Nepal', 'NPL', 0, 1),
(150, 'Netherlands', 'NLD', 0, 1),
(151, 'Netherlands Antilles', 'ANT', 0, 1),
(152, 'New Caledonia', 'NCL', 0, 1),
(153, 'New Zealand', 'NZL', 0, 1),
(154, 'Nicaragua', 'NIC', 0, 1),
(155, 'Niger', 'NER', 0, 1),
(156, 'Nigeria', 'NGA', 0, 1),
(157, 'Niue', 'NIU', 0, 1),
(158, 'Norfolk Island', 'NFK', 0, 1),
(159, 'Northern Mariana Islands', 'MNP', 0, 1),
(160, 'Norway', 'NOR', 0, 1),
(161, 'Oman', 'OMN', 0, 1),
(162, 'Pakistan', 'PAK', 0, 1),
(163, 'Palau', 'PLW', 0, 1),
(164, 'Panama', 'PAN', 0, 1),
(165, 'Papua New Guinea', 'PNG', 0, 1),
(166, 'Paraguay', 'PRY', 0, 1),
(167, 'Peru', 'PER', 0, 1),
(168, 'Philippines', 'PHL', 0, 1),
(169, 'Pitcairn', 'PCN', 0, 1),
(170, 'Poland', 'POL', 0, 1),
(171, 'Portugal', 'PRT', 0, 1),
(172, 'Puerto Rico', 'PRI', 0, 1),
(173, 'Qatar', 'QAT', 0, 1),
(174, 'Reunion', 'REU', 0, 1),
(175, 'Romania', 'ROM', 0, 1),
(176, 'Russian Federation', 'RUS', 0, 1),
(177, 'Rwanda', 'RWA', 0, 1),
(178, 'Saint Kitts and Nevis', 'KNA', 0, 1),
(179, 'Saint Lucia', 'LCA', 0, 1),
(180, 'Saint Vincent and the Grenadines', 'VCT', 0, 1),
(181, 'Samoa', 'WSM', 0, 1),
(182, 'San Marino', 'SMR', 0, 1),
(183, 'Sao Tome and Principe', 'STP', 0, 1),
(184, 'Saudi Arabia', 'SAU', 0, 1),
(185, 'Senegal', 'SEN', 0, 1),
(186, 'Seychelles', 'SYC', 0, 1),
(187, 'Sierra Leone', 'SLE', 0, 1),
(188, 'Singapore', 'SGP', 0, 1),
(189, 'Slovakia', 'SVK', 0, 1),
(190, 'Slovenia', 'SVN', 0, 1),
(191, 'Solomon Islands', 'SLB', 0, 1),
(192, 'Somalia', 'SOM', 0, 1),
(193, 'South Africa', 'ZAF', 0, 1),
(194, 'South Georgia and the South Sandwich Islands', 'SGS', 0, 1),
(195, 'Spain', 'ESP', 0, 1),
(196, 'Sri Lanka', 'LKA', 0, 1),
(197, 'St. Helena', 'SHN', 0, 1),
(198, 'St. Pierre and Miquelon', 'SPM', 0, 1),
(199, 'Sudan', 'SDN', 0, 1),
(200, 'Suriname', 'SUR', 0, 1),
(201, 'Svalbard and Jan Mayen Islands', 'SJM', 0, 1),
(202, 'Swaziland', 'SWZ', 0, 1),
(203, 'Sweden', 'SWE', 0, 1),
(204, 'Switzerland', 'CHE', 0, 1),
(205, 'Syrian Arab Republic', 'SYR', 0, 1),
(206, 'Taiwan', 'TWN', 0, 1),
(207, 'Tajikistan', 'TJK', 0, 1),
(208, 'Tanzania, United Republic of', 'TZA', 0, 1),
(209, 'Thailand', 'THA', 0, 1),
(210, 'Togo', 'TGO', 0, 1),
(211, 'Tokelau', 'TKL', 0, 1),
(212, 'Tonga', 'TON', 0, 1),
(213, 'Trinidad and Tobago', 'TTO', 0, 1),
(214, 'Tunisia', 'TUN', 0, 1),
(215, 'Turkey', 'TUR', 0, 1),
(216, 'Turkmenistan', 'TKM', 0, 1),
(217, 'Turks and Caicos Islands', 'TCA', 0, 1),
(218, 'Tuvalu', 'TUV', 0, 1),
(219, 'Uganda', 'UGA', 0, 1),
(220, 'Ukraine', 'UKR', 0, 1),
(221, 'United Arab Emirates', 'ARE', 0, 1),
(222, 'United Kingdom', 'GBR', 0, 1),
(223, 'United States', 'USA', 0, 1),
(224, 'United States Minor Outlying Islands', 'UMI', 0, 1),
(225, 'Uruguay', 'URY', 0, 1),
(226, 'Uzbekistan', 'UZB', 0, 1),
(227, 'Vanuatu', 'VUT', 0, 1),
(228, 'Vatican City State (Holy See)', 'VAT', 0, 1),
(229, 'Venezuela', 'VEN', 0, 1),
(230, 'Viet Nam', 'VNM', 0, 1),
(231, 'Virgin Islands (British)', 'VGB', 0, 1),
(232, 'Virgin Islands (U.S.)', 'VIR', 0, 1),
(233, 'Wallis and Futuna Islands', 'WLF', 0, 1),
(234, 'Western Sahara', 'ESH', 0, 1),
(235, 'Yemen', 'YEM', 0, 1),
(237, 'The Democratic Republic of Congo', 'DRC', 0, 1),
(238, 'Zambia', 'ZMB', 0, 1),
(239, 'Zimbabwe', 'ZWE', 0, 1),
(240, 'East Timor', 'XET', 0, 1),
(241, 'Jersey', 'JEY', 0, 1),
(242, 'St. Barthelemy', 'XSB', 0, 1),
(243, 'St. Eustatius', 'XSE', 0, 1),
(244, 'Canary Islands', 'XCA', 0, 1),
(245, 'Serbia', 'SRB', 0, 1),
(246, 'Sint Maarten (French Antilles)', 'MAF', 0, 1),
(247, 'Sint Maarten (Netherlands Antilles)', 'SXM', 0, 1),
(248, 'Palestinian Territory, occupied', 'PSE', 0, 1);
