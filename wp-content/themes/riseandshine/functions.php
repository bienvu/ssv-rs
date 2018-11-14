<?php
/*
 *  Author: Sentius Group
 *  URL: sentiustdigital.com | @sentiustheme
 *  Custom functions, support, custom post types and more.
 */

 // Add theme options.
 if( function_exists('acf_add_options_page') ) {
 	acf_add_options_page(array(
 		'page_title' 	=> 'Theme General Settings',
 		'menu_title'	=> 'Theme Options',
 		'menu_slug' 	=> 'theme-general-settings',
 		'capability'	=> 'edit_posts',
 		'redirect'		=> false
 	));
 }

/*------------------------------------*\
	Theme Support
\*------------------------------------*/

if (function_exists('add_theme_support')) {
    // Add Menu Support
    add_theme_support('menus');

    // Add Thumbnail Theme Support
    add_theme_support('post-thumbnails');
    add_image_size('large', 700, '', true); // Large Thumbnail
    add_image_size('medium', 250, '', true); // Medium Thumbnail
    add_image_size('small', 120, '', true); // Small Thumbnail
}

/*------------------------------------*\
	Functions
\*------------------------------------*/

// Register sentius Blank Navigation
function register_french_table_menu()
{
    register_nav_menus(array( // Using array to specify more menus if needed
        'header-menu' => __('Header Menu', 'sentiustheme'), // Main Navigation
        'sidebar-menu' => __('Sidebar Menu', 'sentiustheme'), // Sidebar Navigation
        'extra-menu' => __('Extra Menu', 'sentiustheme') // Extra Navigation if needed (duplicate as many as you need!)
  ));
}
add_action('init', 'register_french_table_menu');

// Navigation
function french_table_nav($menuclass, $name) {
	wp_nav_menu(
	array(
		'theme_location'  => 'header-menu',
		'menu'            => $name,
		'container'       => '',
		'container_class' => '',
		'container_id'    => '',
		'menu_class'      => '',
		'menu_id'         => '',
		'echo'            => true,
		'fallback_cb'     => 'wp_page_menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '<span>',
		'link_after'      => '</span>',
		'items_wrap'      => '<ul class="'.$menuclass.'">%3$s</ul>',
		'depth'           => 0,
		'walker'          => ''
		)
	);
}
// Set class for menu dropdown
function menu_set_dropdown( $sorted_menu_items, $args ) {
    $last_top = 0;
    foreach ( $sorted_menu_items as $key => $obj ) {
        // it is a top lv item?
        if ( 0 == $obj->menu_item_parent ) {
            // set the key of the parent
            $last_top = $key;
        } else {
            $sorted_menu_items[$last_top]->classes['dropdown'] = 'menu-expend';
        }
    }
    return $sorted_menu_items;
}

// Add style to header.
function wp_add_styles() {
    wp_register_style('styles', get_template_directory_uri() . '/assets/css/styles.css', array(), '1.0', 'all');
    wp_enqueue_style('styles');
}

// Add script to footer.
function wp_add_scripts() {
    global $wp_query;
    if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {
        // Script.
        wp_register_script('script', get_template_directory_uri() . '/assets/js/script.js', array('jquery', 'masonry', 'slick'), 'asdsad'); // Custom scripts
    }
}

// Remove Injected classes, ID's and Page ID's from Navigation <li> items
function my_css_attributes_filter( $var ) {
    return is_array($var) ? array() : '';
}

// Remove invalid rel attribute values in the categorylist
function remove_category_rel_from_category_list( $thelist ) {
    return str_replace('rel="category tag"', 'rel="tag"', $thelist);
}

// Add page slug to body class, love this - Credit: Starkers Wordpress Theme
function add_slug_to_body_class($classes) {
    global $post;
    if (is_home()) {
        $key = array_search('blog', $classes);
        if ($key > -1) {
            unset($classes[$key]);
        }
    } elseif (is_page()) {
        $classes[] = sanitize_html_class($post->post_name);
    } elseif (is_singular()) {
        $classes[] = sanitize_html_class($post->post_name);
    }

    return $classes;
}

// Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
function ssv_pagination() {
    global $wp_query;
    $big = 999999999;
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages
    ));
}

// Custom Excerpts
function sentiusvn_index($length) // Create 20 Word Callback for Index page Excerpts, call using sentiusvn_excerpt('sentiusvn_index');
{
    return 20;
}

// Create 40 Word Callback for Custom Post Excerpts, call using sentiusvn_excerpt('sentiusvn_custom_post');
function sentiusvn_custom_post($length)
{
    return 40;
}

// Create the Custom Excerpts callback
function sentiusvn_excerpt($length_callback = '', $more_callback = '')
{
    global $post;
    if (function_exists($length_callback)) {
        add_filter('excerpt_length', $length_callback);
    }
    if (function_exists($more_callback)) {
        add_filter('excerpt_more', $more_callback);
    }
    $output = get_the_excerpt();
    $output = apply_filters('wptexturize', $output);
    $output = apply_filters('convert_chars', $output);
    $output = '<p>' . $output . '</p>';
    echo $output;
}

// Remove 'text/css' from our enqueued stylesheet
function ssv_style_remove($tag)
{
    return preg_replace('~\s+type=["\'][^"\']++["\']~', '', $tag);
}

// Remove thumbnail width and height dimensions that prevent fluid images in the_thumbnail
function ssv_remove_thumbnail_dimensions( $html )
{
    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
    return $html;
}

/*------------------------------------*\
    Actions + Filters + ShortCodes
\*------------------------------------*/

/*------------*\
    Actions
\*------------*/
add_action('wp_enqueue_scripts', 'wp_add_styles'); // Add Theme Stylesheet
add_action('init', 'ssv_pagination'); // Add our sentius Pagination
add_action('wp_footer', 'wp_add_scripts'); // Add Custom Scripts to wp_head

// Remove Actions
remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'index_rel_link'); // Index link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // Prev link
remove_action('wp_head', 'start_post_rel_link', 10, 0); // Start link
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

/*----------*\
    Filters
\*----------*/
add_filter('body_class', 'add_slug_to_body_class'); // Add slug to body class (Starkers build)
add_filter('widget_text', 'do_shortcode'); // Allow shortcodes in Dynamic Sidebar
add_filter('widget_text', 'shortcode_unautop'); // Remove <p> tags in Dynamic Sidebars (better!)
add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected ID (Commented out by default)
add_filter('page_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> Page ID's (Commented out by default)
add_filter( 'wp_nav_menu_objects', 'menu_set_dropdown', 10, 2 );
// add_filter('the_category', 'remove_category_rel_from_category_list'); // Remove invalid rel attribute
add_filter('the_excerpt', 'shortcode_unautop'); // Remove auto <p> tags in Excerpt (Manual Excerpts only)
add_filter('the_excerpt', 'do_shortcode'); // Allows Shortcodes to be executed in Excerpt (Manual Excerpts only)
add_filter('style_loader_tag', 'ssv_style_remove'); // Remove 'text/css' from enqueued stylesheet
add_filter('post_thumbnail_html', 'ssv_remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to thumbnails
add_filter('image_send_to_editor', 'ssv_remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to post images

// Remove Filters
remove_filter('the_excerpt', 'wpautop'); // Remove <p> tags from Excerpt altogether

?>