<?php
/*
 * Plugin Name: LSX FAQ
 * Plugin URI:  https://www.lsdev.biz/
 * Description: Adds a FAQs post type 
 * Version:     1.0.0
 * Author:      LightSpeed
 * Author URI:  https://www.lsdev.biz/
 * License:     GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wc-product-download-dates
 * Domain Path: /languages
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'LSX_FAQ_PATH', plugin_dir_path( __FILE__ ) );
define( 'LSX_FAQ_CORE', __FILE__ );
define( 'LSX_FAQ_URL', plugin_dir_url( __FILE__ ) );
define( 'LSX_FAQ_VER', '1.0.0' );

require_once( LSX_FAQ_PATH . 'classes/class-lsx-faq.php' );

$lsx_faq = lsx_faq();
function lsx_faq() {
	return \lsx\LSX_FAQ::init();
}
