<?php
/*
Plugin Name: Daisycon affiliate marketing plugin
Plugin URI: http://www.daisycon.com/nl/blog/wordpress_affiliate_plugin_productfeeds/
Description: The Daisycon affiliate marketing XML product feed importer is a plugin that helps non-technical affiliates to use Daisycon XML product feeds. Daisycon is active as an affiliate network in the Netherlands, Germany, Belgium and France. To use this plugin you must subscribe as affiliate at Daisycon. This plugin is available in Dutch, English, French and German. 
Author: Daisycon
Version: 2.5
Author URI: http://www.daisycon.com
*/

// Turn of all error reporting in the plugin
error_reporting(0);

// Required files
require_once('database.php');
require_once('core.php');
require_once('general.php');

// Required widgets
require_once('widgets/topPrograms.php');
require_once('widgets/randomPrograms.php');
require_once('widgets/newPrograms.php');
require_once('widgets/listProducts.php');

$general = new general;
$admin = new admin; 

// Create menu
function menu(){
	add_menu_page('Daisycon', 'Daisycon', 'manage_options', 'Daisycon', array('admin', 'adminFeeds'), plugin_dir_url( __FILE__ ) . 'files/images/icon.png');
		add_submenu_page('Daisycon', __('Instellingen','DaisyconPlugin'), __('Instellingen','DaisyconPlugin'), 'manage_options', 'Daisycon', 'manage_options');
		add_submenu_page('Daisycon', __('Programma&lsquo;s','DaisyconPlugin'),  __('Programma&lsquo;s','DaisyconPlugin'), 'manage_options', 'programs', array('admin', 'adminProgram'));
		add_submenu_page('Daisycon',  __('Producten','DaisyconPlugin'),  __('Producten','DaisyconPlugin'), 'manage_options', 'producten', array('admin', 'adminProduct'));
		add_submenu_page('Daisycon',  __('Stylesheets','DaisyconPlugin'),  __('Stylesheets','DaisyconPlugin'), 'manage_options', 'stylesheets', array('admin', 'adminStylesheets'));
		add_submenu_page('Daisycon',  __('Actiecodes','DaisyconPlugin'),  __('Actiecodes','DaisyconPlugin'), 'manage_options', 'actiecodes', array('admin', 'adminActiecodes'));
		add_submenu_page('Daisycon',  __('Categorie&euml;n','DaisyconPlugin'),  __('Categorie&euml;n','DaisyconPlugin'), 'manage_options', 'categorie', array('admin', 'adminCategorie'));
}

add_action('admin_menu', 'menu');

// Add shortcodes
add_shortcode('programs', array('general', 'programs')); 
add_shortcode('category', array('general', 'category')); 
add_shortcode('newest', array('general', 'newPrograms'));
add_shortcode('provider', array('general', 'searchProvider')); 
add_shortcode('populair', array('general', 'topPrograms')); 
add_shortcode('moreProgram', array('general', 'moreProgram')); 
add_shortcode('program', array('general', 'program'));
add_shortcode('products', array('general', 'products'));
add_shortcode('actioncodes', array('general', 'actioncodes'));
add_shortcode('actioncodesProgram', array('general', 'actioncodesProgram'));
add_shortcode('actioncode', array('general', 'actioncode'));
add_shortcode('productsearch ', array('general', 'searchProvider'));

// New shortcodes
add_shortcode('daisycon_products', array('general', 'products'));
add_shortcode('daisycon_actioncode', array('general', 'actioncode'));

// Add Stylesheet
wp_enqueue_style('stylesheet', '/wp-content/plugins/affiliate-marketing-xml-product-feed-importer-for-daisycon/files/css/style.css');

// Add jQuery
wp_enqueue_script("jquery");

// Languages
load_plugin_textdomain( 'DaisyconPlugin', false, dirname( plugin_basename( __FILE__ ) ) . '/files/language/' );

// Add widgets
add_action( 'widgets_init', create_function('', 'return register_widget("TopVifeWidget");') );
add_action( 'widgets_init', create_function('', 'return register_widget("NewVifeWidget");') );
add_action( 'widgets_init', create_function('', 'return register_widget("RandomVifeWidget");') );
add_action( 'widgets_init', create_function('', 'return register_widget("Products");') );

// Add linkreplacer if subid is set to 1	
global $wpdb;
$publisher = $wpdb->get_row("SELECT * FROM publisher");
if($publisher->subid == 1){
	$wi = explode('/media/', $publisher->feed);
	$wi = explode('/', $wi[1]);
	
	wp_enqueue_script('linkreplacer', 'http://tools.daisycon.com/jsreplace/?wi='.$wi[0].'&ws=dai_wp_linkreplacer');	
}
?>