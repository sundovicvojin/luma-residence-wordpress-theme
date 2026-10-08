<?php
/**
 * Obična stranica (sadržaj iz editora).
 *
 * @package luma-residence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
while ( have_posts() ) :
	the_post();
	?>
	<div class="wrap page-head">
		<h1><?php the_title(); ?></h1>
	</div>
	<div class="wrap prose section">
		<?php the_content(); ?>
	</div>
	<?php
endwhile;
get_footer();
