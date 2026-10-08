<?php
/**
 * Slanje pošte: SMTP bez plugina i beleženje grešaka.
 *
 * Kredencijali se definišu u wp-config.php (nikad u temi):
 * define( 'LUMA_SMTP_HOST', 'mail.kesoinvest.com' );
 * define( 'LUMA_SMTP_USER', 'info@kesoinvest.com' );
 * define( 'LUMA_SMTP_PASS', '...' );
 * Bez ovih konstanti koristi se podrazumevani wp_mail().
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adresa pošiljaoca: nalog sa domena sajta, nikad adresa posetioca.
 */
function luma_mail_from(): string {
	if ( defined( 'LUMA_SMTP_USER' ) && is_email( LUMA_SMTP_USER ) ) {
		return LUMA_SMTP_USER;
	}
	return 'info@kesoinvest.com';
}

add_filter( 'wp_mail_from', 'luma_mail_from' );
add_filter( 'wp_mail_from_name', static fn(): string => 'Keso Invest' );

/**
 * SMTP preko SSL-a na portu 465.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer Instanca koju priprema wp_mail().
 */
function luma_phpmailer_init( $phpmailer ): void {
	if ( ! defined( 'LUMA_SMTP_HOST' ) || ! defined( 'LUMA_SMTP_USER' ) || ! defined( 'LUMA_SMTP_PASS' ) ) {
		return;
	}
	$phpmailer->isSMTP();
	$phpmailer->Host       = LUMA_SMTP_HOST;
	$phpmailer->Port       = 465;
	$phpmailer->SMTPSecure = 'ssl';
	$phpmailer->SMTPAuth   = true;
	$phpmailer->Username   = LUMA_SMTP_USER;
	$phpmailer->Password   = LUMA_SMTP_PASS;
	$phpmailer->Sender     = luma_mail_from();
}
add_action( 'phpmailer_init', 'luma_phpmailer_init' );

/**
 * Razlog neuspeha ide u log, samo kad je WP_DEBUG_LOG uključen.
 *
 * @param WP_Error $error Greška iz wp_mail().
 */
function luma_mail_failed( $error ): void {
	if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
		error_log( 'Luma wp_mail greška: ' . $error->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
}
add_action( 'wp_mail_failed', 'luma_mail_failed' );
