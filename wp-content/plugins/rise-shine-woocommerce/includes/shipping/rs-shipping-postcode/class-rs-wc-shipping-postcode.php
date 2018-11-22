<?php
/**
 * RS Post code Shipping Method.
 *
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * RS_WC_Shipping_PostCode class.
 */
class RS_WC_Shipping_PostCode extends WC_Shipping_Method {
  /**
   * Constructor.
   *
   * @param int $instance_id Shipping method instance.
   */
  public function __construct( $instance_id = 0 ) {
    $this->id                 = 'rs_shipping_postcode';
    $this->instance_id        = absint( $instance_id );
    $this->method_title       = __( 'Rise and Shine', 'woocommerce' );
    $this->method_description = __( 'Caculate shipping cost base on PostCode', 'woocommerce' );
    $this->supports           = array(
      'shipping-zones',
      'instance-settings',
      'instance-settings-modal',
    );

    $this->init();
  }

  /**
   * Initialize free shipping.
   */
  public function init() {
    // Load the settings.
    $this->init_form_fields();

    // Define user set variables.
    $this->title                = $this->get_option( 'title' );
    $this->tax_status           = $this->get_option( 'tax_status' );
    $this->cost                 = $this->get_option( 'cost' );

    // Actions.
    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
  }

  /**
   * Init form fields.
   */
  public function init_form_fields() {
    $cost_desc = __( 'Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>.', 'woocommerce' ) . '<br/><br/>' . __( 'Use <code>[qty]</code> for the number of items, <br/><code>[cost]</code> for the total cost of items, and <code>[fee percent="10" min_fee="20" max_fee=""]</code> for percentage based fees.', 'woocommerce' );

    $settings = array(
      'title'      => array(
        'title'       => __( 'Method title', 'woocommerce' ),
        'type'        => 'text',
        'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
        'default'     => __( 'Rise & Shine Shipping', 'woocommerce' ),
        'desc_tip'    => true,
      ),
      'tax_status' => array(
        'title'   => __( 'Tax status', 'woocommerce' ),
        'type'    => 'select',
        'class'   => 'wc-enhanced-select',
        'default' => 'taxable',
        'options' => array(
          'taxable' => __( 'Taxable', 'woocommerce' ),
          'none'    => _x( 'None', 'Tax status', 'woocommerce' ),
        ),
      ),
      'cost'       => array(
        'title'             => __( 'Cost', 'woocommerce' ),
        'type'              => 'text',
        'placeholder'       => '',
        'description'       => $cost_desc,
        'default'           => '0',
        'desc_tip'          => true,
        'sanitize_callback' => array( $this, 'sanitize_cost' ),
      ),
    );
    $this->instance_form_fields = $settings;
  }

  /**
   * Evaluate a cost from a sum/string.
   *
   * @param  string $sum Sum of shipping.
   * @param  array  $args Args.
   * @return string
   */
  protected function evaluate_cost( $sum, $args = array() ) {
    include_once WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php';

    // Allow 3rd parties to process shipping cost arguments.
    $args           = apply_filters( 'woocommerce_evaluate_shipping_cost_args', $args, $sum, $this );
    $locale         = localeconv();
    $decimals       = array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], ',' );
    $this->fee_cost = $args['cost'];

    // Expand shortcodes.
    add_shortcode( 'fee', array( $this, 'fee' ) );

    $sum = do_shortcode(
      str_replace(
        array(
          '[qty]',
          '[cost]',
        ),
        array(
          $args['qty'],
          $args['cost'],
        ),
        $sum
      )
    );

    remove_shortcode( 'fee', array( $this, 'fee' ) );

    // Remove whitespace from string.
    $sum = preg_replace( '/\s+/', '', $sum );

    // Remove locale from string.
    $sum = str_replace( $decimals, '.', $sum );

    // Trim invalid start/end characters.
    $sum = rtrim( ltrim( $sum, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

    // Do the math.
    return $sum ? WC_Eval_Math::evaluate( $sum ) : 0;
  }

  /**
   * Work out fee (shortcode).
   *
   * @param  array $atts Attributes.
   * @return string
   */
  public function fee( $atts ) {
    $atts = shortcode_atts(
      array(
        'percent' => '',
        'min_fee' => '',
        'max_fee' => '',
      ), $atts, 'fee'
    );

    $calculated_fee = 0;

    if ( $atts['percent'] ) {
      $calculated_fee = $this->fee_cost * ( floatval( $atts['percent'] ) / 100 );
    }

    if ( $atts['min_fee'] && $calculated_fee < $atts['min_fee'] ) {
      $calculated_fee = $atts['min_fee'];
    }

    if ( $atts['max_fee'] && $calculated_fee > $atts['max_fee'] ) {
      $calculated_fee = $atts['max_fee'];
    }

    return $calculated_fee;
  }

  /**
   * Calculate the shipping costs.
   *
   * @param array $package Package of items from cart.
   */
  public function calculate_shipping( $package = array() ) {
    $rate = array(
      'id'      => $this->get_rate_id(),
      'label'   => $this->title,
      'cost'    => 0,
      'package' => $package,
    );

    // Calculate the costs.
    $has_costs = false; // True when a cost is set. False if all costs are blank strings.
    $cost      = $this->get_option( 'cost' );

    if ( '' !== $cost ) {
      $has_costs    = true;
      $rate['cost'] = $this->evaluate_cost(
        $cost, array(
          'qty'  => $this->get_package_item_qty( $package ),
          'cost' => $package['contents_cost'],
        )
      );
    }

    // Base on postcode;
    $postcode = $package['destination']['postcode'];
    $cost = $this->get_shipping_cost_by_postcode($postcode);
    if (!empty($cost)) {
      $rate['cost'] = $cost;
    }
    if ( $has_costs ) {
      $this->add_rate( $rate );
    }
  }

  /**
   * Get shipping code by post code.
   * @param string $postcode PostCode.
   * @return int.
   */
  public function get_shipping_cost_by_postcode($postcode) {
    $postcode_terms = array('123', '456', '789');
    if (!in_array($postcode, $postcode_terms)) {
      return '';
    }
    $cost = 15;
    return $cost;
  }


  /**
   * Get items in package.
   *
   * @param  array $package Package of items from cart.
   * @return int
   */
  public function get_package_item_qty( $package ) {
    $total_quantity = 0;
    foreach ( $package['contents'] as $item_id => $values ) {
      if ( $values['quantity'] > 0 && $values['data']->needs_shipping() ) {
        $total_quantity += $values['quantity'];
      }
    }
    return $total_quantity;
  }

  /**
   * Finds and returns shipping classes and the products with said class.
   *
   * @param mixed $package Package of items from cart.
   * @return array
   */
  public function find_shipping_classes( $package ) {
    $found_shipping_classes = array();

    foreach ( $package['contents'] as $item_id => $values ) {
      if ( $values['data']->needs_shipping() ) {
        $found_class = $values['data']->get_shipping_class();

        if ( ! isset( $found_shipping_classes[ $found_class ] ) ) {
          $found_shipping_classes[ $found_class ] = array();
        }

        $found_shipping_classes[ $found_class ][ $item_id ] = $values;
      }
    }
    return $found_shipping_classes;
  }

  /**
   * Sanitize the cost field.
   *
   * @since 3.4.0
   * @param string $value Unsanitized value.
   * @return string
   */
  public function sanitize_cost( $value ) {
    $value = is_null( $value ) ? '' : $value;
    $value = wp_kses_post( trim( wp_unslash( $value ) ) );
    $value = str_replace( array( get_woocommerce_currency_symbol(), html_entity_decode( get_woocommerce_currency_symbol() ) ), '', $value );
    return $value;
  }
}
