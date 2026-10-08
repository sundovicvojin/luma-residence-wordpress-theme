<?php
/**
 * SEO osnova: naslov, meta opis, Open Graph.
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Naslov dokumenta po stranici (srpski, bez generičkih dodataka).
 */
add_filter(
	'pre_get_document_title',
	static function ( string $title ): string {
		if ( is_front_page() ) {
			return 'Luma Residence | Stanovi i poslovni prostori u Novom Sadu';
		}
		if ( is_404() ) {
			return 'Stranica nije pronađena | Luma Residence';
		}
		if ( is_singular() ) {
			return get_the_title() . ' | Luma Residence';
		}
		return $title;
	}
);

/**
 * Meta opis tekuće stranice.
 */
function luma_meta_description(): string {
	if ( is_front_page() ) {
		return luma_opt( 'meta_opis' );
	}
	if ( is_page() ) {
		if ( has_excerpt() ) {
			return wp_strip_all_tags( get_the_excerpt() );
		}
		switch ( get_page_template_slug() ) {
			case 'template-kontakt.php':
				return 'Kontakt Luma Residence, Novi Sad: telefon, email i adresa investitora KESO INVEST. Zakažite obilazak ili pošaljite upit.';
			case 'template-onama.php':
				return 'O projektu Luma Residence u Novom Sadu: faze izgradnje, lokacija i investitor KESO INVEST doo.';
			case 'template-prodaja.php':
				return 'Prodaja stanova u Luma Residence, Novi Sad: koliko je stanova slobodno, koliko prodato i pregled ponude u aplikaciji.';
		}
	}
	if ( is_404() ) {
		return 'Tražena stranica ne postoji. Vratite se na početnu stranicu Luma Residence.';
	}
	return luma_opt( 'meta_opis' );
}

add_action(
	'wp_head',
	static function (): void {
		$desc  = luma_meta_description();
		$title = wp_get_document_title();
		$url   = is_singular() ? get_permalink() : home_url( '/' );
		$img   = luma_img_url( 'og-image.jpg' );
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
		echo '<meta name="theme-color" content="#233b79">' . "\n";
		echo '<meta property="og:site_name" content="Luma Residence">' . "\n";
		echo '<meta property="og:locale" content="sr_RS">' . "\n";
		echo '<meta property="og:type" content="website">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
		echo '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n";
		echo '<meta property="og:image:width" content="1200"><meta property="og:image:height" content="630">' . "\n";
		echo '<meta property="og:image:alt" content="Render zgrade Luma Residence">' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		echo '<link rel="icon" href="' . esc_url( luma_img_url( 'favicon.svg' ) ) . '" type="image/svg+xml">' . "\n";
	},
	3
);
