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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL DEFAULT '0',
  `image` varchar(150) NOT NULL,
  `id_familia` int(11) DEFAULT NULL,
  `id_subfamilia` int(11) DEFAULT NULL,
  `factusol_codfte` int(11) DEFAULT '0',
  `ordering` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botiga_comandes`
--

CREATE TABLE IF NOT EXISTS `#__botiga_comandes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` datetime NOT NULL,
  `userid` int(11) NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botiga_comandesDetall`
--

CREATE TABLE IF NOT EXISTS `#__botiga_comandesDetall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idComanda` int(11) NOT NULL,
  `idItem` int(11) NOT NULL,
  `price` float(10,2) NOT NULL DEFAULT '0.00',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botigas_rebuts`
--

CREATE TABLE IF NOT EXISTS `#__botiga_rebuts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` datetime NOT NULL,
  `userid` int(11) NOT NULL DEFAULT '0',
  `import` float(10,2) NOT NULL DEFAULT '0.00',
  `recarrec` float(10,2) NOT NULL DEFAULT '0.00',
  `importambrecarrec` float(10,2) NOT NULL DEFAULT '0.00',
  `idComanda` int(11) NOT NULL DEFAULT '0',
  `formaPag` varchar(1) NOT NULL DEFAULT '' COMMENT 'P->PayPal; C->Targeta; T->Transferencia',
  `payment_status` varchar(10) NOT NULL DEFAULT '',
  `titular` varchar(100) NOT NULL DEFAULT '',
  `iban` varchar(50) NOT NULL DEFAULT '',
  `paypal` varchar(50) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Estructura de tabla para la tabla `#__botiga_items`
--

CREATE TABLE IF NOT EXISTS `#__botiga_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(150) NOT NULL DEFAULT '',
  `brand` int(11) NOT NULL DEFAULT '0',
  `s_description` text NOT NULL,
  `description` text NOT NULL,
  `image1` varchar(150) NOT NULL DEFAULT '',
  `image2` varchar(150) NOT NULL DEFAULT '',
  `image3` varchar(150) NOT NULL DEFAULT '',
  `image4` varchar(150) NOT NULL DEFAULT '',
  `image5` varchar(150) NOT NULL DEFAULT '',
  `pdf` varchar(150) NOT NULL DEFAULT '',
  `price` float(10,2) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL DEFAULT '0',
  `ref` varchar(15) DEFAULT NULL,
  `factusol_codart` varchar(13) DEFAULT NULL,
  `sincronitzat` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botiga_coupons`
--

CREATE TABLE `#__botiga_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `coupon` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `#__botiga_users`
--

CREATE TABLE `#__botiga_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usergroup` int(11) DEFAULT NULL,
  `nom_empresa` varchar(255) DEFAULT NULL,
  `mail_empresa` varchar(255) DEFAULT NULL,
  `tarifa` tinyint(4) NOT NULL DEFAULT '1',
  `userid` int(11) DEFAULT '0',
  `adreca` varchar(100) DEFAULT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `poblacio` varchar(50) DEFAULT NULL,
  `provincia` varchar(50) DEFAULT NULL,
  `pais` varchar(50) DEFAULT NULL,
  `telefon` varchar(50) DEFAULT NULL,
  `activitat` varchar(255) DEFAULT NULL,
  `nivell` int(11) NOT NULL DEFAULT '1',
  `published` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


