<?php
/**
 * Kontakt sekcija: podaci i forma. Argument "tag" je h1 ili h2.
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$luma_tag = ( isset( $args['tag'] ) && 'h1' === $args['tag'] ) ? 'h1' : 'h2';
?>
<section class="section contact" id="kontakt" aria-labelledby="kontakt-h">
	<div class="wrap contact__grid">
		<div class="contact__info">
			<<?php echo esc_html( $luma_tag ); ?> id="kontakt-h" class="rv"><?php echo esc_html( 'h1' === $luma_tag ? 'Kontakt' : luma_opt( 'kontakt_naslov' ) ); ?></<?php echo esc_html( $luma_tag ); ?>>
			<p class="lead rv">Pošaljite upit ili nas pozovite. Odgovaramo u najkraćem roku.</p>
			<dl class="contact__list rv">
				<div><dt>Adresa</dt><dd><?php echo esc_html( luma_opt( 'investitor' ) ); ?>, <?php echo esc_html( luma_opt( 'adresa' ) ); ?></dd></div>
				<div><dt>Telefon</dt><dd><a href="<?php echo esc_attr( luma_tel( luma_opt( 'telefon_1' ) ) ); ?>"><?php echo esc_html( luma_opt( 'telefon_1' ) ); ?></a><br><a href="<?php echo esc_attr( luma_tel( luma_opt( 'telefon_2' ) ) ); ?>"><?php echo esc_html( luma_opt( 'telefon_2' ) ); ?></a></dd></div>
				<div><dt>Email</dt><dd><a href="mailto:<?php echo esc_attr( luma_opt( 'email' ) ); ?>"><?php echo esc_html( luma_opt( 'email' ) ); ?></a></dd></div>
			</dl>
		</div>
		<div class="contact__form rv">
			<?php luma_contact_form( 'k' ); ?>
		</div>
	</div>
</section>
