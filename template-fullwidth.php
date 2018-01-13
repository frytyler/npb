<?php /* TEMPLATE NAME: Full Width */ ?>
<?php get_header(); ?>
<?php if (have_posts()): the_post(); ?>
<section class="container">
	<div class="inner_container">
		<div class="col_1of1">
			<h1><?php the_title(); ?></h1>
			<?php the_content(); ?>
		</div>
	</div>
</section>
<?php endif; ?>
<?php get_footer(); ?>