<?php

/**
 * Registers the `product_brand` taxonomy,
 * for use with 'product'.
 */
function product_brand_init() {
	register_taxonomy( 'product-brand', array( 'product' ), array(
		'hierarchical'      => false,
		'public'            => true,
		'show_in_nav_menus' => true,
		'show_ui'           => true,
		'show_admin_column' => false,
		'query_var'         => true,
		'rewrite'           => true,
		'capabilities'      => array(
			'manage_terms'  => 'edit_posts',
			'edit_terms'    => 'edit_posts',
			'delete_terms'  => 'edit_posts',
			'assign_terms'  => 'edit_posts',
		),
		'labels'            => array(
			'name'                       => __( 'Brands', 'rise-shine-taxonomies-and-post-types' ),
			'singular_name'              => _x( 'Brand', 'taxonomy general name', 'rise-shine-taxonomies-and-post-types' ),
			'search_items'               => __( 'Search Brands', 'rise-shine-taxonomies-and-post-types' ),
			'popular_items'              => __( 'Popular Brands', 'rise-shine-taxonomies-and-post-types' ),
			'all_items'                  => __( 'All Brands', 'rise-shine-taxonomies-and-post-types' ),
			'parent_item'                => __( 'Parent Brand', 'rise-shine-taxonomies-and-post-types' ),
			'parent_item_colon'          => __( 'Parent Brand:', 'rise-shine-taxonomies-and-post-types' ),
			'edit_item'                  => __( 'Edit Brand', 'rise-shine-taxonomies-and-post-types' ),
			'update_item'                => __( 'Update Brand', 'rise-shine-taxonomies-and-post-types' ),
			'view_item'                  => __( 'View Brand', 'rise-shine-taxonomies-and-post-types' ),
			'add_new_item'               => __( 'New Brand', 'rise-shine-taxonomies-and-post-types' ),
			'new_item_name'              => __( 'New Brand', 'rise-shine-taxonomies-and-post-types' ),
			'separate_items_with_commas' => __( 'Separate Brands with commas', 'rise-shine-taxonomies-and-post-types' ),
			'add_or_remove_items'        => __( 'Add or remove Brands', 'rise-shine-taxonomies-and-post-types' ),
			'choose_from_most_used'      => __( 'Choose from the most used Brands', 'rise-shine-taxonomies-and-post-types' ),
			'not_found'                  => __( 'No Brands found.', 'rise-shine-taxonomies-and-post-types' ),
			'no_terms'                   => __( 'No Brands', 'rise-shine-taxonomies-and-post-types' ),
			'menu_name'                  => __( 'Brands', 'rise-shine-taxonomies-and-post-types' ),
			'items_list_navigation'      => __( 'Brands list navigation', 'rise-shine-taxonomies-and-post-types' ),
			'items_list'                 => __( 'Brands list', 'rise-shine-taxonomies-and-post-types' ),
			'most_used'                  => _x( 'Most Used', 'product-brand', 'rise-shine-taxonomies-and-post-types' ),
			'back_to_items'              => __( '&larr; Back to Brands', 'rise-shine-taxonomies-and-post-types' ),
		),
		'show_in_rest'      => true,
		'rest_base'         => 'product-brand',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
	) );

}
add_action( 'init', 'product_brand_init' );

/**
 * Sets the post updated messages for the `product_brand` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `product_brand` taxonomy.
 */
function product_brand_updated_messages( $messages ) {

	$messages['product-brand'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Brand added.', 'rise-shine-taxonomies-and-post-types' ),
		2 => __( 'Brand deleted.', 'rise-shine-taxonomies-and-post-types' ),
		3 => __( 'Brand updated.', 'rise-shine-taxonomies-and-post-types' ),
		4 => __( 'Brand not added.', 'rise-shine-taxonomies-and-post-types' ),
		5 => __( 'Brand not updated.', 'rise-shine-taxonomies-and-post-types' ),
		6 => __( 'Brands deleted.', 'rise-shine-taxonomies-and-post-types' ),
	);

	return $messages;
}
add_filter( 'term_updated_messages', 'product_brand_updated_messages' );
