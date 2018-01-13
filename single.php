<?php get_header(); ?>
<?php if (have_posts()): the_post(); ?>
	<h1><?php the_title(); ?></h1>
	<?php get_sidebar(); ?>
	<?php $NPB->get_single( $post ); ?>
<?php endif; ?>
<?php get_footer(); ?>