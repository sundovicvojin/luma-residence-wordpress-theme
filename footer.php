<?php
/**
 * Podnožje: investitor, adresa i kontakt.
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>
<footer class="site-footer">
	<div class="wrap">
		<div class="site-footer__grid">
			<div class="site-footer__brand">
				<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Luma Residence, početna strana"><?php get_template_part( 'template-parts/wordmark' ); ?></a>
				<p>Stambeno-poslovni kompleks u Novom Sadu.</p>
			</div>
			<div>
				<h2 class="footer-h">Investitor</h2>
				<p><?php echo esc_html( luma_opt( 'investitor' ) ); ?><br><?php echo esc_html( luma_opt( 'adresa' ) ); ?></p>
			</div>
			<div>
				<h2 class="footer-h">Kontakt</h2>
				<ul class="plain">
					<li><a href="<?php echo esc_attr( luma_tel( luma_opt( 'telefon_1' ) ) ); ?>"><?php echo esc_html( luma_opt( 'telefon_1' ) ); ?></a></li>
					<li><a href="<?php echo esc_attr( luma_tel( luma_opt( 'telefon_2' ) ) ); ?>"><?php echo esc_html( luma_opt( 'telefon_2' ) ); ?></a></li>
					<li><a href="mailto:<?php echo esc_attr( luma_opt( 'email' ) ); ?>"><?php echo esc_html( luma_opt( 'email' ) ); ?></a></li>
				</ul>
			</div>
			<div>
				<h2 class="footer-h">Mapa sajta</h2>
				<ul class="plain">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Početna</a></li>
					<li><a href="<?php echo esc_url( luma_page_url( 'o-nama' ) ); ?>">O nama</a></li>
					<li><a href="<?php echo esc_url( luma_page_url( 'prodaja-stanova' ) ); ?>">Prodaja stanova</a></li>
					<li><a href="<?php echo esc_url( luma_opt( 'keso_url' ) ); ?>" target="_blank" rel="noopener">Keso Invest</a></li>
					<li><a href="<?php echo esc_url( luma_page_url( 'kontakt' ) ); ?>">Kontakt</a></li>
					<li><a href="<?php echo esc_url( luma_opt( 'infobook' ) ); ?>" download="Luma-Residence-Infobook.pdf">Preuzmi infobook</a></li>
				</ul>
			</div>
		</div>
		<p class="site-footer__legal">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( luma_opt( 'investitor' ) ); ?>. Sva prava zadržana. | Created by <a href="https://instagram.com/devbyvojin/" target="_blank" rel="noopener">devbyvojin</a></p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
