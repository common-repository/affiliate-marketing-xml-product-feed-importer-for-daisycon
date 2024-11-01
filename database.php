<?php 
/* Daisycon affiliate marketing plugin
 * File: database.php
 * 
 * To create the database for the plugin and to update the tables if necessary.
 * 
 */

global $wpdb;

// Categories table
$sql_categories = "
	CREATE TABLE IF NOT EXISTS `categories` (
		`category_id` int(11) NOT NULL AUTO_INCREMENT,
		`name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`rename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`visible` tinyint(1) NOT NULL,
	  PRIMARY KEY (`category_id`), 
	  UNIQUE KEY `category_id` (`category_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$query_categories = mysql_query($sql_categories);

// Publisher table
$sql_publisher = "
	CREATE TABLE IF NOT EXISTS `publisher` (
		`daisycon_id` int(11) NOT NULL AUTO_INCREMENT,
	  	`username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	  	`password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	  	`feed` text COLLATE utf8_unicode_ci NOT NULL,
	  	`programsproductfeed` text COLLATE utf8_unicode_ci NOT NULL,
	  	`program_date` datetime NOT NULL,
	  	`actiecodefeed` text COLLATE utf8_unicode_ci NOT NULL,
	  	`actioncode_date` datetime NOT NULL,
	  	`actioncode_status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	  	`api` tinyint(1) NOT NULL,
	  	`urlreplacer` tinyint(1) NOT NULL DEFAULT '1',
	  	`subid` varchar(43) COLLATE utf8_unicode_ci DEFAULT NULL,
	  	`feed_timeout` int(11) NOT NULL DEFAULT '10',
	  PRIMARY KEY (`daisycon_id`),
	  UNIQUE KEY `daisycon_id` (`daisycon_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$query_publisher = mysql_query($sql_publisher);

// Update for publisher table for older versions
$sql_publisher_update = "ALTER TABLE `publisher` 
							CHANGE `feed` `feed` TEXT,
							CHANGE `programsproductfeed` `programsproductfeed` TEXT,
							CHANGE `actiecodefeed` `actiecodefeed` TEXT";

$query_publisher_update = mysql_query($sql_publisher_update);

// Second update for publisher table for older versions
$sql_publisher1_update = "
ALTER TABLE publisher
  ADD COLUMN (feed_timeout int(11));";

$query_publisher1_update = mysql_query($sql_publisher1_update);

// Third update to insert the default feed_timeout
$publisher = $wpdb->get_row("SELECT * FROM publisher");

if($publisher->feed_timeout == NULL){
	$wpdb->update('publisher', array('feed_timeout' => '10'), array('daisycon_id' => '1'));
}


// Programs table
$sql_programs = "
	CREATE TABLE IF NOT EXISTS `programs` (
		`program_id` int(11) NOT NULL,
		`daisycon_program_id` int(11) NOT NULL,
		`name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`description` text COLLATE utf8_unicode_ci NOT NULL,
		`category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`language` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
		`url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`more` text COLLATE utf8_unicode_ci NOT NULL,
		`productfeed` text COLLATE utf8_unicode_ci NOT NULL,
		`productfeed_date` date NOT NULL,
		`product_count` int(11) NOT NULL,
		`image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`date` date NOT NULL,
		`ecpc` decimal(20,14) NOT NULL,
		`visible` tinyint(1) NOT NULL,
		`subid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	  UNIQUE KEY `program_id` (`program_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$query_feeds = mysql_query($sql_programs);

// Products table
$sql_products = "
	CREATE TABLE IF NOT EXISTS `productfeed` (
		`product_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`program_id` int(11) NOT NULL,
		`title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`description` text COLLATE utf8_unicode_ci NOT NULL,
		`image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`price` DECIMAL( 10, 2 ) COLLATE utf8_unicode_ci NOT NULL,
		`link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`sub_category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	  UNIQUE KEY `product_id` (`product_id`),
	  UNIQUE KEY `product_id_2` (`product_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$query_products = mysql_query($sql_products);

// Products update table for older versions
$sql_products_update = "ALTER TABLE `productfeed` 
							CHANGE `price` `price` DECIMAL( 10, 2 ) NOT NULL";

$query_products_update = mysql_query($sql_products_update);

// Stylesheets table
$sql_stylesheets = "
	CREATE TABLE IF NOT EXISTS `stylesheets` (
		`stylesheet_id` int(11) NOT NULL AUTO_INCREMENT,
		`name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
		`bordercolor` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		`backgroundcolor` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		`textcolor` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		`align` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
		`store` tinyint(1) NOT NULL,
		`store_before` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
		`store_button_program` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		`price` tinyint(1) NOT NULL,
		`price_before` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
		`size` int(3) NOT NULL,
		`width` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`height` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`button_store` tinyint(1) NOT NULL,
		`buttoncolor` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		`buttonbordercolor` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		`buttontextcolor` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		`view` varchar(50) COLLATE utf8_unicode_ci NOT NULL,	
		`moreproducts_color` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		`moreproducts_font` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		`moreproducts_text` varchar(100) COLLATE utf8_unicode_ci NOT NULL,	
		`float` tinyint(1) NOT NULL,
		`price_button` tinyint(1) NOT NULL,
	  PRIMARY KEY (`stylesheet_id`),
	  UNIQUE KEY `stylesheet_id` (`stylesheet_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$query_stylesheets = mysql_query($sql_stylesheets);

// Stylesheet update table for older versions
$sql_stylesheets_update = "
ALTER TABLE stylesheets
  ADD COLUMN (moreproducts_color varchar(20),
       moreproducts_font varchar(20),
       moreproducts_text varchar(100));";

$query_stylesheets_update = mysql_query($sql_stylesheets_update);

// Second update to insert the default values for moreproducts_*
$stylesheets = $wpdb->get_row("SELECT * FROM stylesheets");

if($stylesheets->moreproducts_color == NULL){
	$wpdb->update('stylesheets', array(	'moreproducts_color' => '000000',
										'moreproducts_font' => 'FFFFFF',
										'moreproducts_text' => 'Klik hier om meer producten te laden'), array('stylesheet_id' => '1'));
	
	$wpdb->update('stylesheets', array(	'moreproducts_color' => '000000',
										'moreproducts_font' => 'FFFFFF',
										'moreproducts_text' => 'Klik hier om meer producten te laden'), array('stylesheet_id' => '2'));
}



// Insert two default stylesheets to the stylesheets table
$sql_stylesheets_insert = "
	INSERT IGNORE INTO `stylesheets` (`stylesheet_id`, `name`, `bordercolor`, `backgroundcolor`, `textcolor`, `align`, `store`, `store_before`, `store_button_program`, `price`, `price_before`, `size`, `width`, `height`, `button_store`, `buttoncolor`, `buttonbordercolor`, `buttontextcolor`, `view`, `float`, `price_button`, `moreproducts_color`, `moreproducts_font`, `moreproducts_text`) VALUES
		(1, 'Tabelweergave (voorbeeld 1)', 'E3E3E3', 'FFFFFF', '000000', 'left', 1, '', 'before', 1, 'Prijs: ', 70, '400', '150', 0, 'E86400', 'FFFFFF', 'FFFFFF', '0', '0', '1', '000000', 'FFFFFF', 'Klik hier om meer producten te laden'),
		(2, 'Tegelweergave (voorbeeld 2)', 'CEC9D1', 'FFFFFF', '000000', 'center', 1, '', 'before', 1, '', 50, '100', '300', 0, 'FA8F02', '332B1F', 'FFFFFF', '1', '1', '1', '000000', 'FFFFFF', 'Klik hier om meer producten te laden');";

$query_stylesheets_insert = mysql_query($sql_stylesheets_insert);

// Actioncodes table
$sql_actioncodes = "
	CREATE TABLE IF NOT EXISTS `actioncodes` (
		`actioncode_id` int(11) NOT NULL AUTO_INCREMENT,
		`program_id` int(11) NOT NULL,
		`actioncode_title` varchar(255) NOT NULL,
		`actioncode_description` text NOT NULL,
		`actioncode` varchar(255) NOT NULL,
		`date_start` date NOT NULL,
		`date_end` date NOT NULL,
		`actioncode_link` varchar(255) NOT NULL,
		`actioncode_lan` varchar(10) NOT NULL,
	  PRIMARY KEY (`actioncode_id`),
	  UNIQUE KEY `actiecode_id` (`actioncode_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;";

$query_actioncodes = mysql_query($sql_actioncodes);
?>