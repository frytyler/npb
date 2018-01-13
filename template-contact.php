<?php /* TEMPLATE NAME: Contact */ ?>

<?php get_header(); ?>
<?php if (have_posts()): the_post(); ?>
<section class="main container">
	<div class="inner_container">
		<div class="col_2of3">
			<h1><?php the_title(); ?></h1>
			<?php the_content(); ?>
			<?php $NPB->get_contact_page(); ?>
		</div>
		<aside class="sidebar col_1of3">
			<?php get_sidebar(); ?>
		</aside>
	</div>
</section>
<?php endif; ?>
<?php get_footer(); ?>