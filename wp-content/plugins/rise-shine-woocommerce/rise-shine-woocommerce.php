<?php
/**
 * Plugin Name:     Rise Shine Woocommerce
 * Description:     Custom woocommerce functionanity for Rise and Shine
 * Author:          SentiusSSV
 * Text Domain:     rise-shine-woocommerce
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Rise_Shine_Woocommerce
 */


include_once dirname( __FILE__ ) . '/post-types/rs_shipping_fee.php';
include_once dirname( __FILE__ ) . '/taxonomies/rs_store.php';


add_action('init', 'rise_shine_woocommerce_init');
add_action('woocommerce_cart_calculate_fees','rise_shine_woocommerce_cart_calculate_installation_fee');
add_action('woocommerce_admin_process_product_object', 'rise_shine_woocommerce_admin_process_product_object');
add_filter('woocommerce_shipping_method_add_rate_args', 'rise_shine_woocommerce_shipping_method_add_rate_args',10, 2);
add_filter('woocommerce_csv_product_import_mapping_options', 'rise_shine_woocommerce_add_column_to_importer');
add_filter('woocommerce_csv_product_import_mapping_default_columns', 'rise_shine_woocommerce_add_column_to_mapping_screen');
add_filter('woocommerce_product_import_inserted_product_object', 'rise_shine_woocommerce_product_import_inserted_product_object', 10, 2);
add_action('woocommerce_product_options_general_product_data', 'rise_shine_woocommerce_product_options_general_product_data');
add_filter('woocommerce_product_importer_pre_expand_data', 'rise_shine_woocommerce_product_importer_pre_expand_data');
add_filter('woocommerce_shipping_methods', 'rise_shine_woocommerce_shipping_methods');
// Hook to add recipient email
add_filter('woocommerce_email_recipient_new_order', 'rise_shine_woocommerce_email_recipient', 10, 2);
add_filter('woocommerce_email_recipient_cancelled_order', 'rise_shine_woocommerce_email_recipient', 10, 2);
add_filter('woocommerce_email_recipient_failed_order', 'rise_shine_woocommerce_email_recipient', 10, 2);
// Hook validate
add_filter('woocommerce_validate_postcode', 'rise_shine_woocommerce_validate_postcode', 10, 3);
add_filter('woocommerce_add_error', 'rise_shine_woocommerce_add_error', 10);
add_action('woocommerce_after_checkout_validation', 'rise_shine_woocommerce_after_checkout_validation', 10, 2);


/**
 * Hooked woocommerce_add_error
 * Append message error for invalid postcode.
 * @param string $message.
 */
function rise_shine_woocommerce_add_error($message) {
  if ($message == __('Please enter a valid postcode / ZIP.')) {
    $message .=  '<p>' . __('Pick-up or contact your nearest store only. Find your store <a href="/contact/">here</a>') . '</p>';
  }
  return $message;
}

/**
 * Hook woocommerce_after_checkout_validation.
 * Add one more message if postcode is not match in system.
 * @param array $data.
 * @param object $errors.
 */
function rise_shine_woocommerce_after_checkout_validation(&$data, &$errors) {
  if (isset($data['shipping_postcode'])) {
    $shipping_fee_posts = _rise_shine_woocommerce_get_shipping_fee_by_postcode($data['shipping_postcode']);
    if (empty($shipping_fee_posts)) {
      $postcode_validation_notice = __('Pick-up or contact your nearest store only. Find your store <a href="/contact/">here</a>');
      $errors->add('validation', $postcode_validation_notice);
    }
  }
}

/**
 * Hooked woocommerce_validate_postcode.
 * Validate post code.
 * @param boolean $valid.
 * @param string $postcode.
 * @param string $country.
 * @return boolean $valid.
 */
function rise_shine_woocommerce_validate_postcode($valid, $postcode, $country) {
  $shipping_fee_posts = _rise_shine_woocommerce_get_shipping_fee_by_postcode($postcode);
  if (empty($shipping_fee_posts)) {
    $valid = false;
  }
  return $valid;
}


/**
 * Hooked rise_shine_woocommerce_email_recipient_EMAIL_ID.
 * Add more recipinent.
 * @param string $recipient.
 * @param object $order.
 * @return string $recipient.
 */
function rise_shine_woocommerce_email_recipient($recipient, $order) {
  $page = $_GET['page'] = isset( $_GET['page'] ) ? $_GET['page'] : '';
  if ('wc-settings' === $page) {
    return $recipient;
  }
  // just in case
  if (!$order instanceof WC_Order) {
    return $recipient;
  }
  $items = $order->get_items();
  // Get Shipping post code then find store
  $postcode = $order->get_shipping_postcode();
  $shipping_fee_posts = _rise_shine_woocommerce_get_shipping_fee_by_postcode($postcode);
  if (!empty($shipping_fee_posts)) {
    // Add email.
    $shipping_fee_post = $shipping_fee_posts[0];
    $rs_store_term = wp_get_post_terms($shipping_fee_post->ID, 'rs_store');
    if (!empty($rs_store_term)) {
      $rs_store_term = $rs_store_term[0];
      $email = get_field('store_email', $rs_store_term);
      if (!empty($email)) {
        $recipient .= ', '. trim($email);
      }
    }
  }
  return $recipient;
}


/**
 * Hooked woocommerce_shipping_method_add_rate_args.
 * Adjust $args_rate before saveing to alter shipping cost.
 * @param array $args_rate.
 * @param object $object_shipping.
 * @return $args_rate.
 */
function rise_shine_woocommerce_shipping_method_add_rate_args($args_rate, $object_shipping) {
  // We have defaut shipping rate [fee min_fee=100];
  global $post;
  $package = $args_rate['package'];
  // IF not in Australia, we dont adjust.
  if ($package['destination']['country'] != 'AU') {
    return $args_rate;
  }
  $postcode = $package['destination']['postcode'];
  $shipping_fee_posts = _rise_shine_woocommerce_get_shipping_fee_by_postcode($postcode);
  $shipping_fees = array();
  if (!empty($shipping_fee_posts)) {
    foreach ($shipping_fee_posts as $key => $post) {
      $shipping_fee = get_field('shipping_fee');
      if (!empty($shipping_fee)) {
        $shipping_fees[] = (float) $shipping_fee;
      }
    }
  }
  // Adjust cost here.
  if(!empty($shipping_fees)) {
    $args_rate['cost'] = min($shipping_fees);
  }
  return $args_rate;
}

/**
 * Add a 1% surcharge to your cart / checkout
 * change the $percentage to set the surcharge to a value to suit
 */

function rise_shine_woocommerce_cart_calculate_installation_fee() {
  global $woocommerce;
  if ( is_admin() && ! defined( 'DOING_AJAX' ) )
    return;
  $items = $woocommerce->cart->get_cart();
  $installation_fee = 0;
  foreach ($items as $key => $item) {
    $quantity = $item['quantity'];
    $product = $item['data'];
    $installation_fee_item = get_post_meta($product->get_id(), '_assembly_fee', true);
    if (!empty($installation_fee_item)) {
      $installation_fee = (float)$installation_fee_item*$quantity;
      if ($installation_fee !== 0) {
        $woocommerce->cart->add_fee( 'Installation fee for ' . $product->get_title() . ': '. get_woocommerce_currency_symbol() . (float)$installation_fee_item  .  ' x ' . $quantity, $installation_fee, true, '' );
      }
    }
  }
}

/**
 * Hooked init.
 */
function rise_shine_woocommerce_init() {
  include_once dirname( __FILE__ ) . '/includes/shipping/rs-shipping-postcode/class-rs-wc-shipping-postcode.php';
}

/**
 * Hooked woocommerce_shipping_methods.
 * Register new shipping method.
 */
function rise_shine_woocommerce_shipping_methods($shipping_methods) {
  $shipping_methods['rs_shipping_postcode'] = 'RS_WC_Shipping_PostCode';
  return $shipping_methods;
}

/**
 * Hooked to woocommerce_product_importer_before_set_parsed_data.
 * Modify image data before set parsed data.
 * @param $data
 */
function rise_shine_woocommerce_product_importer_pre_expand_data($data) {
  if (!empty($data['images'])) {
    foreach ($data['images'] as $key => $image) {
      if (strpos($image, '://') === false) {
        $data['images'][$key] = 'http://' . $_SERVER['HTTP_HOST'] . '/wp-content/uploads/product-image-migrate/' . $image;
      }
    }
  }
  return $data;
}

/**
 * Hooked woocommerce_product_options_general_product_data
 * Add text field to add Assembly Fee.
 */
function rise_shine_woocommerce_product_options_general_product_data() {
  global $product_object;
  $assembly_fee_value = get_post_meta($product_object->get_id(), '_assembly_fee');
  woocommerce_wp_text_input(
    array(
      'id'                => '_assembly_fee',
      'value'             => empty($assembly_fee_value) ? '' : $assembly_fee_value[0],
      'label'     => __( 'Assembly Fee', 'rise-shine-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
      'placeholder'       => __( 'Assembly Fee', 'rise-shine-woocommerce' ),
      'description'       => __( 'Fix ammount assembly fee' ),
      'type'              => 'number',
    )
  );
}

/**
 * Hooked woocommerce_process_product_meta
 * Save product data.
 */
function rise_shine_woocommerce_admin_process_product_object($product) {
  if (isset($_POST['_assembly_fee'])) {
    $product->update_meta_data('_assembly_fee', sanitize_text_field($_POST['_assembly_fee']));
  }
}

/**
 * Hooked woocommerce_csv_product_import_mapping_options.
 * Register the 'Custom Column' column in the woocommerce importer.
 *
 * @param array $options
 * @return array $options
 */
function rise_shine_woocommerce_add_column_to_importer($options) {
  // column slug => column name
  $options['fabric_ids'] = 'Product Fabrics';
  $options['brand_ids'] = 'Product Brands';
  $options['assembly_fee'] = 'Assembly Fee';
  return $options;
}

/**
 * Hooked woocommerce_csv_product_import_mapping_default_columns.
 * Add automatic mapping support for 'Custom Column'.
 * This will automatically select the correct mapping for columns named 'Custom Column' or 'custom column'.
 *
 * @param array $columns
 * @return array $columns
 */
function rise_shine_woocommerce_add_column_to_mapping_screen($columns) {
  // potential column name => column slug
  $columns['Fabrics'] = 'fabric_ids';
  $columns['fabrics'] = 'fabric_ids';

  $columns['Brands'] = 'brand_ids';
  $columns['brands'] = 'brand_ids';

  return $columns;
}

/**
 * Hooked woocommerce_product_import_inserted_product_object.
 * Process the data read from the CSV file.
 *
 * @param WC_Product $object - Product after imported or updated.
 * @param array $data - CSV data read for the product.
 * @return WC_Product $object
 */
function rise_shine_woocommerce_product_import_inserted_product_object($object, $data) {
  // Fabric save and set to product.
  if (!empty($data['fabric_ids'])) {
    $fabric_ids = array();
    $names = explode(', ', $data['fabric_ids']);
    foreach ( $names as $name ) {
      $term = get_term_by('name', $name, 'product-fabric');
      if ( ! $term || is_wp_error( $term ) ) {
        $term = (object) wp_insert_term( $name, 'product-fabric' );
      }
      if ( ! is_wp_error( $term ) ) {
        $fabric_ids[] = $term->term_id;
      }
    }
    if (!empty($fabric_ids)) {
      wp_set_post_terms($object->get_id(), $fabric_ids, 'product-fabric', false);
    }
  }
  // Brand save and set to product.
  if (!empty($data['brand_ids'])) {
    $brand_ids = array();
    $names = explode(', ', $data['brand_ids']);
    foreach ( $names as $name ) {
      $term = get_term_by('name', $name, 'product-brand');
      if ( ! $term || is_wp_error( $term ) ) {
        $term = (object) wp_insert_term( $name, 'product-brand' );
      }
      if ( ! is_wp_error( $term ) ) {
        $brand_ids[] = $term->term_id;
      }
    }
    if (!empty($brand_ids)) {
      wp_set_post_terms($object->get_id(), $brand_ids, 'product-brand', false);
    }
  }
  return $object;
}

/**
 * Function load shipping fee post by post code.
 * @param string $postcode.
 * @return array $shipping_fee_posts.
 */
function _rise_shine_woocommerce_get_shipping_fee_by_postcode($postcode) {
  $args = array(
    'post_type'   => 'rs_shipping_fee',
    'post_status' => 'publish',
    'meta_key' => 'shipping_fee',
    'posts_per_page' => 1,
    'meta_query'     => array(
      array(
        'key'     => 'postcode',
        'value'   => $postcode,
        'type'    => 'CHAR',
        'compare' => '=',
      ),
    ),
    'orderby' => array(
      'meta_value_num' => 'ASC',
    ),
  );
  $query = new WP_Query($args);
  if (empty($query->posts)) {
    return array();
  }else {
    return $query->posts;
  }
}

