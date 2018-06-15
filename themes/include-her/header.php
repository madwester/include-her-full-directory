<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Include_Her
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<!--<meta charset="<?php //bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">-->
	<meta charset="UTF-8">
	<meta property="og:url"           content="includeher.com.au" />
	<meta property="og:type"          content="website" />
	<meta property="og:title"         content="Include Her - Where developers meet" />
	<meta property="og:description"   content="Include her is an online community where developers meet." />
	<meta property="og:image" content="<?php bloginfo('template_directory')?>/build/images/dark-mockup-2.jpg">
	<!--<meta property="og:image:width" content="200">
	<meta property="og:image:height" content="200">-->
	<meta name="description" content="Include her is an online community where developers meet.">
	<meta name="keywords" content="girls, female, women, code, coding, developer">
	<meta name="author" content="Madeleine Westerstrom">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'include-her' ); ?></a>
	<header id="masthead" class="site-header">
	<nav id="menu" class="navbar navbar-light navbar-expand-lg" role="navigation">
	<div class="site-branding navbar-brand">
			<?php
			the_custom_logo();
			if ( is_front_page() && is_home() ) :
				?>
				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php
			else :
				?>
				<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php
			endif;
			$test_description = get_bloginfo( 'description', 'display' );
			if ( $test_description || is_customize_preview() ) :
				?>
				<p class="site-description"><?php echo $test_description; /* WPCS: xss ok. */ ?></p>
			<?php endif; ?>
		</div><!-- .site-branding -->

		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" 
		aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-align-right navigationSymbol"></i>
		</button>
		<?php
		wp_nav_menu([
			'menu' =>				'primary',
			'theme_location' =>		'primary',
			'container' =>			'div',
			'container_id' =>		'navbarNavDropdown',
			'container_class' =>	'collapse navbar-collapse',
			'menu_id' => 			'main_menu',
			'menu_class' => 		'navbar-nav ml-auto',
			'depth' =>				2,
			'fallback_cb' =>		'bs4navwalker::fallback',
			'walker' =>				new bs4navwalker()
		]);
		?>
	</nav>
	</header>
	<!--<div id="content" class="site-content container-fluid">-->
