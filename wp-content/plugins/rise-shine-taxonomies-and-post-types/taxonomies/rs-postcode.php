<?php

/**
 * Registers the `PostCode` taxonomy,
 * for use with 'product'.
 */
function rs_postcode_init() {
  register_taxonomy( 'rs-postcode', array( 'product' ), array(
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
      'name'                       => __( 'PostCodes', 'rise-shine-taxonomies-and-post-types' ),
      'singular_name'              => _x( 'PostCode', 'taxonomy general name', 'rise-shine-taxonomies-and-post-types' ),
      'search_items'               => __( 'Search PostCodes', 'rise-shine-taxonomies-and-post-types' ),
      'popular_items'              => __( 'Popular PostCodes', 'rise-shine-taxonomies-and-post-types' ),
      'all_items'                  => __( 'All PostCodes', 'rise-shine-taxonomies-and-post-types' ),
      'parent_item'                => __( 'Parent PostCode', 'rise-shine-taxonomies-and-post-types' ),
      'parent_item_colon'          => __( 'Parent PostCode:', 'rise-shine-taxonomies-and-post-types' ),
      'edit_item'                  => __( 'Edit PostCode', 'rise-shine-taxonomies-and-post-types' ),
      'update_item'                => __( 'Update PostCode', 'rise-shine-taxonomies-and-post-types' ),
      'view_item'                  => __( 'View PostCode', 'rise-shine-taxonomies-and-post-types' ),
      'add_new_item'               => __( 'New PostCode', 'rise-shine-taxonomies-and-post-types' ),
      'new_item_name'              => __( 'New PostCode', 'rise-shine-taxonomies-and-post-types' ),
      'separate_items_with_commas' => __( 'Separate PostCodes with commas', 'rise-shine-taxonomies-and-post-types' ),
      'add_or_remove_items'        => __( 'Add or remove PostCodes', 'rise-shine-taxonomies-and-post-types' ),
      'choose_from_most_used'      => __( 'Choose from the most used PostCodes', 'rise-shine-taxonomies-and-post-types' ),
      'not_found'                  => __( 'No PostCodes found.', 'rise-shine-taxonomies-and-post-types' ),
      'no_terms'                   => __( 'No PostCodes', 'rise-shine-taxonomies-and-post-types' ),
      'menu_name'                  => __( 'PostCodes', 'rise-shine-taxonomies-and-post-types' ),
      'items_list_navigation'      => __( 'PostCodes list navigation', 'rise-shine-taxonomies-and-post-types' ),
      'items_list'                 => __( 'PostCodes list', 'rise-shine-taxonomies-and-post-types' ),
      'most_used'                  => _x( 'Most Used', 'PostCode', 'rise-shine-taxonomies-and-post-types' ),
      'back_to_items'              => __( '&larr; Back to PostCodes', 'rise-shine-taxonomies-and-post-types' ),
    ),
    'show_in_rest'      => true,
    'rest_base'         => 'rs-postcode',
    'rest_controller_class' => 'WP_REST_Terms_Controller',
  ) );

}
add_action( 'init', 'rs_postcode_init' );

/**
 * Sets the post updated messages for the `PostCode` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `PostCode` taxonomy.
 */
function rs_postcode_updated_messages( $messages ) {

  $messages['rs-postcode'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => __( 'PostCode added.', 'rise-shine-taxonomies-and-post-types' ),
    2 => __( 'PostCode deleted.', 'rise-shine-taxonomies-and-post-types' ),
    3 => __( 'PostCode updated.', 'rise-shine-taxonomies-and-post-types' ),
    4 => __( 'PostCode not added.', 'rise-shine-taxonomies-and-post-types' ),
    5 => __( 'PostCode not updated.', 'rise-shine-taxonomies-and-post-types' ),
    6 => __( 'PostCodes deleted.', 'rise-shine-taxonomies-and-post-types' ),
  );

  return $messages;
}
add_filter( 'term_updated_messages', 'rs_postcode_updated_messages' );
