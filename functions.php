<?php
/**
 * creativembers functions and definitions
 *
 * @package creativembers
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640; /* pixels */

if ( ! function_exists( 'creativembers_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
function creativembers_setup() {

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on creativembers, use a find and replace
	 * to change 'creativembers' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'creativembers', get_template_directory() . '/languages' );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable support for Post Thumbnails on posts and pages
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	// Set post thumbnails to 100 px square and cropping to true
	set_post_thumbnail_size(150, 150, true);

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary' => __( 'Main Menu', 'creativembers' ),
		'secondary' => __( 'Top Menu', 'creativembers' ),
	) );

	/**
	 * Enable support for Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

	/**
	 * Setup the WordPress core custom background feature.
	 */
	add_theme_support( 'custom-background', apply_filters( 'creativembers_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // creativembers_setup
add_action( 'after_setup_theme', 'creativembers_setup' );

/**
 * Register widgetized area and update sidebar with default widgets
 */
function creativembers_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'creativembers' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'creativembers_widgets_init' );

/**
 * Enqueue scripts and styles
 */
function creativembers_scripts() {
	wp_enqueue_style( 'creativembers-style', get_stylesheet_uri() );

	wp_enqueue_script( 'creativembers-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'creativembers-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'creativembers-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
	}
}
add_action( 'wp_enqueue_scripts', 'creativembers_scripts' );

/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Add description support to menu
 */
require get_template_directory() . '/inc/jetpack.php';


class Menu_With_Description extends Walker_Nav_Menu {
	function start_el(&$output, $item, $depth, $args) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		
		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';
		$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) .'"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '<br /><span class="sub">' . $item->description . '</span>';
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

//Add a login/logout link to your navigation menu
 
add_filter('wp_nav_menu_items', 'add_login_logout_link', 10, 2);
function add_login_logout_link($items, $args) {
 
     if($args->theme_location == 'secondary') {   
        ob_start();
        wp_loginout('index.php');
        $loginoutlink = ob_get_contents();
        ob_end_clean();
 
        $items .= '<li>'. $loginoutlink .'</li>';
 	}
    return $items;
}
// Alter the widget title markup
add_filter('dynamic_sidebar_params', 'wrap_widget_titles', 20);

/**
 * Wrap the widget titles - including any existing before/after title markup
 * inside an extra div we can target with div.widget_title_wrapper.
 *
 * If we needed to do this selectively we could test against $widget['name']
 * or $widget['id'].
 */
function wrap_widget_titles(array $params) {
        // $params will ordinarily be an array of 2 elements, we are interested
        // only in the first element
        $widget =& $params[0];

        // Wrap the title
        $widget['before_title'] = '<div class="widget_title_wrapper">'.$widget['before_title'];
        $widget['after_title'] = $widget['after_title'] .'</div> ';

        return $params;
}
// Add read more link to posts after excerpt
function new_excerpt_more( $more ) {
	return ' [.....] <a class="read-more" href="'. get_permalink( get_the_ID() ) . '">Read More</a>';
}
add_filter( 'excerpt_more', 'new_excerpt_more' );

// Adjust Excerpt length
function custom_excerpt_length( $length ) {
	return 60;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

//Add Font Awesome Integration

add_action('wp_enqueue_scripts', 'zig_style_loader');
function zig_style_loader() {
 
    // Enqueue FontAwesome from NetDNA CDN
    wp_enqueue_style('fontawesome', '//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css', array(), null);
 
    // Enqueue IE conditional styles
    global $wp_styles;
    wp_enqueue_style('ie7-fontawesome', '//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome-ie7.min.css', array(), null);
    $wp_styles->add_data( 'ie7-fontawesome', 'conditional', 'lte IE 7' );
}
