<?php
/**
 * Zaglavlje sajta.
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#sadrzaj">Preskoči na sadržaj</a>
<div id="top-sentinel" aria-hidden="true"></div>
<header class="site-header" id="header">
	<div class="wrap site-header__in">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Luma Residence, početna strana">
			<?php get_template_part( 'template-parts/wordmark' ); ?>
		</a>
		<nav class="nav" id="nav" aria-label="Glavni meni">
			<?php luma_primary_nav(); ?>
			<div class="nav__tail">
				<a class="nav__tel" href="<?php echo esc_attr( luma_tel( luma_opt( 'telefon_1' ) ) ); ?>"><?php echo esc_html( luma_opt( 'telefon_1' ) ); ?></a>
				<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/#kontakt' ) ); ?>">Pošaljite upit</a>
			</div>
		</nav>
		<a class="header-call" href="<?php echo esc_attr( luma_tel( luma_opt( 'telefon_1' ) ) ); ?>">Pozovi</a>
		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav">
			<span class="nav-toggle__label">Meni</span>
			<span class="nav-toggle__bars" aria-hidden="true"></span>
		</button>
	</div>
</header>
<main id="sadrzaj" tabindex="-1">
