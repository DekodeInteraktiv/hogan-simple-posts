<?php
/**
 * Plugin Name: Hogan Module: Simple Posts
 * Plugin URI: https://github.com/dekodeinteraktiv/hogan-simple-posts
 * GitHub Plugin URI: https://github.com/dekodeinteraktiv/hogan-simple-posts
 * Description: Simple Posts List Module for Hogan
 * Version: 1.0.6
 * Author: Dekode
 * Author URI: https://dekode.no
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * Text Domain: hogan-simple-posts
 * Domain Path: /languages/
 *
 * @package Hogan
 * @author Dekode
 */

declare( strict_types = 1 );
namespace Dekode\Hogan\Simple_Posts;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\hogan_load_textdomain' );
add_action( 'hogan/include_modules', __NAMESPACE__ . '\\hogan_register_module' );

/**
 * Register module text domain
 *
 * @return void
 */
function hogan_load_textdomain() {
	\load_plugin_textdomain( 'hogan-simple-posts', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Register module in Hogan
 *
 * @return void
 */
function hogan_register_module() {
	require_once 'class-simple-posts.php';
	\hogan_register_module( new \Dekode\Hogan\Simple_Posts() );
}
