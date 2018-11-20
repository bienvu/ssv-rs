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
			<!-- <?php rs_nav($class,'Main Menu'); ?> -->
			<header class="header">
			  <div class="header__logo">
			    <div class="container">
			      <div class="header__wrap">
			        <div class="header__image">
			          <a href="/"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo_image.svg" alt="Rise and Shine" class="logo-img"></a>
			        </div>

			        <div class="header__right">
								<?php get_template_part('searchform'); ?>

			          <ul class="list-icons">
			            <li><a href="#" class="icon-location"></a></li>
			            <li><a href="#" class="icon-cart"></a></li>
			          </ul>

			          <div class="menu-bars">
			            <span class="menu-bars__row"></span>
			            <span class="menu-bars__row"></span>
			            <span class="menu-bars__row"></span>
			          </div>
			        </div>

			        <div class="best-advice best-advice--mobile">
			          <div class="container">
			            <a href="#"><?php $sitewideData = get_field('sitewide','option'); print $sitewideData['header_text']; ?></a>
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
									<?php rs_nav('primary-menu','Main Menu'); ?>
								</div>
			          <?php get_template_part('templates/searchform'); ?>
			        </div>
			      </div>
			    </div>

			    <div class="header__bottom">
			      <div class="container">
			        <div class="header__wrap">
			          <div class="second-menu">
									<?php rs_nav('header-top','Header Top'); ?>
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
