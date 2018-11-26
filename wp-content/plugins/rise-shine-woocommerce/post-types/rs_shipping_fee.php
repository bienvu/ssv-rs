<?php

/**
 * Registers the `rs_shipping_fee` post type.
 */
function rs_shipping_fee_init() {
	register_post_type( 'rs_shipping_fee', array(
		'labels'                => array(
			'name'                  => __( 'Shipping Fees', 'rise-shine-woocommerce' ),
			'singular_name'         => __( 'Shipping Fee', 'rise-shine-woocommerce' ),
			'featured_image'        => _x( 'Featured Image', 'rs_shipping_fee', 'rise-shine-woocommerce' ),
			'set_featured_image'    => _x( 'Set featured image', 'rs_shipping_fee', 'rise-shine-woocommerce' ),
			'remove_featured_image' => _x( 'Remove featured image', 'rs_shipping_fee', 'rise-shine-woocommerce' ),
			'use_featured_image'    => _x( 'Use as featured image', 'rs_shipping_fee', 'rise-shine-woocommerce' ),
			'filter_items_list'     => __( 'Filter Shipping Fees list', 'rise-shine-woocommerce' ),
			'items_list_navigation' => __( 'Shipping Fees list navigation', 'rise-shine-woocommerce' ),
			'items_list'            => __( 'Shipping Fees list', 'rise-shine-woocommerce' ),
			'new_item'              => __( 'New Shipping Fee', 'rise-shine-woocommerce' ),
			'add_new'               => __( 'Add New', 'rise-shine-woocommerce' ),
			'add_new_item'          => __( 'Add New Shipping Fee', 'rise-shine-woocommerce' ),
			'edit_item'             => __( 'Edit Shipping Fee', 'rise-shine-woocommerce' ),
			'view_item'             => __( 'View Shipping Fee', 'rise-shine-woocommerce' ),
			'view_items'            => __( 'View Shipping Fees', 'rise-shine-woocommerce' ),
			'search_items'          => __( 'Search Shipping Fees', 'rise-shine-woocommerce' ),
			'not_found'             => __( 'No Shipping Fees found', 'rise-shine-woocommerce' ),
			'not_found_in_trash'    => __( 'No Shipping Fees found in trash', 'rise-shine-woocommerce' ),
			'parent_item_colon'     => __( 'Parent Shipping Fee:', 'rise-shine-woocommerce' ),
			'menu_name'             => __( 'Shipping Fees', 'rise-shine-woocommerce' ),
		),
		'public'                => false,
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_nav_menus'     => false,
		'supports'              => array( 'title', 'custom-fields'),
		'has_archive'           => false,
		'show_in_menu'          => 'woocommerce',
		'rewrite'               => false,
		'query_var'             => true,
		'menu_icon'             => 'dashicons-admin-post',
		'show_in_rest'          => true,
		'rest_base'             => 'rs_shipping_fee',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
	) );

}
add_action( 'init', 'rs_shipping_fee_init' );

/**
 * Sets the post updated messages for the `rs_shipping_fee` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `rs_shipping_fee` post type.
 */
function rs_shipping_fee_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['rs_shipping_fee'] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Shipping Fee updated. <a target="_blank" href="%s">View Shipping Fee</a>', 'rise-shine-woocommerce' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'rise-shine-woocommerce' ),
		3  => __( 'Custom field deleted.', 'rise-shine-woocommerce' ),
		4  => __( 'Shipping Fee updated.', 'rise-shine-woocommerce' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Shipping Fee restored to revision from %s', 'rise-shine-woocommerce' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Shipping Fee published. <a href="%s">View Shipping Fee</a>', 'rise-shine-woocommerce' ), esc_url( $permalink ) ),
		7  => __( 'Shipping Fee saved.', 'rise-shine-woocommerce' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Shipping Fee submitted. <a target="_blank" href="%s">Preview Shipping Fee</a>', 'rise-shine-woocommerce' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'Shipping Fee scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Shipping Fee</a>', 'rise-shine-woocommerce' ),
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Shipping Fee draft updated. <a target="_blank" href="%s">Preview Shipping Fee</a>', 'rise-shine-woocommerce' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'rs_shipping_fee_updated_messages' );
