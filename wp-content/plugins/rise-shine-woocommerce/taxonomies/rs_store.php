<?php

/**
 * Registers the `rs_store` taxonomy,
 * for use with 'rs_shipping_fee'.
 */
function rs_store_init() {
  register_taxonomy( 'rs_store', array( 'rs_shipping_fee' ), array(
    'hierarchical'      => true,
    'public'            => true,
    'show_in_nav_menus' => true,
    'show_ui'           => true,
    'show_admin_column' => false,
    'query_var'         => false,
    'rewrite'           => true,
    'capabilities'      => array(
      'manage_terms'  => 'edit_posts',
      'edit_terms'    => 'edit_posts',
      'delete_terms'  => 'edit_posts',
      'assign_terms'  => 'edit_posts',
    ),
    'labels'            => array(
      'name'                       => __( 'Stores', 'rise-shine-woocommerce' ),
      'singular_name'              => _x( 'Store', 'taxonomy general name', 'rise-shine-woocommerce' ),
      'search_items'               => __( 'Search Stores', 'rise-shine-woocommerce' ),
      'popular_items'              => __( 'Popular Stores', 'rise-shine-woocommerce' ),
      'all_items'                  => __( 'All Stores', 'rise-shine-woocommerce' ),
      'parent_item'                => __( 'Parent Store', 'rise-shine-woocommerce' ),
      'parent_item_colon'          => __( 'Parent Store:', 'rise-shine-woocommerce' ),
      'edit_item'                  => __( 'Edit Store', 'rise-shine-woocommerce' ),
      'update_item'                => __( 'Update Store', 'rise-shine-woocommerce' ),
      'view_item'                  => __( 'View Store', 'rise-shine-woocommerce' ),
      'add_new_item'               => __( 'New Store', 'rise-shine-woocommerce' ),
      'new_item_name'              => __( 'New Store', 'rise-shine-woocommerce' ),
      'separate_items_with_commas' => __( 'Separate Stores with commas', 'rise-shine-woocommerce' ),
      'add_or_remove_items'        => __( 'Add or remove Stores', 'rise-shine-woocommerce' ),
      'choose_from_most_used'      => __( 'Choose from the most used Stores', 'rise-shine-woocommerce' ),
      'not_found'                  => __( 'No Stores found.', 'rise-shine-woocommerce' ),
      'no_terms'                   => __( 'No Stores', 'rise-shine-woocommerce' ),
      'menu_name'                  => __( 'Stores', 'rise-shine-woocommerce' ),
      'items_list_navigation'      => __( 'Stores list navigation', 'rise-shine-woocommerce' ),
      'items_list'                 => __( 'Stores list', 'rise-shine-woocommerce' ),
      'most_used'                  => _x( 'Most Used', 'rs_store', 'rise-shine-woocommerce' ),
      'back_to_items'              => __( '&larr; Back to Stores', 'rise-shine-woocommerce' ),
    ),
    'show_in_rest'      => true,
    'rest_base'         => 'rs_store',
    'rest_controller_class' => 'WP_REST_Terms_Controller',
  ) );

}
add_action( 'init', 'rs_store_init' );

/**
 * Sets the post updated messages for the `rs_store` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `rs_store` taxonomy.
 */
function rs_store_updated_messages( $messages ) {

  $messages['rs_store'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => __( 'Store added.', 'rise-shine-woocommerce' ),
    2 => __( 'Store deleted.', 'rise-shine-woocommerce' ),
    3 => __( 'Store updated.', 'rise-shine-woocommerce' ),
    4 => __( 'Store not added.', 'rise-shine-woocommerce' ),
    5 => __( 'Store not updated.', 'rise-shine-woocommerce' ),
    6 => __( 'Stores deleted.', 'rise-shine-woocommerce' ),
  );

  return $messages;
}
add_filter( 'term_updated_messages', 'rs_store_updated_messages' );
