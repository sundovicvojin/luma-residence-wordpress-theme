<?php
/**
 * Template Name: Kontakt
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
get_template_part( 'template-parts/contact', null, array( 'tag' => 'h1' ) );
get_footer();
