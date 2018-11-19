<?php

/**
 * Registers the `fabric` taxonomy,
 * for use with 'product'.
 */
function product_fabric_init() {
	register_taxonomy( 'product-fabric', array( 'product' ), array(
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
			'name'                       => __( 'Fabrics', 'rise-shine-taxonomies-and-post-types' ),
			'singular_name'              => _x( 'Fabric', 'taxonomy general name', 'rise-shine-taxonomies-and-post-types' ),
			'search_items'               => __( 'Search Fabrics', 'rise-shine-taxonomies-and-post-types' ),
			'popular_items'              => __( 'Popular Fabrics', 'rise-shine-taxonomies-and-post-types' ),
			'all_items'                  => __( 'All Fabrics', 'rise-shine-taxonomies-and-post-types' ),
			'parent_item'                => __( 'Parent Fabric', 'rise-shine-taxonomies-and-post-types' ),
			'parent_item_colon'          => __( 'Parent Fabric:', 'rise-shine-taxonomies-and-post-types' ),
			'edit_item'                  => __( 'Edit Fabric', 'rise-shine-taxonomies-and-post-types' ),
			'update_item'                => __( 'Update Fabric', 'rise-shine-taxonomies-and-post-types' ),
			'view_item'                  => __( 'View Fabric', 'rise-shine-taxonomies-and-post-types' ),
			'add_new_item'               => __( 'New Fabric', 'rise-shine-taxonomies-and-post-types' ),
			'new_item_name'              => __( 'New Fabric', 'rise-shine-taxonomies-and-post-types' ),
			'separate_items_with_commas' => __( 'Separate Fabrics with commas', 'rise-shine-taxonomies-and-post-types' ),
			'add_or_remove_items'        => __( 'Add or remove Fabrics', 'rise-shine-taxonomies-and-post-types' ),
			'choose_from_most_used'      => __( 'Choose from the most used Fabrics', 'rise-shine-taxonomies-and-post-types' ),
			'not_found'                  => __( 'No Fabrics found.', 'rise-shine-taxonomies-and-post-types' ),
			'no_terms'                   => __( 'No Fabrics', 'rise-shine-taxonomies-and-post-types' ),
			'menu_name'                  => __( 'Fabrics', 'rise-shine-taxonomies-and-post-types' ),
			'items_list_navigation'      => __( 'Fabrics list navigation', 'rise-shine-taxonomies-and-post-types' ),
			'items_list'                 => __( 'Fabrics list', 'rise-shine-taxonomies-and-post-types' ),
			'most_used'                  => _x( 'Most Used', 'fabric', 'rise-shine-taxonomies-and-post-types' ),
			'back_to_items'              => __( '&larr; Back to Fabrics', 'rise-shine-taxonomies-and-post-types' ),
		),
		'show_in_rest'      => true,
		'rest_base'         => 'product-fabric',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
	) );

}
add_action( 'init', 'product_fabric_init' );

/**
 * Sets the post updated messages for the `fabric` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `fabric` taxonomy.
 */
function product_fabric_updated_messages( $messages ) {

	$messages['fabric'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Fabric added.', 'rise-shine-taxonomies-and-post-types' ),
		2 => __( 'Fabric deleted.', 'rise-shine-taxonomies-and-post-types' ),
		3 => __( 'Fabric updated.', 'rise-shine-taxonomies-and-post-types' ),
		4 => __( 'Fabric not added.', 'rise-shine-taxonomies-and-post-types' ),
		5 => __( 'Fabric not updated.', 'rise-shine-taxonomies-and-post-types' ),
		6 => __( 'Fabrics deleted.', 'rise-shine-taxonomies-and-post-types' ),
	);

	return $messages;
}
add_filter( 'term_updated_messages', 'product_fabric_updated_messages' );
