<?php
/**
 * Luma Residence tema: učitavanje modula.
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LUMA_VERSION', '1.1.0' );

foreach ( array( 'options', 'setup', 'images', 'apartments', 'enqueue', 'seo', 'mail', 'kontakt', 'seed' ) as $luma_module ) {
	require_once get_theme_file_path( 'inc/' . $luma_module . '.php' );
}
unset( $luma_module );
