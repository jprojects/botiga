-- botiga
-- Copyright © 2010 - All rights reserved.
-- License: GNU/GPL
--
-- botiga table(s) definition
--
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acj_botiga_brands`
--

CREATE TABLE `acj_botiga_brands` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL DEFAULT '0',
  `image` varchar(150) NOT NULL,
  `id_familia` int(11) DEFAULT NULL,
  `id_subfamilia` int(11) DEFAULT NULL,
  `factusol_codfte` int(11) DEFAULT '0',
  `ordering` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acj_botiga_items`
--

CREATE TABLE `acj_botiga_items` (
  `id` int(11) NOT NULL,
  `catid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(150) NOT NULL DEFAULT '',
  `fecha_fabricacion` varchar(150) NOT NULL,
  `diametro_rodillo` int(5) NOT NULL,
  `marca` int(11) NOT NULL DEFAULT '0',
  `anchura_rodillo` varchar(5) NOT NULL DEFAULT '',
  `calefaccion` varchar(150) NOT NULL DEFAULT '',
  `capacidad` varchar(150) NOT NULL DEFAULT '',
  `carga` varchar(150) NOT NULL DEFAULT '',
  `volcable` varchar(150) NOT NULL DEFAULT '',
  `compartimentos` int(5) NOT NULL,
  `tipo` smallint(1) NOT NULL,
  `pinzas intro` int(5) NOT NULL,
  `ancho_trabajo` int(5) NOT NULL DEFAULT '0',
  `vias_trabajo` varchar(150) NOT NULL DEFAULT '',
  `pliegues_trans` int(5) NOT NULL DEFAULT '0',
  `pliegues_long` int(5) NOT NULL DEFAULT '0',
  `pliegues_tipo` smallint(1) NOT NULL,
  `vias_descarga` int(5) NOT NULL DEFAULT '0',
  `sistema_transp` tinyint(1) NOT NULL DEFAULT '0',
  `presion` int(5) NOT NULL,
  `depositos` int(5) NOT NULL DEFAULT '0',
  `filtros` varchar(150) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `extrainfo` text NOT NULL,
  `image1` varchar(150) NOT NULL DEFAULT '',
  `image2` varchar(150) NOT NULL DEFAULT '',
  `image3` varchar(150) NOT NULL DEFAULT '',
  `image4` varchar(150) NOT NULL DEFAULT '',
  `image5` varchar(150) NOT NULL DEFAULT '',
  `pdf1` varchar(150) NOT NULL DEFAULT '',
  `pdf2` varchar(150) NOT NULL,
  `pdf3` varchar(150) NOT NULL,
  `pdf4` varchar(150) NOT NULL,
  `pdf5` varchar(150) NOT NULL,
  `pdf6` varchar(150) NOT NULL,
  `price1` float(10,2) NOT NULL,
  `price2` float(10,2) NOT NULL,
  `price3` float(10,2) NOT NULL,
  `price4` float(10,2) NOT NULL,
  `price5` float(10,2) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL DEFAULT '0',
  `ref` varchar(15) DEFAULT NULL,
  `factusol_codart` varchar(13) DEFAULT NULL,
  `sincronitzat` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acj_botiga_coupons`
--

CREATE TABLE `acj_botiga_coupons` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `coupon` varchar(50) NOT NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acj_botiga_users`
--

CREATE TABLE `acj_botiga_users` (
  `id` int(11) NOT NULL,
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


