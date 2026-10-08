<?php
/**
 * Prodaja stanova: broj slobodnih i prodatih stanova (iz aplikacije) i poziv na aplikaciju.
 * Argumenti: tag (h1 ili h2), pin (nivo 2: sekcija se pinuje).
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$luma_tag   = ( isset( $args['tag'] ) && 'h1' === $args['tag'] ) ? 'h1' : 'h2';
$luma_pin   = ! empty( $args['pin'] );
$luma_stats = luma_apartment_stats();
?>
<section class="sale<?php echo 'h1' === $luma_tag ? ' sale--page' : ''; ?>"<?php echo $luma_pin ? ' data-pin' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?> aria-labelledby="prodaja-h">
	<div class="sale__media" data-l2>
		<?php luma_picture( 'hero', '', array( 'sizes' => '100vw', 'class' => 'sale__img' ) ); ?>
	</div>
	<div class="wrap sale__copy">
		<<?php echo esc_html( $luma_tag ); ?> id="prodaja-h" class="rv" data-mask>Prodaja stanova</<?php echo esc_html( $luma_tag ); ?>>
		<p class="lead rv"><?php echo esc_html( luma_opt( 'prodaja_tekst' ) ); ?></p>
		<?php if ( $luma_stats ) : ?>
			<dl class="sale__stats rv">
				<div>
					<dt>Slobodno</dt>
					<dd><span class="num" data-count="<?php echo (int) $luma_stats['available']; ?>"><?php echo (int) $luma_stats['available']; ?></span></dd>
				</div>
				<div>
					<dt>Prodato</dt>
					<dd><span class="num" data-count="<?php echo (int) $luma_stats['sold']; ?>"><?php echo (int) $luma_stats['sold']; ?></span></dd>
				</div>
				<?php if ( $luma_stats['reserved'] > 0 ) : ?>
					<div>
						<dt>Rezervisano</dt>
						<dd><span class="num" data-count="<?php echo (int) $luma_stats['reserved']; ?>"><?php echo (int) $luma_stats['reserved']; ?></span></dd>
					</div>
				<?php endif; ?>
			</dl>
			<p class="sale__total rv">Ukupno <?php echo (int) $luma_stats['total']; ?> stanova u ponudi.</p>
		<?php else : ?>
			<p class="sale__total rv">Trenutno stanje ponude pogledajte u aplikaciji.</p>
		<?php endif; ?>
		<p class="actions rv">
			<a class="btn btn--primary" href="<?php echo esc_url( luma_opt( 'app_url' ) ); ?>" target="_blank" rel="noopener">Pogledaj stanove</a>
		</p>
	</div>
</section>
