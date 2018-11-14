<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>

		<link href="//www.google-analytics.com" rel="dns-prefetch">
    <link href="<?php echo get_template_directory_uri(); ?>/brand/cropped-favicon-32x32.png" rel="shortcut icon">
    <link href="<?php echo get_template_directory_uri(); ?>/brand/cropped-favicon-32x32.png" rel="apple-touch-icon-precomposed">
		<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/brand/cropped-favicon-32x32.png" sizes="32x32" />
		<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/brand/cropped-favicon-192x192.png" sizes="192x192" />
		<link rel="apple-touch-icon-precomposed" href="<?php echo get_template_directory_uri(); ?>/brand/cropped-favicon-180x180.png" />
		<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/brand/cropped-favicon-270x270.png" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="<?php bloginfo('description'); ?>">

		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>

		<!-- wrapper -->
		<div class="wrapper">
			<header class="header">
				<div class="container">
					<div class="header__wrap">
				    <div class="header__logo">
				      <a href="<?php echo home_url(); ?>" class="logo">
				        <img src="<?php echo get_template_directory_uri(); ?>/brand/logo.svg" alt="Logo" class="logo-img">
				      </a>
							<span class="menu-bars">
			          <span class="menu-bars__row"></span>
			          <span class="menu-bars__row"></span>
			          <span class="menu-bars__row"></span>
			        </span>
				    </div>

				    <div class="header__menu ">
								<?php $class = 'main-menu main-menu--has-child' ?>
								<?php french_table_nav($class,'Main Menu'); ?>

								<div class="box-search">
			            <?php get_template_part('searchform'); ?>
			            <div class="item-icon"><i href="#" class="icon-search"></i></div>
			          </div>
				    </div>
			    </div>
		    </div>
		  </header>
