<?php
/**
 * CSS i JS. Osnovni JS (nivo 0 i 1) je mali; GSAP se učitava dinamički, samo na nivou 2.
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Verzija fajla po vremenu izmene (keširanje).
 */
function luma_asset_ver( string $rel ): string {
	$path = get_theme_file_path( $rel );
	return is_readable( $path ) ? (string) filemtime( $path ) : LUMA_VERSION;
}

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		wp_enqueue_style( 'luma-main', get_theme_file_uri( 'assets/css/main.css' ), array(), luma_asset_ver( 'assets/css/main.css' ) );

		wp_register_script( 'luma-core', get_theme_file_uri( 'assets/js/core.js' ), array(), luma_asset_ver( 'assets/js/core.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
		wp_add_inline_script(
			'luma-core',
			'window.LUMA=' . wp_json_encode(
				array(
					'url' => trailingslashit( get_theme_file_uri( 'assets/js' ) ),
					'v'   => luma_asset_ver( 'assets/js/motion.js' ),
				)
			) . ';',
			'before'
		);
		wp_enqueue_script( 'luma-core' );
	}
);

/* Font: jedan woff2 na kritičnom putu. */
add_action(
	'wp_head',
	static function (): void {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( get_theme_file_uri( 'assets/fonts/geist-latin.woff2' ) )
		);
		// Skriveno stanje animacija sme da postoji samo ako JS radi; bez core.js u roku od 4 s klase se skidaju.
		// "l2-pending" se postavlja samo kad uređaj prolazi iste provere kao nivo 2 (vidi core.js).
		echo "<script>(function(d,w){var h=d.documentElement,n=navigator,m=function(q){return w.matchMedia(q).matches};h.classList.add('js');if('IntersectionObserver' in w){h.classList.add('anim-ready');if(m('(min-width:1024px)')&&m('(hover:hover) and (pointer:fine)')&&!m('(prefers-reduced-motion:reduce)')&&!(n.connection&&n.connection.saveData)&&(n.deviceMemory===undefined||n.deviceMemory>=4)&&(n.hardwareConcurrency===undefined||n.hardwareConcurrency>=4)){h.classList.add('l2-pending')}setTimeout(function(){if(!w.__lumaOK)h.classList.remove('anim-ready');h.classList.remove('l2-pending')},4500)}})(document,window)</script>\n";
	},
	1
);

/* Preload LCP slike (hero) na početnoj. */
add_action(
	'wp_head',
	static function (): void {
		if ( ! is_front_page() ) {
			return;
		}
		$m = luma_img_manifest();
		if ( empty( $m['hero'] ) ) {
			return;
		}
		$base = get_theme_file_uri( 'assets/img/hero-' );
		$set  = array();
		foreach ( array_keys( $m['hero']['widths'] ) as $w ) {
			$set[] = $base . $w . '.avif ' . $w . 'w';
		}
		printf(
			'<link rel="preload" as="image" type="image/avif" imagesrcset="%s" imagesizes="100vw" fetchpriority="high">' . "\n",
			esc_attr( implode( ', ', $set ) )
		);
	},
	2
);
