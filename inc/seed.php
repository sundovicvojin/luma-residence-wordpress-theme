<?php
/**
 * Početni sadržaj pri aktivaciji: stranice sajta (jednom po verziji teme).
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pravi stranicu ako ne postoji; vraća ID.
 */
function luma_seed_page( string $slug, string $title, string $template = '' ): int {
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		return (int) $existing->ID;
	}
	$id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => '',
		)
	);
	if ( $id && ! is_wp_error( $id ) && $template ) {
		update_post_meta( $id, '_wp_page_template', $template );
	}
	return (int) $id;
}

/**
 * Glavna funkcija: bezbedna za ponavljanje.
 */
function luma_seed(): void {
	$home = luma_seed_page( 'pocetna', 'Početna' );
	luma_seed_page( 'o-nama', 'O nama', 'template-onama.php' );
	luma_seed_page( 'prodaja-stanova', 'Prodaja stanova', 'template-prodaja.php' );
	luma_seed_page( 'kontakt', 'Kontakt', 'template-kontakt.php' );

	if ( $home && 'posts' === get_option( 'show_on_front' ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home );
	}
	if ( ! get_option( 'permalink_structure' ) ) {
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
	}
	flush_rewrite_rules( false );
	update_option( 'luma_seeded', LUMA_VERSION, false );
}

add_action(
	'after_switch_theme',
	static function (): void {
		if ( get_option( 'luma_seeded' ) !== LUMA_VERSION ) {
			luma_seed();
		}
	}
);

/* Nadogradnja teme na novu verziju: stranice se dodaju jednom, pri prvom učitavanju sajta (ne samo u administraciji). */
add_action(
	'wp_loaded',
	static function (): void {
		$seeded = get_option( 'luma_seeded' );
		if ( $seeded && LUMA_VERSION !== $seeded && ! wp_doing_ajax() && ! wp_doing_cron() ) {
			luma_seed();
		}
	}
);
