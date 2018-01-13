<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,minimum-scale=1">
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php global $page, $paged, $q_config, $NPB; wp_title( '|', true, 'right' ); bloginfo('name'); $site_description = get_bloginfo('description', 'display'); if ( $site_description && ( is_home() || is_front_page() ) ) echo " | $site_description"; ?></title>
<?=$NPB->get_meta_keywords(); ?>
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php wp_head(); ?>
</head>
<body <?php body_class($q_config['language']); ?>>
	<header class="header">
		<div class="container">
			<button type="button" class="btn btn-secondary btn-navbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<div class="inner_container">
				<div class="col_1of1">
					<?=$NPB->get_language_toggle(); ?>
					<?=$NPB->get_menu('Top','secondary-nav right');?>
				</div>
				<div class="col_2of5">
					<?=$NPB->get_logo();?>
				</div>
				<div class="col_3of5">
					<?=$NPB->get_menu('Primary', 'main-nav right');?>
				</div>
			</div>
			<?=$NPB->get_ribbon();?>
		</div>
	</header>