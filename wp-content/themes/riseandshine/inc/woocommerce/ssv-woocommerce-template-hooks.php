<?php
/**
 * SSV WooCommerce hooks
 *
 * @package ssvtheme
 */

/**
 * Layout
 *
 * @see  storefront_before_content()
 * @see  storefront_after_content()
 * @see  woocommerce_breadcrumb()
 * @see  storefront_shop_messages()
 */
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
