<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,minimum-scale=1">
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php global $page, $paged, $NPB; wp_title( '|', true, 'right' ); bloginfo('name'); $site_description = get_bloginfo('description', 'display'); if ( $site_description && ( is_home() || is_front_page() ) ) echo " | $site_description"; ?></title>
<?=$NPB->get_meta_keywords(); ?>
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<header class="header splash">
		<div class="container">
			<div class="inner_container">
				<div class="col_1of2">
					<br /><br />
					<?=$NPB->get_logo();?>
					<br />
				</div>
				<div class="col_1of2">
					<br /><br />
					<h1 class="logo align-right">Le Petit déjeuner de <br /><span class="larger">Prière national</span></h1>
				</div>
			</div>
			<?=$NPB->get_ribbon('splash');?>
		</div>
	</header>