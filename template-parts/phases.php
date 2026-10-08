<?php
/**
 * Faze izgradnje.
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="section phases" aria-labelledby="faze">
	<div class="wrap phases__grid">
		<div class="phases__head">
			<h2 id="faze" class="rv" data-mask>Dve faze izgradnje</h2>
			<p class="rv"><?php echo esc_html( luma_opt( 'o_tekst_2' ) ); ?> Status: <strong><?php echo esc_html( luma_opt( 'status' ) ); ?></strong>.</p>
		</div>
		<ol class="phases__list">
			<li class="phase rv">
				<span class="phase__tag">I faza</span>
				<h3><?php echo esc_html( luma_opt( 'faza_1_naziv' ) ); ?></h3>
				<p><?php echo esc_html( luma_opt( 'faza_1_status' ) ); ?></p>
			</li>
			<li class="phase rv">
				<span class="phase__tag">II faza</span>
				<h3><?php echo esc_html( luma_opt( 'faza_2_naziv' ) ); ?></h3>
				<p><?php echo esc_html( luma_opt( 'faza_2_status' ) ); ?></p>
			</li>
		</ol>
	</div>
</section>
