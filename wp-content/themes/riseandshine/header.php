<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>

		<link href="//www.google-analytics.com" rel="dns-prefetch">
		<link href="<?php echo get_template_directory_uri(); ?>/assets/favicons/favicon-32x32.png" rel="shortcut icon">
    <link href="<?php echo get_template_directory_uri(); ?>/assets/favicons/favicon-32x32.png" rel="apple-touch-icon-precomposed">
		<link rel="apple-touch-icon" sizes="57x57" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192"  href="<?php echo get_template_directory_uri(); ?>/assets/favicons/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/favicon-16x16.png">
		<link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/assets/favicons/manifest.json">
		<meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/assets/favicons/ms-icon-144x144.png">
		<meta name="theme-color" content="#ffffff">

		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="<?php bloginfo('description'); ?>">

		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>

		<!-- wrapper -->
		<div class="wrapper">
			<header class="header">
			  <div class="header__logo">
			    <div class="container">
			      <div class="header__wrap">
			        <div class="header__image">
			          <a href="/"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo_image.svg" alt="Rise and Shine" class="logo-img"></a>
			        </div>

			        <div class="header__right">
								<?php get_template_part('templates/searchform'); ?>
			          <ul class="list-icons">
			            <li><a href="#our-stores" class="icon-location-pin"></a></li>
			            <li><a href="/cart" class="icon-cart"></a></li>
			          </ul>

			          <div class="menu-bars">
			            <span class="menu-bars__row"></span>
			            <span class="menu-bars__row"></span>
			            <span class="menu-bars__row"></span>
			          </div>
			        </div>
			      </div>
			    </div>
			  </div>

			  <div class="header__body">
			    <div class="header__top">
			      <div class="container">
			        <div class="header__wrap">
								<div class="primary-menu">
									<?php rs_nav('primary-menu','Main Menu','has-icon'); ?>
								</div>
			          <?php get_template_part('templates/searchform'); ?>
			        </div>
			      </div>
			    </div>

			    <div class="header__bottom">
			      <div class="container">
			        <div class="header__wrap">
			          <div class="second-menu">
									<?php rs_nav('header-top','Header Top','has-icon'); ?>
			          </div>
			          <div class="best-advice"><?php $sitewideData = get_field('sitewide','option'); print $sitewideData['header_text']; ?></div>
			          <div class="my-login">
			            <a href="/my-account" class="account"><?php _e( 'my account', 'ssvtheme' ); ?></a>
			            <a href="/cart" class="cart"><i class="icon-cart"></i></a>
			          </div>
			        </div>
			      </div>
			    </div>
			  </div>
			</header>
