<?php
/*
Plugin Name:  WooCommerce Orders Export
Description:  Export WooCommerce ordes to pdf. Created using WooCommerce REST API
Author:       Aman Jamwal
Version:      1.0
Text Domain:  myplugin
Domain Path:  /languages
License:      GPL v2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.txt
*/



// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

// WooCommerce client
use Automattic\WooCommerce\Client;

// enqueue admin style
function myplugin_enqueue_style_admin() {
	
	$src = plugin_dir_url( __FILE__ ) .'admin/css/myplugin-admin-styles.css';
	wp_enqueue_style( 'myplugin-admin', $src, array(), null, 'all' );

}
add_action( 'admin_enqueue_scripts', 'myplugin_enqueue_style_admin' );


// enqueue admin script
function myplugin_enqueue_script_admin() {
	
	$src = plugin_dir_url( __FILE__ ) .'admin/js/export-to-pdf.js';
	// include the export-to-pdf.js file
	wp_enqueue_script( 'myplugin-admin-script', $src, array('jquery'), null, true );
	// WPurl and WPtitle are used in export-to-pdf.js file
	wp_localize_script( 'myplugin-admin-script', 'WPsettings', array(
		'WPurl' => esc_url(home_url()),
		'WPtitle' => get_bloginfo('name')
	));

}
add_action( 'admin_enqueue_scripts', 'myplugin_enqueue_script_admin' );


// include plugin dependencies

// consumer key and consumer secret
include plugin_dir_path( __FILE__ ) . 'admin/ck-cs.php';

require_once plugin_dir_path( __FILE__ ) . 'admin/wc_restapi_client/vendor/autoload.php';

// admin-menu.php file decides where the plugin appears (if its a sublevel menu or a top level)
require_once plugin_dir_path( __FILE__ ) . 'admin/admin-menu.php';

// settings-page.php file is the main page where the user can view the table
require_once plugin_dir_path( __FILE__ ) . 'admin/settings-page.php';
