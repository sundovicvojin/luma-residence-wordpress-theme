<?php
/**
 * Template Name: Prodaja stanova
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
get_template_part( 'template-parts/sale', null, array( 'tag' => 'h1' ) );
?>
<section class="infobook infobook--slim" aria-label="Dalje informacije">
	<div class="wrap infobook__slim">
		<p class="rv">Sve osnove i tehnički podaci nalaze se u infobooku projekta.</p>
		<p class="actions rv">
			<a class="btn btn--primary" href="<?php echo esc_url( luma_opt( 'infobook' ) ); ?>" download="Luma-Residence-Infobook.pdf">Preuzmi infobook</a>
			<a class="btn btn--ghost" href="<?php echo esc_url( luma_page_url( 'kontakt' ) ); ?>">Pošaljite upit</a>
		</p>
	</div>
</section>
<?php
get_footer();
