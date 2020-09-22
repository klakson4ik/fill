<?php

require 'config/Config_DB.php';
require 'config/DB.php';
use config\DB;
$db = new PDO("mysql:host=" . $dbHost, $dbLogin, $dbPassword);
$db->exec("CREATE DATABASE `$dbName` COLLATE 'utf8_bin'") or die(print_r($db->errorInfo()[2], true));;
$db = DB::connector();

$sql = "CREATE TABLE `test` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) COLLATE 'utf8_bin' NOT NULL,
  `code` int(10) unsigned NOT NULL,
  `weight` varchar(255) COLLATE 'utf8_bin' NOT NULL DEFAULT '0',
  `quantity_msk` int(10) unsigned NOT NULL DEFAULT '0',
  `quantity_spb` int(10) unsigned NOT NULL DEFAULT '0',
  `quantity_smr` int(10) unsigned NOT NULL DEFAULT '0',
  `quantity_srv` int(10) unsigned NOT NULL DEFAULT '0',
  `quantity_kzn` int(10) unsigned NOT NULL DEFAULT '0',
  `quantity_nsk` int(10) unsigned NOT NULL DEFAULT '0',
  `quantity_chbk` int(10) unsigned NOT NULL DEFAULT '0',
  `quantity_bschbk` int(10) unsigned NOT NULL DEFAULT '0',
  `price_msk` int(10) unsigned NOT NULL DEFAULT '0',
  `price_spb` int(10) unsigned NOT NULL DEFAULT '0',
  `price_smr` int(10) unsigned NOT NULL DEFAULT '0',
  `price_srv` int(10) unsigned NOT NULL DEFAULT '0',
  `price_kzn` int(10) unsigned NOT NULL DEFAULT '0',
  `price_nsk` int(10) unsigned NOT NULL DEFAULT '0',
  `price_chbk` int(10) unsigned NOT NULL DEFAULT '0',
  `price_bschbk` int(10) unsigned NOT NULL DEFAULT '0',
  `usage` text COLLATE 'utf8_bin'
) ENGINE='InnoDB' COLLATE 'utf8_bin';()";

$db->exec($sql) or die(print_r($db->errorInfo()[2], true));;
