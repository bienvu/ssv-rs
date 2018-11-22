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

add_action('init', 'rise_shine_woocommerce_init');
add_filter('woocommerce_csv_product_import_mapping_options', 'rise_shine_woocommerce_add_column_to_importer');
add_filter('woocommerce_csv_product_import_mapping_default_columns', 'rise_shine_woocommerce_add_column_to_mapping_screen');
add_filter('woocommerce_product_import_inserted_product_object', 'rise_shine_woocommerce_product_import_inserted_product_object', 10, 2);
add_action('woocommerce_product_options_general_product_data', 'rise_shine_woocommerce_product_options_general_product_data');
add_filter('woocommerce_product_importer_pre_expand_data', 'rise_shine_woocommerce_product_importer_pre_expand_data');
add_filter('woocommerce_shipping_methods', 'rise_shine_woocommerce_shipping_methods');


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
      'description'       => __( 'Fix ammount assembly feee' ),
      'type'              => 'number',
    )
  );
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

