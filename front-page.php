<?php
/**
 * Početna strana.
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="hero" aria-labelledby="naslov">
	<div class="hero__media" data-l2>
		<?php
		luma_picture(
			'hero',
			'Render zgrade Luma Residence u zalasku sunca: fasada od cigle sa balkonima i belim površinama',
			array(
				'priority' => true,
				'sizes'    => '100vw',
				'class'    => 'hero__img',
			)
		);
		?>
	</div>
	<div class="wrap hero__copy">
		<h1 id="naslov"><?php echo esc_html( luma_opt( 'hero_naslov' ) ); ?></h1>
		<p class="lead"><?php echo esc_html( luma_opt( 'hero_podnaslov' ) ); ?></p>
		<p class="actions">
			<a class="btn btn--primary" href="<?php echo esc_url( luma_opt( 'app_url' ) ); ?>" target="_blank" rel="noopener">Pogledaj stanove</a>
			<a class="btn btn--ghost" href="<?php echo esc_url( luma_opt( 'infobook' ) ); ?>" download="Luma-Residence-Infobook.pdf">Preuzmi infobook</a>
		</p>
	</div>
</section>

<section class="facts section--tight" aria-label="Ponuda u brojevima">
	<div class="wrap">
		<dl class="facts__grid">
			<div class="fact rv" style="--i:0">
				<dt>Stanovi</dt>
				<dd><small>od</small> <?php echo luma_num( luma_opt( 'stan_od' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <small>do</small> <?php echo luma_num( luma_opt( 'stan_do' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <small>m²</small></dd>
			</div>
			<div class="fact rv" style="--i:1">
				<dt>Poslovni prostori u prizemlju</dt>
				<dd><small>od</small> <?php echo luma_num( luma_opt( 'posl_od' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <small>do</small> <?php echo luma_num( luma_opt( 'posl_do' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <small>m²</small></dd>
			</div>
			<div class="fact rv" style="--i:2">
				<dt>Garažna parking mesta</dt>
				<dd><?php echo luma_num( luma_opt( 'garaze' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></dd>
			</div>
			<div class="fact rv" style="--i:3">
				<dt>Završetak I faze</dt>
				<dd><small>kraj</small> <?php echo luma_num( luma_opt( 'rok' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></dd>
			</div>
			<?php foreach ( array( 'tvrdnja_energija', 'tvrdnja_spratnost' ) as $luma_claim ) : ?>
				<?php if ( '' !== luma_opt( $luma_claim ) ) : ?>
					<div class="fact fact--text rv"><dt>Napomena</dt><dd><?php echo esc_html( luma_opt( $luma_claim ) ); ?></dd></div>
				<?php endif; ?>
			<?php endforeach; ?>
		</dl>
	</div>
</section>

<section class="section about" id="projekat" aria-labelledby="o-projektu">
	<div class="wrap about__grid">
		<div class="about__media rv" data-l2 data-parallax data-clip>
			<?php luma_picture( 'porodica', 'Porodica sa detetom posmatra zgradu u izgradnji', array( 'sizes' => '(min-width: 900px) 38vw, 92vw' ) ); ?>
		</div>
		<div class="about__text">
			<h2 id="o-projektu" class="rv" data-mask><?php echo esc_html( luma_opt( 'o_naslov' ) ); ?></h2>
			<div class="prose rv"><?php echo luma_paragraphs( luma_opt( 'o_tekst_1' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<p class="rv"><a class="btn btn--ghost" href="<?php echo esc_url( luma_page_url( 'o-nama' ) ); ?>">Više o nama</a></p>
		</div>
	</div>
</section>

<section class="section offer" aria-label="Poslovni prostori i garaža">
	<div class="wrap offer__grid">
		<article class="offer__item offer__item--wide rv">
			<div class="offer__media" data-l2 data-parallax>
				<?php luma_picture( 'poslovni', 'Dvoje ljudi razgovara u poslovnom prostoru sa velikim prozorima', array( 'sizes' => '(min-width: 900px) 56vw, 92vw' ) ); ?>
			</div>
			<h2 class="h3">Poslovni prostori za savremene biznise</h2>
			<p><?php echo esc_html( luma_opt( 'poslovni_tekst' ) ); ?></p>
		</article>
		<article class="offer__item offer__item--narrow rv">
			<div class="offer__media" data-l2 data-parallax>
				<?php luma_picture( 'garaza', 'Žena sa korpom u prodavnici, ilustracija praktičnosti svakodnevnih obaveza', array( 'sizes' => '(min-width: 900px) 36vw, 92vw' ) ); ?>
			</div>
			<h2 class="h3">Podzemna garaža: sigurnost, praktičnost i udobnost</h2>
			<p><?php echo esc_html( luma_opt( 'garaza_tekst' ) ); ?></p>
		</article>
	</div>
</section>

<?php get_template_part( 'template-parts/sale', null, array( 'tag' => 'h2', 'pin' => true ) ); ?>

<section class="infobook" aria-labelledby="infobook-h">
	<div class="wrap infobook__grid">
		<div class="infobook__media rv" data-l2 data-clip data-parallax>
			<?php luma_picture( 'zivot-2', 'Roditelj i dete se grle u kuhinji', array( 'sizes' => '(min-width: 900px) 40vw, 92vw' ) ); ?>
		</div>
		<div class="infobook__text">
			<h2 id="infobook-h" class="rv" data-mask>Infobook projekta</h2>
			<p class="rv"><?php echo esc_html( luma_opt( 'infobook_tekst' ) ); ?></p>
			<p class="rv"><a class="btn btn--primary" href="<?php echo esc_url( luma_opt( 'infobook' ) ); ?>" download="Luma-Residence-Infobook.pdf">Preuzmi infobook</a></p>
		</div>
	</div>
</section>

<?php
get_template_part( 'template-parts/contact', null, array( 'tag' => 'h2' ) );
get_footer();
