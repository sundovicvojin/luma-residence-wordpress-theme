<?php
/**
 * Lokacija: mapa i blizina.
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$luma_near = array();
foreach ( preg_split( '/\R/', luma_opt( 'blizina' ) ) as $luma_line ) {
	$luma_parts = array_map( 'trim', explode( '|', $luma_line, 2 ) );
	if ( '' !== $luma_parts[0] && ! empty( $luma_parts[1] ) ) {
		$luma_near[] = $luma_parts;
	}
}
?>
<section class="section location" aria-labelledby="blizina">
	<div class="wrap">
		<h2 id="blizina" class="rv" data-mask><?php echo esc_html( luma_opt( 'blizina_naslov' ) ); ?></h2>
		<div class="location__grid">
			<figure class="location__map rv" data-l2 data-clip>
				<?php luma_picture( 'lokacija', 'Mapa sa oznakom lokacije Luma Residence na uglu ulica Oblačića Rada i Boška Vrebalova', array( 'sizes' => '(min-width: 900px) 58vw, 92vw' ) ); ?>
				<figcaption><a href="<?php echo esc_url( luma_opt( 'mapa_url' ) ); ?>" target="_blank" rel="noopener">Otvori lokaciju u mapi</a></figcaption>
			</figure>
			<ul class="near">
				<?php foreach ( $luma_near as $luma_i => $luma_n ) : ?>
					<li class="rv" style="--i:<?php echo (int) $luma_i; ?>">
						<span class="near__d"><?php echo esc_html( $luma_n[1] ); ?></span>
						<span class="near__l"><?php echo esc_html( $luma_n[0] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>
