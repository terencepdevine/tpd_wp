<?php
/**
 * terence_devine_wp functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package terence_devine_wp
 */

 

function custom_image_sizes() {
    add_image_size( 'project-block', 928, 392, true ); // (cropped)
}

add_action( 'after_setup_theme', 'custom_image_sizes' );


 function add_project_type_to_rest_api() {
    register_rest_field(
        'project', // Your custom post type slug
        'project_type', // The name of the field to be added
        array(
            'get_callback'    => 'get_project_type_names',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

function get_project_type_names( $object ) {
    $project_type_ids = wp_get_post_terms( $object['id'], 'project_type', array('fields' => 'ids') ); // Get the taxonomy IDs
    $project_type_names = array();

    if (!empty($project_type_ids) && !is_wp_error($project_type_ids)) {
        foreach ($project_type_ids as $project_type_id) {
            $term = get_term( $project_type_id );
            if (!is_wp_error($term)) {
                $project_type_names[] = array(
                    'id' => $term->term_id,
                    'name' => $term->name,
                    'description' => $term->description,
                    'slug' => $term->slug
                );
            }
        }
    }

    return $project_type_names;
}

add_action( 'rest_api_init', 'add_project_type_to_rest_api' );


 function create_project_type_taxonomy() {
    // Labels for the taxonomy
    $labels = array(
        'name'              => _x( 'Project Types', 'taxonomy general name', 'textdomain' ),
        'singular_name'     => _x( 'Project Type', 'taxonomy singular name', 'textdomain' ),
        'search_items'      => __( 'Search Project Types', 'textdomain' ),
        'all_items'         => __( 'All Project Types', 'textdomain' ),
        'parent_item'       => __( 'Parent Project Type', 'textdomain' ),
        'parent_item_colon' => __( 'Parent Project Type:', 'textdomain' ),
        'edit_item'         => __( 'Edit Project Type', 'textdomain' ),
        'update_item'       => __( 'Update Project Type', 'textdomain' ),
        'add_new_item'      => __( 'Add New Project Type', 'textdomain' ),
        'new_item_name'     => __( 'New Project Type Name', 'textdomain' ),
        'menu_name'         => __( 'Project Type', 'textdomain' ),
    );

    // Arguments for the taxonomy
    $args = array(
        'hierarchical'      => true, // Set to false for non-hierarchical taxonomy (like tags)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'project-type' ),
        'show_in_rest'      => true, // Enable REST API support
    );

    // Register the taxonomy
    register_taxonomy( 'project_type', array( 'project' ), $args );
}

// Hook into the init action to register the taxonomy
add_action( 'init', 'create_project_type_taxonomy', 0 );


 function add_featured_image_to_rest_api() {
    register_rest_field(
        'project', // Your custom post type slug
        'featured_image_url', // The name of the field to be added
        array(
            'get_callback'    => 'get_featured_image_url',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

function get_featured_image_url( $object ) {
    $featured_image_id = $object['featured_media']; // Get the featured image ID
    $featured_image_url = wp_get_attachment_image_url( $featured_image_id, 'project-block' ); // Get the URL of the featured image

    return $featured_image_url;
}

add_action( 'rest_api_init', 'add_featured_image_to_rest_api' );


 function create_project_post_type() {
    $labels = array(
        'name'               => _x( 'Projects', 'post type general name' ),
        'singular_name'      => _x( 'Project', 'post type singular name' ),
        'menu_name'          => _x( 'Projects', 'admin menu' ),
        'name_admin_bar'     => _x( 'Project', 'add new on admin bar' ),
        'add_new'            => _x( 'Add New', 'project' ),
        'add_new_item'       => __( 'Add New Project' ),
        'new_item'           => __( 'New Project' ),
        'edit_item'          => __( 'Edit Project' ),
        'view_item'          => __( 'View Project' ),
        'all_items'          => __( 'All Projects' ),
        'search_items'       => __( 'Search Projects' ),
        'parent_item_colon'  => __( 'Parent Projects:' ),
        'not_found'          => __( 'No projects found.' ),
        'not_found_in_trash' => __( 'No projects found in Trash.' )
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'projects' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
        'show_in_rest'       => true, // Ensure this is set to true
        'rest_base'          => 'projects',
        'rest_controller_class' => 'WP_REST_Posts_Controller', 
    );

    register_post_type( 'project', $args );
}

add_action( 'init', 'create_project_post_type' );



if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function terence_devine_wp_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on terence_devine_wp, use a find and replace
		* to change 'terence_devine_wp' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'terence_devine_wp', get_template_directory() . '/languages' );

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
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'terence_devine_wp' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'terence_devine_wp_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'terence_devine_wp_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function terence_devine_wp_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'terence_devine_wp_content_width', 640 );
}
add_action( 'after_setup_theme', 'terence_devine_wp_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function terence_devine_wp_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'terence_devine_wp' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'terence_devine_wp' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'terence_devine_wp_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function terence_devine_wp_scripts() {
	wp_enqueue_style( 'terence_devine_wp-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'terence_devine_wp-style', 'rtl', 'replace' );

	wp_enqueue_script( 'terence_devine_wp-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'terence_devine_wp_scripts' );

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
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

