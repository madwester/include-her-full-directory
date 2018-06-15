<?php
/**
 * Include Her functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Include_Her
 */

if ( ! function_exists( 'include_her_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function include_her_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Include Her, use a find and replace
		 * to change 'include-her' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'include-her', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'primary' => esc_html__( 'Primary', 'include-her' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'include_her_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );


	add_filter(‘widget_text’, ‘do_shortcode’);
	
	}
endif;
add_action( 'after_setup_theme', 'include_her_setup' );


//REMOVE SIZE ON THUMBNAILS
function remove_thumbnail_dimensions( $html, $post_id, $post_image_id ) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
    return $html;
}
add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10, 3 );

function includeher_add_editor_style(){
	add_editor_style('build/css/editor-style.css');
}
add_action('admin_init', 'includeher_add_editor_style');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function include_her_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'include_her_content_width', 1140 );
}
add_action( 'after_setup_theme', 'include_her_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function include_her_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'include-her' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'include-her' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'include_her_widgets_init' );

//show admin bar for only admin users
function remove_admin_bar() {

    if( current_user_can( 'manage_options' ) )
        return true;

    return false;

}
add_filter( 'show_admin_bar' , 'remove_admin_bar' );

/**
 * Enqueue scripts and styles.
 */
function include_her_scripts() {
	wp_enqueue_style( 'include-her-style', get_stylesheet_uri() );

	//wp_enqueue_script( 'include-her-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( 'include-her-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'include_her_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Bootstrap nav walker.
 */
require get_template_directory() . '/inc/bootstrap-wp-navwalker.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

// Add scripts and stylesheets
function mw_scripts() {
    //STYLESHEETS
	wp_enqueue_style( 'bootstrap-css', get_template_directory_uri() . '/node_modules/bootstrap/dist/css/bootstrap.min.css' );
    wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/build/fonts/css/fontawesome-all.min.css' );
    wp_enqueue_style( 'custom-css', get_template_directory_uri() . '/build/css/custom.css' );

    //SCRIPTS FROM HERE!
	//wp_enqueue_script( 'jquery', get_template_directory_uri() . '/node_modules/jquery/dist/jquery.js', array(), '1', true );
	wp_register_script( 'popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js', false, '', true );
	wp_enqueue_script( 'popper' );

	wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/node_modules/bootstrap/dist/js/bootstrap.min.js', array( 'jquery' ), '1', true );
	wp_enqueue_script( 'nav-scroll', get_template_directory_uri() . '/build/js/nav-scroll.js', array( 'jquery' ), '1', true );
	wp_enqueue_script( 'magnific', get_template_directory_uri() . '/build/js/magnific.min.js', array(), '1', false );
	wp_enqueue_script( 'instafeedjs', get_template_directory_uri() . '/build/js/instafeed.min.js', array(), '1', false );
	wp_enqueue_script( 'custominstafeedjs', get_template_directory_uri() . '/build/js/custom-instafeed.js', array( 'magnific' ), '1', false );
    wp_enqueue_script( 'customjs', get_template_directory_uri() . '/build/js/custom.js', array(), '1', false );
}
add_action( 'wp_enqueue_scripts', 'mw_scripts' );

//true = load in footer
//false = load in head

add_filter( 'bp_login_redirect', 'bpdev_redirect_to_profile', 11, 3 );
 
function bpdev_redirect_to_profile( $redirect_to_calculated, $redirect_url_specified, $user ){
 
    if( empty( $redirect_to_calculated ) )
        $redirect_to_calculated = admin_url();
 
    //if the user is not site admin,redirect to his/her profile
 
    if( isset( $user->ID) && ! is_super_admin( $user->ID ) )
        return bp_core_get_user_domain( $user->ID );
    else
        return $redirect_to_calculated; /*if site admin or not logged in,do not do anything much*/
 
}


