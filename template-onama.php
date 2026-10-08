<?php
/**
 * Template Name: O nama
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<div class="wrap page-head">
	<h1 class="rv" data-mask>O nama</h1>
	<p class="lead rv">Luma Residence gradi <?php echo esc_html( luma_opt( 'investitor' ) ); ?> iz Novog Sada.</p>
</div>
<?php
get_template_part( 'template-parts/phases' );
get_template_part( 'template-parts/location' );
?>
<section class="section investor" aria-labelledby="investitor-h">
	<div class="wrap investor__grid">
		<h2 id="investitor-h" class="rv" data-mask>Investitor</h2>
		<div class="investor__body rv">
			<p class="investor__name"><?php echo esc_html( luma_opt( 'investitor' ) ); ?></p>
			<p><?php echo esc_html( luma_opt( 'adresa' ) ); ?></p>
			<p>
				<a href="<?php echo esc_attr( luma_tel( luma_opt( 'telefon_1' ) ) ); ?>"><?php echo esc_html( luma_opt( 'telefon_1' ) ); ?></a><br>
				<a href="<?php echo esc_attr( luma_tel( luma_opt( 'telefon_2' ) ) ); ?>"><?php echo esc_html( luma_opt( 'telefon_2' ) ); ?></a><br>
				<a href="mailto:<?php echo esc_attr( luma_opt( 'email' ) ); ?>"><?php echo esc_html( luma_opt( 'email' ) ); ?></a>
			</p>
			<p class="actions"><a class="btn btn--ghost" href="<?php echo esc_url( luma_opt( 'keso_url' ) ); ?>" target="_blank" rel="noopener">Sajt Keso Invest</a></p>
		</div>
	</div>
</section>
<?php
get_footer();
