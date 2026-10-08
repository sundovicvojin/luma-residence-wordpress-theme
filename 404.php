<?php
/**
 * Stranica nije pronađena.
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<div class="wrap page-head">
	<h1>Stranica nije pronađena</h1>
	<p class="lead">Adresa koju tražite ne postoji ili je premeštena.</p>
	<p class="actions">
		<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">Nazad na početnu</a>
		<a class="btn btn--ghost" href="<?php echo esc_url( luma_page_url( 'kontakt' ) ); ?>">Kontakt</a>
	</p>
</div>
<?php
get_footer();
