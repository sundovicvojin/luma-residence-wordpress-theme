<?php
/**
 * Slike iz teme: <picture> sa AVIF/WebP i JPEG rezervom, srcset iz manifest.json.
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manifest slika (ključ => širina, visina, dostupne širine).
 */
function luma_img_manifest(): array {
	static $manifest = null;
	if ( null === $manifest ) {
		$file     = get_theme_file_path( 'assets/img/manifest.json' );
		$json     = is_readable( $file ) ? file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions
		$manifest = $json ? json_decode( $json, true ) : array();
		$manifest = is_array( $manifest ) ? $manifest : array();
	}
	return $manifest;
}

/**
 * Ispisuje <picture> za sliku iz teme.
 *
 * @param string $key  Ključ iz manifesta.
 * @param string $alt  Alternativni tekst ('' za dekorativne slike).
 * @param array  $args sizes, class, priority (LCP), decoding.
 */
function luma_picture( string $key, string $alt, array $args = array() ): void {
	$man = luma_img_manifest();
	if ( empty( $man[ $key ] ) ) {
		// Slika nije dostupna: jasno označen prazan prostor, bez izmišljanja slike.
		echo '<span class="img-missing" role="img" aria-label="' . esc_attr( $alt ) . '">Slika nije dostupna</span>';
		return;
	}
	$m        = $man[ $key ];
	$widths   = array_keys( $m['widths'] );
	$max      = (int) max( $widths );
	$mid      = (int) $widths[ (int) floor( ( count( $widths ) - 1 ) / 2 + 0.5 ) ];
	$sizes    = $args['sizes'] ?? '100vw';
	$priority = ! empty( $args['priority'] );
	$class    = isset( $args['class'] ) ? ' class="' . esc_attr( $args['class'] ) . '"' : '';
	$base     = get_theme_file_uri( 'assets/img/' . $key . '-' );
	$srcset   = static function ( string $ext ) use ( $widths, $base ): string {
		$parts = array();
		foreach ( $widths as $w ) {
			$parts[] = $base . $w . '.' . $ext . ' ' . $w . 'w';
		}
		return implode( ', ', $parts );
	};

	echo '<picture>';
	foreach ( array( 'avif' => 'image/avif', 'webp' => 'image/webp' ) as $ext => $mime ) {
		if ( isset( $m['formats'] ) && ! in_array( $ext, $m['formats'], true ) ) {
			continue;
		}
		echo '<source type="' . esc_attr( $mime ) . '" srcset="' . esc_attr( $srcset( $ext ) ) . '" sizes="' . esc_attr( $sizes ) . '">';
	}
	printf(
		'<img%s src="%s" srcset="%s" sizes="%s" width="%d" height="%d" alt="%s" %s>',
		$class, // phpcs:ignore WordPress.Security.EscapeOutput
		esc_url( $base . $mid . '.jpg' ),
		esc_attr( $srcset( 'jpg' ) ),
		esc_attr( $sizes ),
		$max,
		(int) $m['widths'][ $max ],
		esc_attr( $alt ),
		$priority ? 'fetchpriority="high" decoding="async"' : 'loading="lazy" decoding="async"' // phpcs:ignore WordPress.Security.EscapeOutput
	);
	echo '</picture>';
}

/**
 * Putanja do slike u temi (npr. za Open Graph).
 */
function luma_img_url( string $file ): string {
	return get_theme_file_uri( 'assets/img/' . ltrim( $file, '/' ) );
}
