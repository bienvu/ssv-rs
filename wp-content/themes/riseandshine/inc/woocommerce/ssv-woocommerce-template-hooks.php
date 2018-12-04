<?php
/**
 * SSV WooCommerce hooks
 *
 * @package riseandshine
 */

add_filter('woocommerce_product_tabs', 'riseandshine_default_product_tabs');
add_filter('woocommerce_product_description_heading', 'riseandshine_product_description_heading');
add_action('woocommerce_single_product_summary', 'riseandshine_single_product_size_information', 15);
add_action('woocommerce_single_product_summary', 'riseandshine_product_share_buttons_payment_brands', 35);

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb',    20 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper',    10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content',    'ssv_before_content',                    10 );
add_action( 'woocommerce_after_main_content',    'ssv_after_content',                      10 );
add_action( 'woocommerce_breadcrumb_content',    'woocommerce_breadcrumb',                      10 );

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
add_action( 'woocommerce_after_single_product_summary', 'ssv_content_middle', 15 );
add_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 20 );
add_action( 'woocommerce_after_single_product_summary', 'ssv_content_bottom', 25 );

// Product list
//remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
//remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
add_action( 'woocommerce_before_shop_loop_item_title', 'ssv_woocommerce_template_loop_product_thumbnail', 10 );

remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
add_action( 'woocommerce_shop_loop_item_title', 'ssv_woocommerce_template_loop_product_title', 10 );
add_action( 'woocommerce_shop_loop_item_title', 'ssv_woocommerce_product_size', 20 );

// Price
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

// Product detail
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

add_filter( 'woocommerce_breadcrumb_defaults', 'wcc_change_breadcrumb_delimiter', 20 );
