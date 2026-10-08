<?php
/**
 * Osnovna podešavanja teme i čišćenje WordPress dodataka.
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	static function (): void {
		load_theme_textdomain( 'luma-residence', get_theme_file_path( 'languages' ) );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
		register_nav_menus( array( 'primary' => 'Glavni meni' ) );
	}
);

/* Jezik dokumenta: srpski, latinica. */
add_filter(
	'language_attributes',
	static function ( string $output ): string {
		if ( is_admin() ) {
			return $output;
		}
		$output = preg_replace( '/lang="[^"]*"/', 'lang="sr-Latn"', $output, 1, $count );
		return $count ? $output : $output . ' lang="sr-Latn"';
	}
);

/* Jače čuvanje kvaliteta osnova (tekst na planovima mora ostati čitljiv). */
add_filter(
	'wp_editor_set_quality',
	static function ( int $quality, string $mime ): int {
		return in_array( $mime, array( 'image/webp', 'image/jpeg', 'image/avif' ), true ) ? 90 : $quality;
	},
	10,
	2
);

/* Čist wp_head: bez emoji skripte, wp-embed i nepotrebnih link oznaka. */
add_action(
	'init',
	static function (): void {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		remove_action( 'wp_head', 'rest_output_link_wp_head' );
		remove_action( 'wp_head', 'feed_links_extra', 3 );
		remove_action( 'wp_head', 'feed_links', 2 );
	}
);
add_filter( 'emoji_svg_url', '__return_false' );
add_filter( 'tiny_mce_plugins', static fn( $plugins ) => is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array() );

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		// Tema ne koristi blokove na front-endu; editor u administraciji ostaje netaknut.
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_script( 'wp-embed' );
	},
	100
);
add_action(
	'wp_footer',
	static function (): void {
		wp_dequeue_script( 'wp-embed' );
	},
	1
);

/* Bez komentara i RSS-a: sajt ih ne koristi. */
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );

/**
 * Meni: ako vlasnik nije napravio meni, prikazuje se osnovni meni.
 */
function luma_primary_nav(): void {
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'nav__list',
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		return;
	}
	echo '<ul class="nav__list">';
	$items = array(
		array( 'Početna', home_url( '/' ), is_front_page(), false ),
		array( 'O nama', luma_page_url( 'o-nama' ), is_page( 'o-nama' ), false ),
		array( 'Prodaja stanova', luma_page_url( 'prodaja-stanova' ), is_page( 'prodaja-stanova' ), false ),
		array( 'Keso Invest', luma_opt( 'keso_url' ), false, true ),
	);
	foreach ( $items as $item ) {
		printf(
			'<li><a href="%s"%s%s>%s</a></li>',
			esc_url( $item[1] ),
			$item[2] ? ' aria-current="page"' : '', // phpcs:ignore WordPress.Security.EscapeOutput
			$item[3] ? ' target="_blank" rel="noopener"' : '', // phpcs:ignore WordPress.Security.EscapeOutput
			esc_html( $item[0] )
		);
	}
	echo '</ul>';
}

/**
 * URL stranice po slug-u (sa rezervom ako stranica još ne postoji).
 */
function luma_page_url( string $slug ): string {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

/**
 * Broj sa atributom za brojač (ako je vrednost čist broj).
 */
function luma_num( string $value ): string {
	$value = trim( $value );
	if ( '' !== $value && ctype_digit( $value ) ) {
		return '<span class="num" data-count="' . esc_attr( $value ) . '">' . esc_html( $value ) . '</span>';
	}
	return '<span class="num">' . esc_html( $value ) . '</span>';
}

/**
 * Pasusi iz običnog teksta.
 */
function luma_paragraphs( string $text ): string {
	return wpautop( esc_html( $text ) );
}
