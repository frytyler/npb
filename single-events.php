<?php get_header(); ?>
<?php if (have_posts()): the_post(); ?>
<section class="main eventpage container">
	<?php $NPB->get_event( $post ); ?>
</section>
<?php endif; ?>
<?php get_footer(); ?>