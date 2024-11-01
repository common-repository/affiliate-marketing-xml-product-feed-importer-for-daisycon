<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();
	
	global $wpdb;
	
	// Define all the different database tables
	$dc_categories = "categories";
	$dc_publisher = "publisher";
	$dc_programs = "programs";
	$dc_productfeed = "productfeed";
	$dc_stylesheets = "stylesheets";
	$dc_actioncodes = "actioncodes";
	
	// Drop tables
	$wpdb->query("DROP TABLE IF EXISTS $dc_categories");
	$wpdb->query("DROP TABLE IF EXISTS $dc_publisher");
	$wpdb->query("DROP TABLE IF EXISTS $dc_productfeed");
	$wpdb->query("DROP TABLE IF EXISTS $dc_stylesheets");
	$wpdb->query("DROP TABLE IF EXISTS $dc_actioncodes");
	$wpdb->query("DROP TABLE IF EXISTS $dc_programs");
?>