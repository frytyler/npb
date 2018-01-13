<?php /* TEMPLATE NAME: Custom Category  */ ?>

<?php get_header(); ?>
<div class="main container">
	<div class="inner_container">
		<?php if (have_posts()): the_post(); ?>
			<div class="col_2of3">
				<h2><?php the_title(); ?></h2>	
				<?php the_content(); ?>
				<?php 
					$postmeta = get_post_meta($post->ID, 'pagecat', true);
					$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

					// Set up default guery args.
					$args = array("post_type" => "post", "posts_per_page" => 10, "paged" => $paged, "orderby" => "date", "order" => "DESC");

					// Narrow post results to whatever category is select on the page.
					if(!empty($postmeta)) {
						$categories = get_categories( 'child_of='.$postmeta["catids"][0] ); 	
						if(empty($categories)) {
							$args["category__in"] = $postmeta["catids"];
						} else {
							$cats = array();
							$x=0;
							foreach($categories as $cat):
								$cats[$x] = $cat->cat_ID;
								$x++;
							endforeach;
							$args["category__in"] = $cats;
						}
					}			
					$NPB->get_queried_posts($args, 'section.post.twig');
				?>
			</div>
			<aside class="col_1of3 sidebar">
				<?php get_sidebar('services'); ?>	
			</aside>
		<?php endif; ?>
	</div>
</div>
<?php get_footer(); ?>