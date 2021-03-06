<?php
/**
 * Single Product tabs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/tabs.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woocommerce_product_tabs', array() );
$deliveryText = get_field('delivery_and_returns','option');

if ( ! empty( $tabs ) ) : ?>
	<div class="rs-tabs">
	  <div class="container">
	    <div class="rs-tabs__wrap">
	      <ul>
					<?php foreach ( $tabs as $key => $tab ) : ?>
						<li class="js-index <?php if($key == 'description'): ?>active<?php endif; ?>" id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
							<a href="#tab-<?php echo esc_attr( $key ); ?>"><span><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?></span></a>
						</li>
					<?php endforeach; ?>
	      </ul>

	      <div class="rs-tabs__body">
	        <ul>
						<?php foreach ( $tabs as $key => $tab ) :?>
							<?php if($key == 'shipping'): ?>
								<li class="is-index">
			            <span class="js-show"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?> <i class="icon-arrow-right"></i></span>

			            <div class="show rs-tabs__content">
										<?php echo $deliveryText; ?>
			            </div>
			          </li>
							<?php else: ?>
								<li class="is-index <?php if($key == 'description'): ?>active<?php endif; ?>">
			            <span class="js-show"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?> <i class="icon-arrow-right"></i></span>

			            <div class="show rs-tabs__content">
			              <?php if ( isset( $tab['callback'] ) ) { call_user_func( $tab['callback'], $key, $tab ); } ?>
			            </div>
			          </li>
							<?php endif; ?>
						<?php endforeach; ?>
	        </ul>
	      </div>
	    </div>
	  </div>
	</div>

<?php endif; ?>
