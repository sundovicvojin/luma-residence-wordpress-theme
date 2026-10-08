<?php
/**
 * Rezervni šablon.
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<div class="wrap page-head">
	<h1><?php echo esc_html( is_search() ? 'Rezultati pretrage' : 'Luma Residence' ); ?></h1>
</div>
<div class="wrap prose section">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php
			the_excerpt();
		endwhile;
	else :
		echo '<p>Nema sadržaja.</p>';
	endif;
	?>
</div>
<?php
get_footer();
