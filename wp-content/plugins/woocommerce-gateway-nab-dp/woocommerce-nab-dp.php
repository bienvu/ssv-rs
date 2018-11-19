<?php
/*
 * Plugin Name: WooCommerce NAB Transact Gateway
 * Plugin URI: http://woothemes.com/woocommerce
 * Woo: 18684:b2c5b885ace10577c515dc881fcbb62c
 * Description: Use NAB Transact (National Australia Bank) as a credit card processor for WooCommerce.  
 * Supports both V1 and V2 of the Direct Post API, API XML, Subscriptions, Refunds, UnionPay Online  
 * Payments and Risk Management.
 * Version: 2.0.7
 * Author: Tyson Armstrong
 * Author URI: http://work.tysonarmstrong.com/
 * 
 * Copyright: © 2012-2017 Tyson Armstrong
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'b2c5b885ace10577c515dc881fcbb62c', '18684' );

add_action('plugins_loaded', 'woocommerce_nab_dp_init', 0);

function woocommerce_nab_dp_init() {

	if (!class_exists('WC_Payment_Gateway'))  return;

	class WC_Gateway_NAB_Direct_Post extends WC_Payment_Gateway_CC {

		private static $log;

		public function __construct() {
			global $woocommerce;

		    $this->id 					= 'nab_dp';
		    $this->method_title 		= __('NAB Transact', 'woothemes');
			$this->method_description 	= __('Use NAB Transact to process payments in your WooCommerce store.', 'woothemes');
			$this->icon 				= WP_PLUGIN_URL . "/" . plugin_basename( dirname(__FILE__)) . '/images/nab_small.jpg';
			$this->supports 			= array('products','subscriptions','subscription_cancellation','subscription_suspension','subscription_reactivation','subscription_amount_changes','subscription_date_changes','subscription_payment_method_change','subscription_payment_method_change_customer','subscription_payment_method_change_admin','multiple_subscriptions','refunds');

		    // Load the form fields.
		    $this->init_form_fields();

		    // Load the settings.
		    $this->init_settings();

		    if (isset($this->settings['api']) && $this->settings['api'] === 'xmlapi') {
		    	$this->supports[] = 'default_credit_card_form';

		    	// Add Card Name field to credit card form
				add_filter('woocommerce_credit_card_form_fields', array($this,'add_card_name_field'));
				add_action( 'wp_enqueue_scripts', array($this,'add_card_name_field_styles'));
		    }

		 	if ($this->settings['testmode'] == 'yes') {
		 		if (isset($this->settings['api_version']) && $this->settings['api_version'] == 'V2') {
		 			$this->payurl = 'https://demo.transact.nab.com.au/directpostv2/authorise';
				} else {
					$this->fingerprinturl = 'https://demo.transact.nab.com.au/directpost/genfingerprint'; // ?
					$this->payurl = 'https://demo.transact.nab.com.au/directpost/authorise';
				}
				$this->crnfingerprinturl = 'https://demo.transact.nab.com.au/directpost/crnfingerprint';
				$this->crnurl = 'https://demo.transact.nab.com.au/directpost/crnmanage';
				$this->crnxmlapiurl = 'https://demo.transact.nab.com.au/xmlapi/periodic';
				$this->xmlapiurl = 'https://demo.transact.nab.com.au/xmlapi/payment';
				$this->riskapiurl = 'https://demo.transact.nab.com.au/riskmgmt/payment';
		    } else {
		    	if (isset($this->settings['api_version']) && $this->settings['api_version'] == 'V2') {
		    		$this->payurl = 'https://transact.nab.com.au/live/directpostv2/authorise';
				} else {
					$this->fingerprinturl = 'https://transact.nab.com.au/live/directpost/genfingerprint';
					$this->payurl = 'https://transact.nab.com.au/live/directpost/authorise';
				}
				$this->crnfingerprinturl = 'https://transact.nab.com.au/live/directpost/crnfingerprint';
				$this->crnurl = 'https://transact.nab.com.au/live/directpost/crnmanage';
				$this->crnxmlapiurl = 'https://transact.nab.com.au/xmlapi/periodic';
				$this->xmlapiurl = 'https://transact.nab.com.au/live/xmlapi/payment';
				$this->riskapiurl = 'https://transact.nab.com.au/riskmgmt/payment';
		    }

		    // Define user set variables
		    $this->title = $this->settings['title'];
		    $this->paymentmethods = 'Visa, Mastercard';
		    if ($this->settings['accept_amex'] == 'yes') $this->paymentmethods .= ', American Express';
		    if ($this->settings['accept_diners'] == 'yes') $this->paymentmethods .= ', Diners Club';
		    if ($this->settings['accept_jcb'] == 'yes') $this->paymentmethods .= ', JCB';
		    if (isset($this->settings['api_version']) && $this->settings['api_version'] == 'V2' && $this->settings['accept_upop'] == 'yes') $this->paymentmethods .= ', UnionPay';
		    $this->description = 'Credit cards accepted: '.$this->paymentmethods;
		    


   		 	// Hooks
			if (!isset($this->settings['api']) || $this->settings['api'] === 'directpost') { add_action( 'woocommerce_receipt_nab_dp', array(&$this, 'receipt_page') );
			}
			add_action( 'woocommerce_api_wc_gateway_nab_direct_post', array(&$this, 'relay_response'));

			// Save admin options
			add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) ); // 1.6.6
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) ); // 2.0.0

			add_action( 'woocommerce_order_status_on-hold_to_processing', array( &$this, 'capture_payment' ) );
			add_action( 'woocommerce_order_status_on-hold_to_completed', array( &$this, 'capture_payment' ) );
			add_action( 'woocommerce_order_status_on-hold_to_cancelled', array( &$this, 'cancel_payment' ) );
			add_action( 'woocommerce_order_status_on-hold_to_refunded', array( &$this, 'cancel_payment' ) );
		

			// Additional tasks if Subscriptions is installed
			if (class_exists('WC_Subscriptions_Order')) {

				add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'scheduled_subscription_payment' ), 10, 2 );
				add_filter( 'wcs_resubscribe_order_created', array( $this, 'delete_resubscribe_meta' ), 10 );
				add_action( 'woocommerce_subscription_failing_payment_method_updated_'.$this->id, array( $this, 'update_failing_payment_method' ), 10, 2 );

				// Allow store managers to manually set Simplify as the payment method on a subscription
				add_filter( 'woocommerce_subscription_payment_meta', array( $this, 'add_subscription_payment_meta' ), 10, 2 );
				add_filter( 'woocommerce_subscription_validate_payment_meta', array( $this, 'validate_subscription_payment_meta' ), 10, 2 );

				// Filter acceptable order statuses to allow changing of payment gateway with DP-style gateways
				add_filter('woocommerce_valid_order_statuses_for_payment', array( $this, 'allow_payment_method_change'), 10, 2);

			}

		}


		/**
	     * Initialise Gateway Settings Form Fields
		 *
		 * @since 1.0.0
	     */
		function init_form_fields() {
			$this->form_fields = array(
			    'enabled' => array(
			        'title' => __( 'Enable/Disable', 'woothemes' ),
			        'type' => 'checkbox',
			        'label' => __( 'Enable this payment method', 'woothemes' ),
			        'default' => 'yes'
			    ),
			    'title' => array(
			        'title' => __( 'Title', 'woothemes' ),
			        'type' => 'text',
			        'description' => __( 'This controls the title which the user sees during checkout.', 'woothemes' ),
			        'default' => __( 'NAB Transact', 'woothemes' )
			    ),
			    'api' => array(
			        'title' => __( 'Integration Method', 'woothemes' ),
			        'type' => 'select',
			        'options' => array('directpost'=>'Direct Post','xmlapi'=>'XML API'),
			        'description' => __( '<strong>Direct Post</strong> is safer and easier. <strong>XML API</strong> allows customers to enter their credit card details on the checkout page however you must be <a href="https://docs.woocommerce.com/document/pci-dss-compliance-and-woocommerce/" target="_blank">PCI-DSS compliant</a> as the credit card details are transmitted to your server.', 'woothemes' ),
			        'default' => __( 'directpost', 'woothemes' )
			    ),	
			    'api_version' => array(
			        'title' => __( 'API Version', 'woothemes' ),
			        'type' => 'select',
			        'options' => array('V1'=>'V1','V2'=>'V2'),
			        'description' => __( 'V1 supports fewer features.', 'woothemes' ),
			        'default' => __( 'V1', 'woothemes' ),
			        'class' => 'for-directpost'
			    ),			    
				'testmode' => array(
					'title' => __( 'Test mode', 'woothemes' ),
					'label' => __( 'Enable Test mode', 'woothemes' ),
					'type' => 'checkbox',
					'description' => __( 'Process transactions in Test mode. No transactions will actually take place.', 'woothemes' ),
					'default' => 'yes'
				),
				'client_id' => array(
					'title' => __( 'NAB Client ID', 'woothemes' ),
					'type' => 'text',
					'description' => __( 'The Client ID will be of the format "ABC0010", where ABC is your unique three-letter account code.', 'woothemes' ),
					'default' => ''
				),
				'nab_password' => array(
					'title' => __( 'NAB Password', 'woothemes' ),
					'type' => 'password',
					'description' => __( 'Your merchant password is for payment authentication only.', 'woothemes' ),
					'default' => ''
				),
				'reference_prefix' => array(
					'title' => __( 'Reference ID prefix', 'woothemes' ),
					'type' => 'text',
					'description' => __( 'If set, the transaction reference in NAB will be this text and the order number. 20 characters max.', 'woothemes' ),
					'default' => 'WooCommerce',
					'custom_attributes' => array(
						'maxlength'=> '20'
						)
				),
				'capture' => array(
					'title'       => __( 'Capture', 'woothemes' ),
					'label'       => __( 'Capture charge immediately', 'woothemes' ),
					'type'        => 'checkbox',
					'description' => __( 'Whether or not to immediately capture the charge. When unchecked, the charge issues a pre-authorization only and the funds will need to be captured later. Uncaptured charges expire after 5 working days. Preauth cannot be used in conjunction with both Risk Management and XML API.', 'woothemes' ),
					'default'     => 'yes'
				),
				'risk_management' => array(
					'title' => __( 'Risk management', 'woothemes' ),
					'label' => __( 'Enable risk management feature', 'woothemes' ),
					'type' => 'checkbox',
					'description' => __( '<strong>V2 only.</strong> This feature must be enabled by NAB Transact.', 'woothemes' ),
					'default' => 'yes'
				),
				/*'3d_secure' => array(
					'title' => __( '3D Secure', 'woothemes' ),
					'label' => __( 'Enable 3D Secure feature', 'woothemes' ),
					'type' => 'checkbox',
					'description' => __( '<strong>V2 only.</strong> This feature must be enabled by NAB Transact.', 'woothemes' ),
					'default' => 'yes'
				),
				'3d_secure_number' => array(
					'title' => __( 'Your NAB EB number', 'woothemes' ),
					'type' => 'text',
					'description' => __( '<strong>V2 only.</strong> Used only when you have 3D Secure enabled, this is your online merchant number specified by your bank which has been registered for Verified by VISA or SecureCode (or both). This will be your NAB EB number, e.g. “22123456”.', 'woothemes' ),
					'default' => ''
				), */
				'accept_upop' => array(
					'title' => __( 'Accept UnionPay Online Payments', 'woothemes' ),
					'label' => __( 'Accept UnionPay', 'woothemes' ),
					'type' => 'checkbox',
					'description' => __( '<strong>Direct Post and V2 only.</strong> Contact NAB to activate UnionPay Online Payments on on your account.', 'woothemes' ),
					'default' => 'no',
					'class' => 'for-directpost'
				),
				'accept_amex' => array(
					'title' => __( 'Accept American Express', 'woothemes' ),
					'label' => __( 'Accept American Express cards', 'woothemes' ),
					'type' => 'checkbox',
					'description' => __( 'Call 1300 363 614 to activate American Express on your account.', 'woothemes' ),
					'default' => 'no'
				),
				'accept_diners' => array(
					'title' => __( 'Accept Diners Club', 'woothemes' ),
					'label' => __( 'Accept Diners Club cards', 'woothemes' ),
					'type' => 'checkbox',
					'description' => __( 'Call 1300 360 500 to activate Diners Club on your account.', 'woothemes' ),
					'default' => 'no'
				),
				'accept_jcb' => array(
					'title' => __( 'Accept JCB', 'woothemes' ),
					'label' => __( 'Accept JCB cards', 'woothemes' ),
					'type' => 'checkbox',
					'description' => __( 'Call 1300 363 614 to activate JCB on your account.', 'woothemes' ),
					'default' => 'no'
				)
			);
		} // End init_form_fields()

		/**
		 * Admin Panel Options
		 *
		 * @since 1.0.0
		 */
		public function admin_options() {

	    	?>
	    	<h3><?php _e('NAB Transact Credit Card Payment', 'wc-nab'); ?></h3>
	    	<p><?php _e('Using the NAB Transact Direct Post payment gateway.', 'wc-nab'); ?></p>
	    	<table class="form-table nab-dp-settings">
	    	<?php
	    		// Generate the HTML For the settings form.
	    		$this->generate_settings_html();
	    	?>
			</table><!--/.form-table-->

			<script type="text/javascript">
				function nab_dp_show_relevant_fields() {
					var api = jQuery('select#woocommerce_nab_dp_api').val();
					jQuery('table.nab-dp-settings tr').show();
					if (api !== 'directpost') {
						jQuery('.for-directpost').closest('tr').hide();
					}
				}
				nab_dp_show_relevant_fields();
				jQuery('select#woocommerce_nab_dp_api').on('change', function() {
					nab_dp_show_relevant_fields();
				});
			</script>

	    	<?php
	    } // End admin_options()


	   	/**
		 * Payment fields (if using XML API)
		 *
		 * @since 1.0.0
		 */
		function payment_fields() {
			if ($this->description) echo '<p>'.$this->description.'</p>';

			if (!isset($this->settings['api']) || $this->settings['api'] === 'directpost') return;

			// Payment form
			if ($this->settings['testmode']=='yes') : ?><p><?php _e('TEST MODE ENABLED', 'woothemes'); ?></p><p><strong>Note:</strong> When in test mode, only amounts in ending in .00 or .08 cents will be approved. All other amounts will be declined for testing.</p><?php endif;

			if ( $this->supports( 'tokenization' ) && is_checkout() ) {
	            $this->tokenization_script();
	            $this->saved_payment_methods();
	            $this->form();
	            $this->save_payment_method_checkbox();
	        } else {
	            $this->form();
	        }
		}


		/**
		 * Process the payment and return the result
		 * - redirects the customer to the pay page
		 *
		 * @since 1.0.0
		 */
		function process_payment( $order_id ) {

			global $woocommerce;

			$order = new WC_Order( $order_id );

			if (isset($this->settings['api']) && $this->settings['api'] === 'xmlapi') { // For XML API

				// If this is a subscription, let's call CRN first to save the customer
				if ($this->order_has_subscription($order_id)) {

					// Build reference ID
					$referenceid = $this->get_reference_id($order_id);
					$this->save_reference_id($order_id,$referenceid);

					$data = array(
						'cardnumber' => intval(str_replace(' ','',$_POST['nab_dp-card-number'])),
						'expiry' => str_replace(' ','',$_POST['nab_dp-card-expiry']),
						'reference' => $referenceid
					);
					$crnxml = $this->generateAddCRNXMLMessage($data);

					$crn_result = $this->send($crnxml,$this->crnxmlapiurl);

					$result_object = simplexml_load_string($crn_result);

					if (!isset($result_object->Periodic->PeriodicList->PeriodicItem->successful) || (string) $result_object->Periodic->PeriodicList->PeriodicItem->successful !== "yes") {
						
						if (isset($result_object->Status->statusDescription)) {
							$msg = (string) $result_object->Status->statusDescription;
							$code = (string) $result_object->Status->statusCode;	
						} else {
							$msg = (string) $result_object->Periodic->PeriodicList->PeriodicItem->responseText;
							$code = (string) $result_object->Periodic->PeriodicList->PeriodicItem->responseCode;
						}
						
						// Failed to save CRN
						$order->add_order_note(sprintf(__('Order #%s: Saving CRN to NAB Transact failed: %s [%s].', 'woothemes'), $order_id, $msg, $code));
						wc_add_notice(sprintf(__('Saving CRN to NAB Transact failed: %s [%s].', 'woothemes'), $msg, $code));
						return;	
					} else {
						$this->save_subscription_meta($order_id, (string) $result_object->Periodic->PeriodicList->PeriodicItem->crn);
						$order->add_order_note(__('Saved CRN to NAB Transact for subscription payments.', 'woothemes'));
					}

				}


				// Because this COULD have been a subscription with $0 up-front, do we actually need to process a payment?

				if ($order->get_total() == 0) {

					$order->add_order_note(__('No payment requested as order total is $0.','woothemes'));
			    	$order->payment_complete();
			    	$woocommerce->cart->empty_cart();

				} else {

					// Build reference ID
					$referenceid = $this->get_reference_id($order_id);
					$this->save_reference_id($order_id,$referenceid);

					if (in_array(get_woocommerce_currency(),array('BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','VND','VUV','XAF','XOF','XPF'))) {
						$amount = $order->get_total();
					} else {
						$amount = number_format( (float)$order->get_total() * 100, 0, '.', '' );
					}

					$data = array(
						'cardnumber' => intval(str_replace(' ','',$_POST['nab_dp-card-number'])),
						'expiry' => str_replace(' ','',$_POST['nab_dp-card-expiry']),
						'name' => $_POST['nab_dp-card-name'],
						'amountcents' => $amount,
						'reference' => $referenceid,
						'currency' => get_woocommerce_currency(),
						'capture' => 1,
						'first_name' => $order->get_billing_first_name(),
						'last_name' => $order->get_billing_last_name(),
						'postcode' => $order->get_billing_postcode(),
						'city' => $order->get_billing_city(),
						'country' => $order->get_billing_country(),
						'delivery_country' => $order->get_shipping_country(),
						'email' => $order->get_billing_email()
					);
					
					if (isset($this->settings['capture']) && $this->settings['capture'] == 'no') {
						// Auth only
						$data['capture'] = 0;
					}
					

					$payxml = $this->generateCardPaymentXMLMessage($data);
					if (isset($this->settings['risk_management']) && $this->settings['risk_management'] === 'yes') {
						$payment_result = $this->send($payxml,$this->riskapiurl);
					} else {
						$payment_result = $this->send($payxml,$this->xmlapiurl);
					}
					
					$result_object = simplexml_load_string($payment_result);
					
					if (!isset($result_object->Status->statusCode) || (string) $result_object->Status->statusCode !== '000') {
						// Failure
						$order->add_order_note(sprintf(__('NAB Transact payment failed: %s.', 'woothemes'), (string) $result_object->Status->statusDescription));
						wc_add_notice( __('Error processing payment: ', 'woothemes') . (string) $result_object->Status->statusDescription, 'error' );
						return;					
					}
					
					if (!isset($result_object->Payment->TxnList->Txn->approved) || (string) $result_object->Payment->TxnList->Txn->approved !== "Yes") {
						// Payment denied
						$order->add_order_note(sprintf(__('NAB Transact payment failed: %s. (ref ID: %s)', 'woothemes'), (string) $result_object->Payment->TxnList->Txn->responseText, (string) $result_object->Payment->TxnList->Txn->txnID));
						wc_add_notice( __('Payment denied: ', 'woothemes') . (string) $result_object->Payment->TxnList->Txn->responseText . ' ['. (string) $result_object->Payment->TxnList->Txn->responseCode.']', 'error' );
						return;	
					}

					// Check for risk management failure
					$riskmanagementmsg = '';
					if (isset($this->settings['risk_management']) && $this->settings['risk_management'] === 'yes') {
						if (isset($result_object->Payment->TxnList->Txn->antiFraudResponseCode) && (string) $result_object->Payment->TxnList->Txn->antiFraudResponseCode !== "000") {
							// Failed risk management
							$order->add_order_note(sprintf(__('NAB Transact payment failed Risk Management assessment: %s. (ref ID: %s)', 'woothemes'), (string) $result_object->Payment->TxnList->Txn->antiFraudResponseText, (string) $result_object->Payment->TxnList->Txn->txnID));
							wc_add_notice( __('Payment denied: ', 'woothemes') . (string) $result_object->Payment->TxnList->Txn->antiFraudResponseText . ' ['. (string) $result_object->Payment->TxnList->Txn->antiFraudResponseCode.']', 'error' );
							return;	
						} else {
							$riskmanagementmsg = 'Payment passed Risk Management assessment.';
						}
					}

					// Success
					if (isset($result_object->Payment->TxnList->Txn->preauthID) && strlen((string) $result_object->Payment->TxnList->Txn->preauthID) > 0) {
						// Preauth only
						update_post_meta( $order->id, '_nabdp_preauthid', (string) $result_object->Payment->TxnList->Txn->preauthID );
						update_post_meta( $order->id, '_nabdp_preauth_captured', 'no');
						update_post_meta( $order->id, '_nabdp_referenceid', (string) $result_object->Payment->TxnList->Txn->purchaseOrderNo);

						if ( $order->has_status( array( 'pending', 'failed' ) ) ) {
							$order->reduce_order_stock();
						}

						$order->update_status( 'on-hold', sprintf( __( 'NAB charge pre-authorized (preauth ID: %s). Change order to Processing or Completed to take payment. %s', 'woothemes' ), (string) $result_object->Payment->TxnList->Txn->preauthID, $riskmanagementmsg ) );
						$this->log( sprintf(__("Order %s: Successful pre-authorization (preauthid: %s) for transaction %s. %s" , 'wc-nab'), $order->get_order_number(), (string) $result_object->Payment->TxnList->Txn->preauthID, (string) $result_object->Payment->TxnList->Txn->txnID, $riskmanagementmsg));
					} else {
						// Full capture
						$order->add_order_note(sprintf(__('NAB Transact payment on card %s approved on %s. Reference ID: %s. %s','woothemes'), (string) $result_object->Payment->TxnList->Txn->CreditCardInfo->pan, (string) $result_object->Payment->TxnList->Txn->settlementDate, (string) $result_object->Payment->TxnList->Txn->txnID, $riskmanagementmsg));
			    		$order->payment_complete((string) $result_object->Payment->TxnList->Txn->txnID);
			    		$woocommerce->cart->empty_cart();
					}

				}
	    		

	    		if (!defined('WC_VERSION')) {
	    			$redirect = add_query_arg('key', $order->order_key, add_query_arg('order', $order_id, get_permalink(get_option('woocommerce_thanks_page_id'))));
	    		} else { // WC 2.1+
	    			$redirect = $this->get_return_url($order);
	    		}
                return array(
                    'result' => 'success',
                    'redirect' => $redirect
                );

			} else { // For Direct Post

				$redirect = $order->get_checkout_payment_url( true );
				// Check if this is a payment change, and if so, add a query arg for later
				if (class_exists('WC_Subscriptions_Change_Payment_Gateway')) {
					$is_payment_change = WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment;
					if ($is_payment_change) $redirect = add_query_arg('is_payment_change',1,$redirect);
				}

				return array(
					'result' 	=> 'success',
					'redirect'	=> $redirect
				);
			}
		}

		/**
         * Add a Card Name field to the default WooCommerce checkout form (and make it first)
         */
        function add_card_name_field($default_fields) {
        	$fields = array_merge(array('card-name-field' => '<p class="form-row form-row-wide">
				<label for="' . esc_attr( $this->id ) . '-card-name">' . __( 'Name on card', 'woocommerce' ) . ' <span class="required">*</span></label>
				<input id="' . esc_attr( $this->id ) . '-card-name" class="input-text wc-credit-card-form-card-name" type="text" autocomplete="off" placeholder="" name="' . $this->id . '-card-name' . '" />
			</p>'),$default_fields);
        	return $fields;
        }

        function add_card_name_field_styles() {
        	if (is_checkout()) {
				wp_register_style( 'woocommerce-nab-dp', plugin_dir_url(__FILE__) . 'woocommerce-nab-dp.css' );
				wp_enqueue_style( 'woocommerce-nab-dp' );
			}
        }

		/**
		 * Send post data to a https url
		 * Used to get the fingerprint
		 *
		 * @since 1.0.0
		 */
	  	function send($packet, $url) {
	  		if (is_array($packet)) {
	  	  		$packet = http_build_query($packet);
	  	  	}

		  	$response = wp_remote_post( $url, array(
				'method' => 'POST',
				'timeout' => 45,
				'body' => $packet
			    )
			);

			if( ! is_wp_error( $response ) ) {
			   return $response['body'];
			}
		}

		function get_reference_id($order_id) {
			
			$order = new WC_Order( $order_id );
			$order_no = $order->get_order_number();

			if ($this->settings['reference_prefix'] !== '') {
				$reference_id = str_replace(array('-','_','#'),'',substr($this->settings['reference_prefix'].$order_no,0,32));
			} else {
				$reference_id = str_replace(array('-','_','#'),'',substr(urlencode(time().$order_no),0,32));
			}
			// Changes to reference_id to satisfy UPOP if enabled
            if ($this->settings['accept_upop'] == "yes" && (empty($this->settings['api']) || $this->settings['api'] === "directpost" || !isset($this->settings['api']))) {
            	$reference_id = $reference_id.'UPOP'.rand(100,999); // Unique
                $reference_id = preg_replace("/[^a-zA-Z0-9]+/", "", $reference_id); // Alphanumeric
                $reference_id = str_pad($reference_id,8,'0'); // Min 8 chars
                $reference_id = substr($reference_id,0,32); // Max 32 chars
            }
			$reference_id = apply_filters( 'woocommerce_'.$this->id.'_reference_id', $reference_id, $order, $this->settings );
			return $reference_id;
		}

		function save_reference_id($order_id,$ref) {
			update_post_meta($order_id,'_nab_reference_id',$ref);
		}

		/**
		 * Collect the credit card details on the pay page and post
		 * to NAB Transact
		 * - includes fingerprint generation
		 *
		 * @since 1.0.0
		 */
		function receipt_page($order_id) {
			global $woocommerce;
			// Get the order
			$order = new WC_Order( $order_id );

			// Payment form
			if ($this->settings['testmode']=='yes') : ?><p><?php _e('TEST MODE ENABLED', 'woothemes'); ?></p><p><strong>Note:</strong> When in test mode, only amounts in ending in .00 or .08 cents will be approved. All other amounts will be declined for testing.</p><?php endif;

			$timestamp = gmdate('YmdHis');
			if (version_compare( WC_VERSION, '2.7', '<' )) {
				$order_key = $order->order_key;
			} else {
				$order_key = $order->get_order_key();
			}

			$reference_id = $this->get_reference_id($order_id);
			$this->save_reference_id($order_id,$reference_id);

			if (in_array(get_woocommerce_currency(),array('BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','VND','VUV','XAF','XOF','XPF'))) {
				$amount = $order->get_total();
			} else {
				$amount = number_format($order->get_total(),2,'.','');
			}
			
			$is_payment_change = (isset($_GET['is_payment_change']) && $_GET['is_payment_change'] == '1');
			if ($is_payment_change) {
				$amount = 0;
				update_post_meta($order_id,'_is_mid_change_method',true);
			}

			$is_crn = 0;

			if ($this->order_has_subscription($order_id)) {
				// Get the total initial payment if using older v1.5 of Subscriptions
				if ($this->order_has_subscription($order_id) == 1) {
					$amount = WC_Subscriptions_Order::get_total_initial_payment( $order );
				}
				if ($is_payment_change) $amount = 0;
				$is_crn = 1;
				$eps_crn = 'WOO'.$order_id.'-'.time();
				$fingerprint = $this->generate_fingerprint('CRN',array('timestamp'=>$timestamp,'eps_crn'=>$eps_crn));
			} elseif ($this->settings['api_version'] == 'V2') {

				// Determine the transaction type to pass based on functionality
				if (isset($this->settings['risk_management']) && $this->settings['risk_management'] == 'yes' && isset($this->settings['3d_secure']) && $this->settings['3d_secure'] == 'yes') {
					$txntype = 6;
				} elseif (isset($this->settings['risk_management']) && $this->settings['risk_management'] == 'yes' && (!isset($this->settings['3d_secure']) || $this->settings['3d_secure'] == 'no')) {
					$txntype = 2;
				} elseif ((!isset($this->settings['risk_management']) || $this->settings['risk_management'] == 'no') && isset($this->settings['3d_secure']) && $this->settings['3d_secure'] == 'yes') {
					$txntype = 4;
				} else {
					$txntype = 0;
				}
				if (isset($this->settings['capture']) && $this->settings['capture'] == 'no') {
					$txntype++;
				}

				$fingerprint = $this->generate_fingerprint('V2',array('amount'=>$amount,'timestamp'=>$timestamp,'txntype'=>$txntype,'reference_id'=>$reference_id));
				$upop_fingerprint = $this->generate_fingerprint('UPOP',array('amount'=>$amount,'timestamp'=>$timestamp,'reference_id'=>$reference_id,'txntype'=>$txntype));

			} else {
				$fingerprint = $this->generate_fingerprint('V1',array('timestamp'=>$timestamp,'amount'=>$amount,'reference_id'=>$reference_id));
			}

			if (version_compare( WC_VERSION, '2.7', '<' )) {
				$order_key = $order->order_key;
			} else {
				$order_key = $order->get_order_key();
			}
			

			$this->result_url = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_Nab_Direct_Post', home_url('/') ) );
			$this->result_url = add_query_arg('order',$order_id,$this->result_url);
			$this->result_url = add_query_arg('key',$order_key,$this->result_url);
			$this->result_url = add_query_arg('is_crn',$is_crn,$this->result_url);

			if ($is_payment_change) $this->result_url = add_query_arg('is_payment_change',1,$this->result_url);

			if (get_option('woocommerce_force_ssl_checkout')=='yes' || is_ssl()) $this->result_url = str_replace('http:', 'https:', $this->result_url);
			if ($this->description) : ?><p><?php echo $this->description;
			//if (!$is_crn && $this->settings['api_version'] == 'V2' && $this->settings['accept_upop'] == 'yes') echo ', UnionPay Online Payments'; ?></p><?php endif; ?>
			<p class="woocommerce-error" id="nab_error_message" style="display:none;"></p>
			<form method="POST" action="<?php echo ($is_crn) ? $this->crnurl : $this->payurl; ?>" class="nab_payment_form">
			<?php // Fields always required ?>
			<input type="hidden" name="EPS_MERCHANT" value="<?php echo $this->settings['client_id']; ?>" />
			<input type="hidden" name="EPS_TIMESTAMP" value="<?php echo $timestamp; ?>" />
			<input type="hidden" name="EPS_RESULTURL" value="<?php echo $this->result_url; ?>" />
			<input type="hidden" name="EPS_FINGERPRINT" value="<?php echo urlencode($fingerprint); ?>" data-fingerprint="<?php echo urlencode($fingerprint); ?>" <?php if (isset($upop_fingerprint)) echo 'data-upop-fingerprint="'.$upop_fingerprint.'"'; ?> />
			<?php // Although EPS_REDIRECT isn't in the NAB documentation,
			// it is required to maintain the query variables in EPS_RESULTURL.
			// It seems like a bug in NAB's system ?>
			<input type="hidden" name="EPS_REDIRECT" value="true" />

			<input type="hidden" name="EPS_CURRENCY" value="<?php echo get_woocommerce_currency(); ?>" />

			<?php // IF A CRN CALL 
			if ($is_crn) { ?>
				<input type="hidden" name="EPS_TYPE" value="CRN" />
				<input type="hidden" name="EPS_ACTION" value="ADDCRN" />
				<input type="hidden" name="EPS_CRN" value="<?php echo $eps_crn; ?>" />
			<?php }// IF A V2 PAYMENT
			if ($this->settings['api_version'] == 'V2' && !$is_crn) { 
				echo '<input type="hidden" name="EPS_TXNTYPE" value="'.$txntype.'" />';
				if (isset($this->settings['risk_management']) && $this->settings['risk_management'] == 'yes') {
					if (version_compare( WC_VERSION, '2.7', '<' )) {
						$firstname = $order->billing_first_name;
						$lastname = $order->billing_last_name;
						$postcode = $order->billing_postcode;
						$country = $order->billing_country;
						$shippingcountry = $order->shipping_country;	
						$billingemail = $order->billing_email;
					} else {
						$firstname = $order->get_billing_first_name();
						$lastname = $order->get_billing_last_name();
						$postcode = $order->get_billing_postcode();
						$country = $order->get_billing_country();	
						$shippingcountry = $order->get_shipping_country();	
						$billingemail = $order->get_billing_email();
					}
					echo '<input type="hidden" name="EPS_FIRSTNAME" value="'.$firstname.'" />';
					echo '<input type="hidden" name="EPS_LASTNAME" value="'.$lastname.'" />';
					if ($this->get_user_ip()) {
						echo '<input type="hidden" name="EPS_IP" value="'.$this->get_user_ip().'" />';
					}
					echo '<input type="hidden" name="EPS_ZIPCODE" value="'.$postcode.'" />';
					echo '<input type="hidden" name="EPS_BILLINGCOUNTRY" value="'.$country.'" />';
					if ($shippingcountry) {
						echo '<input type="hidden" name="EPS_DELIVERYCOUNTRY" value="'.$shippingcountry.'" />';
					}
					echo '<input type="hidden" name="EPS_EMAILADDRESS" value="'.$billingemail.'" />';
				} 
				if (isset($this->settings['3d_secure']) && $this->settings['3d_secure'] == 'yes') {
					echo '<input type="hidden" name="3D_XID" value="'.str_pad($timestamp,20,'0').'" />';
					echo '<input type="hidden" name="EPS_MERCHANTNUM" value="'.$this->settings['3d_secure_number'].'" />';
				}
			}

			if (!$is_crn) { ?>
				<input type="hidden" name="EPS_AMOUNT" value="<?php echo $amount; ?>" />
				<input type="hidden" name="EPS_REFERENCEID" value="<?php echo $reference_id; ?>" />
			<?php } ?>	

			<input type="hidden" name="EPS_CARDTYPE" value="" id="jsCardType" />
			<?php if ($this->settings['accept_upop'] == 'yes' && $this->settings['api_version'] == 'V2' && !$is_crn) { ?>
				<div>
					<input type="radio" name="EPS_PAYMENTCHOICE" id="nab_cardtype_vmc" class="input-radio cardtype_checking" checked="checked" value="" /> <label for="nab_cardtype_vmc"><?php echo sprintf(__("Pay with %s", 'wc-nab'),str_replace(', UnionPay','',$this->paymentmethods)); ?></label><br />
					<input type="radio" name="EPS_PAYMENTCHOICE" id="nab_cardtype_upop" class="input-radio cardtype_checking" value="UPOP" /> <label for="nab_cardtype_upop"><?php _e("Pay with UnionPay", 'wc-nab'); ?></label>
				</div><br />
			<?php } ?>
				<fieldset id="nab_card_details">
					<p class="form-row form-row-first">
						<label for="nab_card_number"><?php _e("Credit card number", 'wc-nab') ?> <span class="required">*</span></label>
						<input type="text" class="input-text" name="EPS_CARDNUMBER" id="nab_card_number" /><span id="jsCardType"></span>
					</p>
					<div class="clear"></div>
					<p class="form-row form-row-first">
						<label for="cc-expire-month"><?php _e("Expiration date", 'wc-nab') ?> <span class="required">*</span></label>
						<select name="EPS_EXPIRYMONTH" id="cc-expire-month">
							<option value=""><?php _e('Month', 'wc-nab') ?></option>
							<?php
								$months = array();
								for ($i = 1; $i <= 12; $i++) {
								    $timestamp = mktime(0, 0, 0, $i, 1);
								    $months[date('m', $timestamp)] = date('F', $timestamp);
								}
								foreach ($months as $num => $name) {
						            printf('<option value="%s">%s - %s</option>', $num,$num, $name);
						        }

							?>
						</select>
						<select name="EPS_EXPIRYYEAR" id="cc-expire-year">
							<option value=""><?php _e('Year', 'wc-nab') ?></option>
							<?php
								$years = array();
								for ($i = date('Y'); $i <= date('Y') + 15; $i++) {
									if ($is_crn) {
								    	printf('<option value="%u">%u</option>', substr($i,2), $i);
								    } else {
								    	printf('<option value="%u">%u</option>', $i, $i);
								    }
								}
							?>
						</select>
					</p>
					<p class="form-row form-row-last">
						<label for="nab_card_ccv"><?php _e("Card security code", 'wc-nab') ?> <span class="required">*</span></label>
						<input type="text" class="input-text" id="nab_card_ccv" name="EPS_CCV" maxlength="4" style="width:45px" />
						<span class="help nab_card_ccv_description"><?php _e('3 or 4 digits usually found on the signature strip.', 'wc-nab') ?></span>
					</p>
					<div class="clear"></div>
				</fieldset>
				<div class="upop_note" style="display: none;"><p><?php _e("You will be able to enter your UnionPay details on the next page.",'wc-nab'); ?></p></div>
				<input type="submit" id="jsPayButton" class="submit buy button" value="<?php _e('Confirm and pay','wc-nab'); ?>" />
				</form>
				<script type="text/javascript">
				jQuery(function(){

					jQuery('input#jsPayButton').on('click',function(e) {
						var number = jQuery('input#nab_card_number').val();
						number = number.replace(/[^0-9]/g, '');
						jQuery('input#nab_card_number').val(number);
						if (!validateFields(true)) {
							e.preventDefault();
							return false;
						} else {
							jQuery(this).attr('disabled','disabled');
							jQuery(this).block({
		                        message: null,
		                        overlayCSS: {
		                            background: '#fff',
		                            opacity: 0.6
		                        }
	                    	});
	                    	jQuery('form.nab_payment_form').submit();
	                    	console.log('submitted');
							return false;
						}
					});

					jQuery('input.cardtype_checking').on('change',function() {
				        if (jQuery('input.cardtype_checking#nab_cardtype_vmc').is(':checked')) {
				            jQuery('fieldset#nab_card_details').show();
				            jQuery('div.upop_note').hide();
				            jQuery('input[name="EPS_TXNTYPE"]').val(jQuery('input[name="EPS_TXNTYPE"]').data('vmc_value'));
				            jQuery('input[name="EPS_FINGERPRINT"]').val(jQuery('input[name="EPS_FINGERPRINT"]').attr('data-fingerprint'));
				            validateFields(false);
				        } else if (jQuery('input.cardtype_checking#nab_cardtype_upop').is(':checked')) {
				            jQuery('fieldset#nab_card_details').hide();
				            jQuery('div.upop_note').show();
				            jQuery('input[name="EPS_TXNTYPE"]').data('vmc_value',jQuery('input[name="EPS_TXNTYPE"]').val()).val('0');
				            jQuery('input[name="EPS_FINGERPRINT"]').val(jQuery('input[name="EPS_FINGERPRINT"]').attr('data-upop-fingerprint'));
				        }
				    });

					jQuery('input#nab_card_number').on('keyup input blur',function() {
						var number = jQuery(this).val();
						number = number.replace(/[^0-9]/g, '');
							var re = new RegExp("^4[0-9]{12}(?:[0-9]{3})?$");
           					if (number.match(re) != null) {
           					jQuery('span#jsCardType').html('<img src="<?php echo plugins_url( 'images/visa.png', __FILE__ ) ?>" alt="Visa detected" style="vertical-align: bottom;">');
				            jQuery('input#jsCardType').val('visa');
				            jQuery('input#nab_card_number').data('validated',true);
				            validateFields(false);
				            return;
           					}
							re = new RegExp("^(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}$");
				            if (number.match(re) != null) {
							jQuery('span#jsCardType').html('<img src="<?php echo plugins_url( 'images/mastercard.png', __FILE__ ) ?>" alt="Mastercard detected" style="vertical-align: bottom;">');
				            jQuery('input#jsCardType').val('mastercard');
				            jQuery('input#nab_card_number').data('validated',true);
				            validateFields(false);
				            return;
				            }
				            re = new RegExp("^3[47][0-9]{13}$");
				            if (number.match(re) != null) {
				            jQuery('span#jsCardType').html('<img src="<?php echo plugins_url( 'images/amex.png', __FILE__ ) ?>" alt="American Express detected" style="vertical-align: bottom;">');
				            jQuery('input#jsCardType').val('amex');
				            jQuery('input#nab_card_number').data('validated',true);
				            validateFields(false);
				            return;
				            }
				            re = new RegExp("^3(?:0[0-5]|[68][0-9])[0-9]{11}$");
				            if (number.match(re) != null) {
				            jQuery('span#jsCardType').html('<img src="<?php echo plugins_url( 'images/diners.png', __FILE__ ) ?>" alt="Diners Club card detected" style="vertical-align: bottom;">');
				            jQuery('input#jsCardType').val('dinersclub');
				            jQuery('input#nab_card_number').data('validated',true);
				            validateFields(false);
				            return;
				            }
				            re = new RegExp("^(?:3[0-9]{15}|(2131|1800)[0-9]{11})$");
				            if (number.match(re) != null) {
				            jQuery('span#jsCardType').html('<img src="<?php echo plugins_url( 'images/jcb.png', __FILE__ ) ?>" alt="JCB card detected" style="vertical-align: bottom;">');
				            jQuery('input#jsCardType').val('jcb');
				            jQuery('input#nab_card_number').data('validated',true);
				            validateFields(false);
				            return;
				            }
							jQuery('span#jsCardType').html('');
							jQuery('input#jsCardType').val('');
							jQuery('input#nab_card_number').data('validated',false);
					});
	
					jQuery('select#cc-expire-month,select#cc-expire-year').on('change',function() {
						validateFields(false);
					});
					jQuery('input#nab_card_ccv').on('keyup',function() {
						validateFields(false);	
					});

					function validateFields(showError) {
						jQuery('input#jsPayButton').removeAttr('disabled');
						// Skip validation is UPOP is selected
						if (jQuery('input#nab_cardtype_upop').is(':checked')) {
							return true;
						}
						var error = new Array();
						// Card number
						if (jQuery('input#nab_card_number').data('validated') != true) {
							error.push("<?php _e('Please enter a valid credit card number.','wc-nab'); ?>");
						}
						// Expiry date
						if (jQuery('select#cc-expire-month').val() == '' || jQuery('select#cc-expire-year').val() == '') {
							error.push("<?php _e('Please enter a valid expiry date.','wc-nab'); ?>");
						}
						var tdate = new Date();
						var year = tdate.getFullYear().toString().substring(2);
						var month = tdate.getMonth() + 1;
						if (month.length == 1) month = '0' + month;
						if (jQuery('select#cc-expire-month').val() < month && jQuery('select#cc-expire-year').val() == year) {
							error.push("<?php _e('Please enter an expiry date in the future.','wc-nab'); ?>");
						}
						// CCV
						if (jQuery('input#nab_card_ccv').val().length < 3 || jQuery('input#nab_card_ccv').val().length > 4) {
							error.push("<?php _e('Please enter a valid card security code.','wc-nab'); ?>");
						}
						if (error.length == 0) {
							jQuery('#nab_error_message').hide().html('');
							return true;
						} else if (showError == true) {
							var error_string = error.join("<br />");
							jQuery('#nab_error_message').show().html(error_string);
							return false;
						} else {
							return false;
						}
					}
					jQuery('input#jsPayButton').removeAttr('disabled');
				});
				</script>
		<?php
		}

		/**
		 * Process a refund
		 *
		 * @since 1.3.0
		 */
		public function process_refund($order_id, $amount = null, $reason = '') {

			$order = new WC_Order($order_id);

			$txnid = $order->get_transaction_id();
			$refid = get_post_meta($order_id,'_nab_reference_id',true);
			
			if (!$txnid || !$refid) return new WP_Error('no_txnid','Sorry, this order does not have a transaction ID saved. We cannot process an automatic refund for this order.');

			if (in_array(get_woocommerce_currency(),array('BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','VND','VUV','XAF','XOF','XPF'))) {
				$amount = $amount;
			} else {
				$amount = number_format( (float)$amount * 100, 0, '.', '' );
			}

			$data = array('amountcents'=>$amount, 'txnid'=>$txnid, 'reference' => $refid);

			$refund_xml = $this->generateRefundXMLMessage($data);
			$refund_result = $this->send($refund_xml,$this->xmlapiurl);

			$result_object = simplexml_load_string($refund_result); 

			if (isset($result_object->Payment->TxnList->Txn->approved) && (string) $result_object->Payment->TxnList->Txn->approved === "Yes") {
				// Refund approved
				$order->add_order_note(sprintf(__('NAB Transact refund was processed for $%s on %s. Transaction ID: %s','woothemes'),intval((string) $result_object->Payment->TxnList->Txn->amount)/100,(string) $result_object->Payment->TxnList->Txn->settlementDate,(string) $result_object->Payment->TxnList->Txn->txnID));
	    		return 1;
			} elseif (isset($result_object->Payment->TxnList->Txn->approved) && (string) $result_object->Payment->TxnList->Txn->approved !== "Yes") {
				// Refund declined
				$order->add_order_note(sprintf(__('NAB Transact refund was NOT processed due to: %s [%s].','woothemes'),(string) $result_object->Payment->TxnList->Txn->responseText, (string) $result_object->Payment->TxnList->Txn->responseCode));
		    	$this->log(sprintf(__('Order %s: NAB Transact refund declined - %s [%s].', 'woothemes'),$order->get_order_number(),(string) $result_object->Payment->TxnList->Txn->responseText, (string) $result_object->Payment->TxnList->Txn->responseCode));
		    	return new WP_Error('invalid_refund_response','Sorry, the refund was declined by NAB. Details have been saved as an order note.');
			} else {
				// Error processing refund
				$order->add_order_note(sprintf(__('NAB Transact refund was NOT processed due to: %s [%s].','woothemes'),(string) $result_object->Status->statusDescription, (string) $result_object->Status->statusCode));
		    	$this->log(sprintf(__('Order %s: NAB Transact refund errored - %s [%s].', 'woothemes'),$order->get_order_number(),(string) $result_object->Status->statusDescription, (string) $result_object->Status->statusCode));
		    	return new WP_Error('invalid_refund_response','Sorry, there was an error processing the refund. Details have been saved as an order note.');
			}
		}


		/**
		 * Generate fingerprint (3 versions)
		 *
		 * @since 1.3.0
		 */
		function generate_fingerprint($ver='V1',$vars) {
			$fingerprint;
			switch ($ver) {
				case "UPOP" :
					// Generate the Payment Fingerprint, V2 for UPOP payment style
					$fingerprint = sha1($this->settings['client_id'].'|'.$this->settings['nab_password'].'|0|'.$vars['reference_id'].'|'.$vars['amount'].'|'.$vars['timestamp']);
					break;

				case "V2" :
					// Generate the Payment Fingerprint, V2 style
					$fingerprint = sha1($this->settings['client_id'].'|'.$this->settings['nab_password'].'|'.$vars['txntype'].'|'.$vars['reference_id'].'|'.$vars['amount'].'|'.$vars['timestamp']);
					break;

				case "V1" :
					// Get the Payment Fingerprint
					$data = array(
						'EPS_MERCHANT'=>$this->settings['client_id'],
						'EPS_PASSWORD'=>$this->settings['nab_password'],
						'EPS_TIMESTAMP'=>$vars['timestamp'],
						'EPS_REFERENCEID'=>$vars['reference_id'],
						'EPS_AMOUNT'=>$vars['amount']
						);
					$fingerprint = urldecode($this->send($data,$this->fingerprinturl));
					if (strpos($fingerprint,' ') !== false || $fingerprint == '') {
						$errormsg = str_replace('error=','',$fingerprint);
						echo '<div class="woocommerce-error">';
						echo sprintf(__('There was a problem generating your NAB payment fingerprint: %s', 'wc-nab'),$errormsg);
						if ($errormsg == "Invalid merchant") {
							echo '<br />';
							_e('Your NAB credentials are incorrect. Please confirm your merchant details with NAB.', 'wc-nab');
						}
						echo '</div>';
						$this->log(sprintf(__('Error generating NAB payment fingerprint: %s', 'wc-nab'),$errormsg));
					}
					break;

				case "CRN" :
					$data = array(
						'EPS_MERCHANT'=>$this->settings['client_id'],
						'EPS_PASSWORD'=>$this->settings['nab_password'],
						'EPS_TYPE'=>'CRN',
						'EPS_ACTION'=>'ADDCRN',
						'EPS_CRN'=>$vars['eps_crn'],
						'EPS_TIMESTAMP'=>$vars['timestamp']
						);
					$fingerprint = $this->send($data,$this->crnfingerprinturl);
					if (strpos($fingerprint,' ') !== false || $fingerprint == '') {
						$errormsg = str_replace('error=','',$fingerprint);
						echo '<div class="woocommerce-error">';
						echo sprintf(__('There was a problem generating your NAB customer fingerprint: %s', 'wc-nab'),$errormsg);
						if ($errormsg == "Invalid merchant") {
							echo '<br />';
							_e('Your NAB credentials are incorrect. Please confirm your merchant details with NAB.', 'wc-nab');
						}
						echo '</div>';
						$this->log(sprintf(__('Error generating NAB customer fingerprint: %s', 'wc-nab'),$errormsg));
					}
					break;
			}
			return $fingerprint;
		}


		/**
		 * Generates the XML message to send via API for a scheduled subscription payment
		 *
		 * $data = array('crn','amountcents','reference','currency');
		 * @since 1.2.0 
		 **/
		function generateScheduledPaymentXMLMessage($data) {
			$tz_in_secs = date('Z');
			$tz_in_mins = round($tz_in_secs/60);
			if ($tz_in_mins >= 0) $tz_in_mins = '+'.$tz_in_mins;
			$timestamp = date('YdmHis000000').$tz_in_mins;

			$messageID = substr(time().'-'.$data['reference'],0,30);

			$xml = new DOMDocument();
			$root = $xml->appendChild($xml->createElement("NABTransactMessage"));
			
			// Create MessageInfo
			$MessageInfo = $root->appendChild($xml->createElement("MessageInfo"));
			$MessageInfo->appendChild($xml->createElement("messageID",$messageID));
			$MessageInfo->appendChild($xml->createElement("messageTimestamp",$timestamp));
			$MessageInfo->appendChild($xml->createElement("timeoutValue",'60'));
			$MessageInfo->appendChild($xml->createElement("apiVersion",'spxml-4.2'));

			// Create MerchantInfo
			$MerchantInfo = $root->appendChild($xml->createElement("MerchantInfo"));
			$MerchantInfo->appendChild($xml->createElement("merchantID",$this->settings['client_id']));
			$MerchantInfo->appendChild($xml->createElement("password",$this->settings['nab_password']));
			
			// Create RequestType
			$RequestType = $root->appendChild($xml->createElement("RequestType","Periodic"));

			// Create Periodic
			$Periodic = $root->appendChild($xml->createElement("Periodic"));
			$PeriodicList = $Periodic->appendChild($xml->createElement("PeriodicList"));
			$PeriodicList->appendChild($xml->createAttribute("count"))->appendChild($xml->createTextNode("1"));
			$PeriodicItem = $PeriodicList->appendChild($xml->createElement("PeriodicItem"));
			$PeriodicItem->appendChild($xml->createAttribute("ID"))->appendChild($xml->createTextNode("1"));
			$PeriodicItem->appendChild($xml->createElement("actionType","trigger"));
			$PeriodicItem->appendChild($xml->createElement("periodicType","8"));
			$PeriodicItem->appendChild($xml->createElement("crn",$data['crn']));
			$PeriodicItem->appendChild($xml->createElement("transactionReference",$data['reference']));
			$PeriodicItem->appendChild($xml->createElement("amount",$data['amountcents']));
			$PeriodicItem->appendChild($xml->createElement("currency",$data['currency']));
			$CreditCardInfo = $PeriodicItem->appendChild($xml->createElement("CreditCardInfo"));
			$CreditCardInfo->appendChild($xml->createElement("recurringFlag","no"));

			return $xml->saveHTML();
		}

		/**
		 * Generates the XML message to add a CRN for subscriptions
		 *
		 * $data = array('cardnumber','expiry','reference');
		 * @since 1.2.0 
		 **/
		function generateAddCRNXMLMessage($data) {
			$tz_in_secs = date('Z');
			$tz_in_mins = round($tz_in_secs/60);
			if ($tz_in_mins >= 0) $tz_in_mins = '+'.$tz_in_mins;
			$timestamp = date('YdmHis000000').$tz_in_mins;

			$messageID = substr(time().'-'.$data['reference'],0,30);

			$xml = new DOMDocument();
			$root = $xml->appendChild($xml->createElement("NABTransactMessage"));
			
			// Create MessageInfo
			$MessageInfo = $root->appendChild($xml->createElement("MessageInfo"));
			$MessageInfo->appendChild($xml->createElement("messageID",$messageID));
			$MessageInfo->appendChild($xml->createElement("messageTimestamp",$timestamp));
			$MessageInfo->appendChild($xml->createElement("timeoutValue",'60'));
			$MessageInfo->appendChild($xml->createElement("apiVersion",'spxml-4.2'));

			// Create MerchantInfo
			$MerchantInfo = $root->appendChild($xml->createElement("MerchantInfo"));
			$MerchantInfo->appendChild($xml->createElement("merchantID",$this->settings['client_id']));
			$MerchantInfo->appendChild($xml->createElement("password",$this->settings['nab_password']));
			
			// Create RequestType
			$RequestType = $root->appendChild($xml->createElement("RequestType","Periodic"));

			// Create Periodic
			$Periodic = $root->appendChild($xml->createElement("Periodic"));
			$PeriodicList = $Periodic->appendChild($xml->createElement("PeriodicList"));
			$PeriodicList->appendChild($xml->createAttribute("count"))->appendChild($xml->createTextNode("1"));
			$PeriodicItem = $PeriodicList->appendChild($xml->createElement("PeriodicItem"));
			$PeriodicItem->appendChild($xml->createAttribute("ID"))->appendChild($xml->createTextNode("1"));
			$PeriodicItem->appendChild($xml->createElement("actionType","addcrn"));
			$PeriodicItem->appendChild($xml->createElement("periodicType","5"));
			$PeriodicItem->appendChild($xml->createElement("crn",substr('crn'.time(),0,20)));
			$CreditCardInfo = $PeriodicItem->appendChild($xml->createElement("CreditCardInfo"));
			$CreditCardInfo->appendChild($xml->createElement("cardNumber",$data['cardnumber']));
			$CreditCardInfo->appendChild($xml->createElement("expiryDate",$data['expiry']));

			return $xml->saveHTML();
		}

		/**
		 * Generates the XML message to send via API for Capture (after preauth)
		 *
		 * $data = array('preauthid','txnid','amountcents','reference','currency');
		 * @since 1.2.0 
		 **/
		function generateCapturePaymentXMLMessage($data) {
			$tz_in_secs = date('Z');
			$tz_in_mins = round($tz_in_secs/60);
			if ($tz_in_mins >= 0) $tz_in_mins = '+'.$tz_in_mins;
			$timestamp = date('YdmHis000000').$tz_in_mins;

			$messageID = substr(time().'-'.$data['reference'],0,30);

			$xml = new DOMDocument();
			$root = $xml->appendChild($xml->createElement("NABTransactMessage"));
			
			// Create MessageInfo
			$MessageInfo = $root->appendChild($xml->createElement("MessageInfo"));
			$MessageInfo->appendChild($xml->createElement("messageID",$messageID));
			$MessageInfo->appendChild($xml->createElement("messageTimestamp",$timestamp));
			$MessageInfo->appendChild($xml->createElement("timeoutValue",'60'));
			$MessageInfo->appendChild($xml->createElement("apiVersion",'xml-4.2'));

			// Create MerchantInfo
			$MerchantInfo = $root->appendChild($xml->createElement("MerchantInfo"));
			$MerchantInfo->appendChild($xml->createElement("merchantID",$this->settings['client_id']));
			$MerchantInfo->appendChild($xml->createElement("password",$this->settings['nab_password']));
			
			// Create RequestType
			$RequestType = $root->appendChild($xml->createElement("RequestType","Payment"));

			// Create Transactions
			$Payment = $root->appendChild($xml->createElement("Payment"));
			$TxnList = $Payment->appendChild($xml->createElement("TxnList"));
			$TxnList->appendChild($xml->createAttribute("count"))->appendChild($xml->createTextNode("1"));

			$Txn = $TxnList->appendChild($xml->createElement("Txn"));
			$Txn->appendChild($xml->createAttribute("ID"))->appendChild($xml->createTextNode("1"));
			//$Txn = $PeriodicList->appendChild($xml->createElement("PeriodicItem"));
			$Txn->appendChild($xml->createElement("txnType","11"));
			$Txn->appendChild($xml->createElement("txnSource","23"));
			$Txn->appendChild($xml->createElement("amount",$data['amountcents']));
			$Txn->appendChild($xml->createElement("purchaseOrderNo",$data['reference']));
			$Txn->appendChild($xml->createElement("preauthID",$data['preauthid']));

			return $xml->saveHTML();
		}


		/**
		 * Generates the XML message to send via API for a refund
		 *
		 * $data = array('txnid','amountcents','reference');
		 * @since 1.2.0 
		 **/
		function generateRefundXMLMessage($data) {
			$tz_in_secs = date('Z');
			$tz_in_mins = round($tz_in_secs/60);
			if ($tz_in_mins >= 0) $tz_in_mins = '+'.$tz_in_mins;
			$timestamp = date('YdmHis000000').$tz_in_mins;

			$messageID = substr(time().'-'.$data['reference'],0,30);

			$xml = new DOMDocument();
			$root = $xml->appendChild($xml->createElement("NABTransactMessage"));
			
			// Create MessageInfo
			$MessageInfo = $root->appendChild($xml->createElement("MessageInfo"));
			$MessageInfo->appendChild($xml->createElement("messageID",$messageID));
			$MessageInfo->appendChild($xml->createElement("messageTimestamp",$timestamp));
			$MessageInfo->appendChild($xml->createElement("timeoutValue",'60'));
			$MessageInfo->appendChild($xml->createElement("apiVersion",'xml-4.2'));

			// Create MerchantInfo
			$MerchantInfo = $root->appendChild($xml->createElement("MerchantInfo"));
			$MerchantInfo->appendChild($xml->createElement("merchantID",$this->settings['client_id']));
			$MerchantInfo->appendChild($xml->createElement("password",$this->settings['nab_password']));
			
			// Create RequestType
			$RequestType = $root->appendChild($xml->createElement("RequestType","Payment"));

			// Create Transactions
			$Payment = $root->appendChild($xml->createElement("Payment"));
			$TxnList = $Payment->appendChild($xml->createElement("TxnList"));
			$TxnList->appendChild($xml->createAttribute("count"))->appendChild($xml->createTextNode("1"));

			$Txn = $TxnList->appendChild($xml->createElement("Txn"));
			$Txn->appendChild($xml->createAttribute("ID"))->appendChild($xml->createTextNode("1"));
			
			$Txn->appendChild($xml->createElement("txnType","4"));
			$Txn->appendChild($xml->createElement("txnSource","23"));
			$Txn->appendChild($xml->createElement("amount",$data['amountcents']));
			$Txn->appendChild($xml->createElement("txnID",$data['txnid']));
			$Txn->appendChild($xml->createElement("purchaseOrderNo",$data['reference']));

			return $xml->saveHTML();
		}

		/**
		 * Generates the XML message to send via API for payment (during checkout, with cc details)
		 *
		 * $data = array('cardnumber','expiry','name','amountcents','reference','currency');
		 * @since 1.2.0 
		 **/
		function generateCardPaymentXMLMessage($data) {
			$tz_in_secs = date('Z');
			$tz_in_mins = round($tz_in_secs/60);
			if ($tz_in_mins >= 0) $tz_in_mins = '+'.$tz_in_mins;
			$timestamp = date('YdmHis000000').$tz_in_mins;

			$messageID = substr(time().'-'.$data['reference'],0,30);

			$xml = new DOMDocument();
			$root = $xml->appendChild($xml->createElement("NABTransactMessage"));
			
			// Create MessageInfo
			$MessageInfo = $root->appendChild($xml->createElement("MessageInfo"));
			$MessageInfo->appendChild($xml->createElement("messageID",$messageID));
			$MessageInfo->appendChild($xml->createElement("messageTimestamp",$timestamp));
			$MessageInfo->appendChild($xml->createElement("timeoutValue",'60'));
			$MessageInfo->appendChild($xml->createElement("apiVersion",'xml-4.2'));

			// Create MerchantInfo
			$MerchantInfo = $root->appendChild($xml->createElement("MerchantInfo"));
			$MerchantInfo->appendChild($xml->createElement("merchantID",$this->settings['client_id']));
			$MerchantInfo->appendChild($xml->createElement("password",$this->settings['nab_password']));
			
			// Create RequestType
			$RequestType = $root->appendChild($xml->createElement("RequestType","Payment"));

			// Create Transactions
			$Payment = $root->appendChild($xml->createElement("Payment"));
			$TxnList = $Payment->appendChild($xml->createElement("TxnList"));
			$TxnList->appendChild($xml->createAttribute("count"))->appendChild($xml->createTextNode("1"));

			$Txn = $TxnList->appendChild($xml->createElement("Txn"));
			$Txn->appendChild($xml->createAttribute("ID"))->appendChild($xml->createTextNode("1"));
			//$Txn = $PeriodicList->appendChild($xml->createElement("PeriodicItem"));
			if ($data['capture']) {
				// Full payment
				$Txn->appendChild($xml->createElement("txnType","0"));
			} else {
				// Preauth only
				$Txn->appendChild($xml->createElement("txnType","10"));
			}
			$Txn->appendChild($xml->createElement("txnSource","23"));
			$Txn->appendChild($xml->createElement("amount",$data['amountcents']));
			$Txn->appendChild($xml->createElement("purchaseOrderNo",$data['reference']));
			
			$CreditCardInfo = $Txn->appendChild($xml->createElement('CreditCardInfo'));
			$CreditCardInfo->appendChild($xml->createElement('cardNumber',$data['cardnumber']));
			$CreditCardInfo->appendChild($xml->createElement('expiryDate',$data['expiry']));
			$CreditCardInfo->appendChild($xml->createElement('cardHolderName',$data['name']));
			$CreditCardInfo->appendChild($xml->createElement('recurringflag','no'));

			if (isset($this->settings['risk_management']) && $this->settings['risk_management'] === "yes") {
				// Add risk management details
				$BuyerInfo = $Txn->appendChild($xml->createElement('BuyerInfo'));
				$BuyerInfo->appendChild($xml->createElement('ip',$this->get_user_ip()));
				if (isset($data['first_name']) && $data['first_name'] && strlen($data['first_name']) <= 40) {
					$BuyerInfo->appendChild($xml->createElement('firstName',$data['first_name']));
				}
				if (isset($data['last_name']) && $data['last_name'] && strlen($data['last_name']) <= 40) {
					$BuyerInfo->appendChild($xml->createElement('lastName',$data['last_name']));
				}
				if (isset($data['postcode']) && $data['postcode'] && strlen($data['postcode']) <= 30) {
					$BuyerInfo->appendChild($xml->createElement('zipCode',$data['postcode']));
				}
				if (isset($data['city']) && $data['city'] && strlen($data['city']) <= 30) {
					$BuyerInfo->appendChild($xml->createElement('town',$data['city']));
				}
				if (isset($data['country']) && $data['country'] && strlen($data['country']) <= 3) {
					$BuyerInfo->appendChild($xml->createElement('billingCountry',$data['country']));
				}
				if (isset($data['delivery_country']) && $data['delivery_country'] && strlen($data['delivery_country']) <= 3) {
					$BuyerInfo->appendChild($xml->createElement('deliveryCountry',$data['delivery_country']));
				}
				if (isset($data['email']) && $data['email'] && strlen($data['email']) <= 100) {
					$BuyerInfo->appendChild($xml->createElement('emailAddress',$data['email']));
				}
			}

			return $xml->saveHTML();
		}

		/**
		 * Relay response - handles response from NAB Transact
		 *
		 * @since 1.0.0
		 */
		function relay_response() {
			// Use alternate handler if this is a CRN response
			if (isset($_GET['is_crn']) && $_GET['is_crn'] == true) {
				$this->relay_response_crn();
				exit();
			}

			global $woocommerce;

			// Process response
			$response = new stdClass;
			foreach ($_GET as $key => $value) {
				$response->$key = $value;
			}
		    foreach ($_POST as $key => $value) {
	            $response->$key = $value;
	        }
	        
	        if ( ! empty( $response->order ) ) {

		        $order = new WC_Order( (int) $response->order );

				if ($response->rescode == '00' || $response->rescode == '08' || $response->rescode == '11') { // Approved
					if ($order->key_is_valid( $response->key )) {

						// If it's a preauth only
						if (isset($response->preauthid)) {
							// Save preauth id
							update_post_meta( $order->id, '_nabdp_preauthid', $response->preauthid );
							update_post_meta( $order->id, '_nabdp_preauth_captured', 'no');
							update_post_meta( $order->id, '_nabdp_referenceid', $response->refid);

							if ( $order->has_status( array( 'pending', 'failed' ) ) ) {
								$order->reduce_order_stock();
							}

							$order->update_status( 'on-hold', sprintf( __( 'NAB charge pre-authorized (preauth ID: %s). Change order to Processing or Completed to take payment.', 'woothemes' ), $response->preauthid ) );
							$this->log( sprintf(__("Order %s: Successful pre-authorization (preauthid: %s) for transaction %s" , 'wc-nab'), $order->get_order_number(), $response->preauthid, $response->txnid));
						} else {

							// Payment complete
							$order->add_order_note(
							'NAB Transaction id: '.$response->txnid
							."\r\nNAB Settlement date: ".$response->settdate);

							$order->payment_complete($response->txnid);

							if (isset($response->afrescode) && $response->afrescode != '000') {
								$order->add_order_note(sprintf(__('FraudGuard warning triggered: %s','wc-nab'), $response->afrestext));
								$order->update_status( 'on-hold' );
								$this->log(sprintf(__('Order %s: FraudGuard warning triggered: %s', 'wc-nab'),$order->get_order_number(),$response->afrestext));
							}

						}

						// Remove cart
						$woocommerce->cart->empty_cart();

					} else { // payment received but order key didn't match!

						// Key did not match order id
						$order->add_order_note( sprintf(__('Transaction successful, but order key was not valid. Confirm the amount of this payment. Code %s - %s.', 'wc-nab'), $response->response_code, $response->response_reason_text ) );
						$this->log(sprintf(__('Order %s: Transaction successful, but order key was not valid. Confirm the amount of this payment. Code %s - %s.', 'wc-nab'),$order->get_order_number(),$response->response_code, $response->response_reason_text));

						// Put on hold if pending
						if ($order->status == 'pending' || $order->status == 'failed') {
							$order->update_status( 'on-hold' );
						}
					}
				} else { // Transaction failed
					$order->update_status( 'failed' );
					$order->add_order_note( sprintf(__("NAB payment failure: code %s - %s.", 'wc-nab'), $response->rescode, $response->restext ) );
					$this->log(sprintf(__('Order %s: NAB payment failure. Code %s - %s.', 'wc-nab'),$order->get_order_number(),$response->rescode, $response->restext));
				}

				wp_redirect( $this->get_return_url( $order ) );
				exit;

			}

			wp_redirect( $this->get_return_url() );
			exit;
		}

		/**
		 * Relay response - handles response from NAB Transact CRN
		 * At this stage we've just added a CRN record, and now we must process the payment to this CRN
		 * 
		 * @since 1.2.0
		 */
		function relay_response_crn() {
			global $woocommerce;

			// Process response
			$response = new stdClass;
			foreach ($_GET as $key => $value) {
				$response->$key = $value;
			}
		    foreach ($_POST as $key => $value) {
	            $response->$key = $value;
	        }

			if ( ! empty( $response->order ) ) {
		    
		        $order = new WC_Order( (int) $response->order );

				if ((!isset($response->afrescode) || $response->afrescode=='400' || $response->afrescode=='000') && ($response->rescode == '00' || $response->rescode == '08' || $response->rescode == '11')) { // Approved
					if ($order->key_is_valid( $response->key ) && $order->status != 'completed' && $order->get_status() != 'processing') {

						// Save CRN to order meta also
						$this->save_subscription_meta( (int)$response->order , $response->CRN);

						// Add Order Note
						$order->add_order_note(__('Credit card details stored.','wc-nab'));

						if (!function_exists('wcs_get_subscriptions_for_order')) {
							$amount = WC_Subscriptions_Order::get_total_initial_payment($order);
						} else {
							$amount = $order->get_total();
						}

						if (isset($response->is_payment_change) && $response->is_payment_change == '1' && get_post_meta($order->get_id(),'_is_mid_change_method',true)) {
							$amount = 0;
							delete_post_meta($order->get_id(),'_is_mid_change_method',true);
						}

						if ($amount == 0) {
							$order->payment_complete();
						} else {
							// Now to process payment, but only if it hasn't already been done
							// (this url is used by result AND return)
							if ($order->status != 'completed') {
								if (version_compare( WC_VERSION, '2.7', '<' )) {
									$order_key = $order->order_key;
								} else {
									$order_key = $order->get_order_key();
								}

								if (in_array(get_woocommerce_currency(),array('BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','VND','VUV','XAF','XOF','XPF'))) {
									$amount = $amount;
								} else {
									$amount = number_format( (float)$amount * 100, 0, '.', '' );
								}

								$order_id = $order->get_id();
								$reference_id = $this->get_reference_id($order_id);
								$this->save_reference_id($order_id,$reference_id);
								
								$data = array(
									'crn'=>$response->CRN,
									'amountcents'=>$amount,
									'reference'=>$reference_id,
									'currency'=>get_woocommerce_currency());
								$payment_xml = $this->generateScheduledPaymentXMLMessage($data);
								$payment_result = $this->send($payment_xml,$this->crnxmlapiurl,true);

								$result_object = simplexml_load_string($payment_result); 


								if ($result_object->Periodic->PeriodicList->PeriodicItem->responseCode == '00' || $result_object->Periodic->PeriodicList->PeriodicItem->responseCode == '08' || $result_object->Periodic->PeriodicList->PeriodicItem->responseCode == '11') {
									// Payment success!
									$order->add_order_note(sprintf(__("NAB Transaction id: %s\r\nNAB Settlement date: %s",'wc-nab'),$result_object->Periodic->PeriodicList->PeriodicItem->txnID,$result_object->Periodic->PeriodicList->PeriodicItem->settlementDate));
									$order->payment_complete((string)$result_object->Periodic->PeriodicList->PeriodicItem->txnID);
									// Remove cart
									$woocommerce->cart->empty_cart();

								} else {
									if ($order->status != 'completed' && $order->status != 'processing') {
										$order->update_status( 'failed', sprintf(__("NAB error whilst processing payment via XML API using CRN: code %s - %s.", 'wc-nab'), $result_object->Status->statusCode, $result_object->Status->statusDescription) );
										$this->log(sprintf(__('Order %s: NAB error whilst processing payment via XML API using CRN. Code %s - %s.', 'wc-nab'),$order->get_order_number(),$result_object->Status->statusCode, $result_object->Status->statusDescription));
									}
								}
							}
						}

					} else { // payment received but order key didn't match!

						// Key did not match order id
						$order->add_order_note( sprintf(__('Payment received, but order key was not valid. Check the amount of the payment processed. Code %s - %s.', 'wc-nab'), $response->response_code, $response->response_reason_text ) );
						$this->log(sprintf(__('Order %s: Payment received, but order key was not valid. Check the amount of the payment processed. Code %s - %s.', 'wc-nab'),$order->get_order_number(),$response->response_code, $response->response_reason_text));

						// Put on hold if pending
						if ($order->status == 'pending' || $order->status == 'failed') {
							$order->update_status( 'on-hold' );
						}
					}
				} else { // Transaction failed
					if ($order->status != 'completed' && $order->status != 'processing') {
						$order->update_status( 'failed', sprintf(__("NAB error whilst adding CRN: code %s - %s. PAYMENT NOT PROCESSED!", 'wc-nab'), $response->rescode, $response->restext) );
						$this->log(sprintf(__('Order %s: NAB error whilst adding CRN: code %s - %s. PAYMENT NOT PROCESSED!', 'wc-nab'),$order->get_order_number(),$response->rescode, $response->restext));
					}
				}
				
				// It's possible we just processed a change of payment method, not a proper order
		        // so we might want to just go back to the My Account page
		        if (isset($_GET['is_payment_change']) && $_GET['is_payment_change'] == '1') {
		        	wp_redirect(get_permalink( woocommerce_get_page_id( 'myaccount' ) ));
		        } else {
		        	wp_redirect( $this->get_return_url( $order ) );
		        }
				exit;

			}
			wp_redirect( $this->get_return_url() );
			exit;
		}

		/**
		 * Capture payment when the order is changed from on-hold to complete or processing
		 *
		 * @param  int $order_id
		 */
		public function capture_payment( $order_id ) {
			$order = new WC_Order( $order_id );

			if (version_compare( WC_VERSION, '2.7', '<' )) {
				$payment_method = $order->payment_method;
			} else {
				$payment_method = $order->get_payment_method();
			}
			

			if ( 'nab_dp' === $payment_method ) {
				$charge   = get_post_meta( $order_id, '_nabdp_preauthid', true );
				$captured = get_post_meta( $order_id, '_nabdp_preauth_captured', true );
				$reference_id = get_post_meta( $order_id, '_nabdp_referenceid', true );

				if ( $charge && 'no' === $captured ) {
					$amount = $order->get_total();
					if (in_array(get_woocommerce_currency(),array('BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','VND','VUV','XAF','XOF','XPF'))) {
						$amount = $amount;
					} else {
						$amount = number_format( (float)$amount * 100, 0, '.', '' );
					}

					if ( 0 == $amount ) {
						// Payment complete
						$order->payment_complete();
						return true;
					}

					$data = array(
						'preauthid'=>$charge,
						'amountcents'=>$amount,
						'reference'=>$reference_id,
						'currency'=>get_woocommerce_currency());
					$payment_xml = $this->generateCapturePaymentXMLMessage($data);
					$payment_result = $this->send($payment_xml,$this->xmlapiurl,true);

					$result_object = simplexml_load_string($payment_result); 

					if ($result_object->Payment->TxnList->Txn->responseCode == '00' || $result_object->Payment->TxnList->Txn->responseCode == '08' || $result_object->Payment->TxnList->Txn->responseCode == '11') {
						// Payment success!
						$order->add_order_note(
							'Payment captured. NAB Transaction id: '.$result_object->Payment->TxnList->Txn->txnID
							."\r\nNAB Settlement date: ".$result_object->Payment->TxnList->Txn->settlementDate);

						$order->payment_complete((string) $result_object->Payment->TxnList->Txn->txnID);
						delete_post_meta( $order->id, '_nabdp_preauthid' );
						update_post_meta( $order->id, '_nabdp_preauth_captured', 'yes' );
						return true;
					} elseif (isset($result_object->Payment->TxnList->Txn->responseCode)) {
						$order->add_order_note(sprintf(__("NAB error whilst processing payment via XML API using preauthID. Unable to capture charge. Code %s - %s.", 'wc-nab'), $result_object->Payment->TxnList->Txn->responseCode, $result_object->Payment->TxnList->Txn->responseText) );
						$this->log(sprintf(__('Order %s: NAB error whilst processing payment via XML API using preauthID. Unable to capture charge. Code %s - %s.', 'wc-nab'),$order->get_order_number(),$result_object->Payment->TxnList->Txn->responseCode, $result_object->Payment->TxnList->Txn->responseText));
						return new WP_Error('nab_error',sprintf(__("NAB error whilst processing payment via XML API using preauthID. Unable to capture charge. Code %s - %s.", 'wc-nab'), $result_object->Payment->TxnList->Txn->responseCode, $result_object->Payment->TxnList->Txn->responseText));
					} else {
						$order->add_order_note(sprintf(__("NAB error whilst processing payment via XML API using preauthID. Unable to capture charge. Code %s - %s.", 'wc-nab'), $result_object->Status->statusCode, $result_object->Status->statusDescription) );
						$this->log(sprintf(__('Order %s: NAB error whilst processing payment via XML API using preauthID. Unable to capture charge. Code %s - %s.', 'wc-nab'),$order->get_order_number(),$result_object->Status->statusCode, $result_object->Status->statusDescription));
						return new WP_Error('nab_error',sprintf(__("NAB error whilst processing payment via XML API using preauthID. Unable to capture charge. Code %s - %s.", 'wc-nab'), $result_object->Status->statusCode, $result_object->Status->statusDescription));
					}
				} else {
					return new WP_Error( 'nab_error', __( 'Cannot capture payment (already captured or preauthid missing).', 'wc-nab' ) );
				}
			}
		}

		/**
		 * Cancel pre-auth on refund/cancellation
		 *
		 * @param  int $order_id
		 */
		public function cancel_payment( $order_id ) {
			$order = new WC_Order( $order_id );

			if (version_compare( WC_VERSION, '2.7', '<' )) {
				$payment_method = $order->payment_method;
			} else {
				$payment_method = $order->get_payment_method();
			}
			

			if ( 'nab_dp' === $payment_method ) {
				$preauthid = get_post_meta($order->id, '_nabdp_preauthid', true);
				delete_post_meta( $order->id, '_nabdp_preauth_captured' );
				delete_post_meta( $order->id, '_nabdp_preauthid' );

				$order->add_order_note( sprintf( __( 'NAB preauthorization code deleted. (Preauth ID: %s)', 'woocommerce-gateway-stripe' ), $preauthid ) );
			}
		}


		/**
		 * scheduled_subscription_payment function.
		 * 
		 * @param $amount_to_charge float The amount to charge.
		 * @param WC_Order $renewal_order A WC_Order object created to record the renewal payment.
		 * @access public
		 * @return void
		 */
		function scheduled_subscription_payment( $amount_to_charge, $renewal_order ) {

			$result = $this->process_subscription_payment( $renewal_order, $amount_to_charge );
			
			if ( is_wp_error( $result ) ) {	
				$renewal_order->update_status( 'failed', sprintf( __( 'NAB transaction failed (%s)', 'wc-nab' ), $result->get_error_message() ) );
				$this->log(sprintf(__('Renewal order %s: NAB transaction failed. Error: %s', 'wc-nab'),$renewal_order->get_order_number(),$result->get_error_message()));
			}
			
		}

		/**
		 * process_subscription_payment function.
		 * 
		 * @access public
		 * @param mixed $order
		 * @param int $amount (default: 0)
		 * @return void
		 */
		function process_subscription_payment( $order = '', $amount = 0 ) {
			if ( 0 == $amount ) {
				// Payment complete
				$order->payment_complete();
				return true;
			}

			$crn = get_post_meta($order->id,'_nab_crn',true);
			if (!$crn) 
				return new WP_Error( 'nab_error', __( 'CRN not found.', 'wc-nab' ) );

			//$subscription_name = sprintf( __( '%s - Order %s', 'wc-merchant-warrior' ), substr(esc_html( get_bloginfo( 'name', 'display' ) ) , 0, 15), $order->get_order_number() );
			
			$order_id = $order->get_id();
			$referenceid = $this->get_reference_id($order_id);
			$this->save_reference_id($order_id,$referenceid);

			if (in_array(get_woocommerce_currency(),array('BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','VND','VUV','XAF','XOF','XPF'))) {
				$amount = $amount;
			} else {
				$amount = number_format( (float)$amount * 100, 0, '.', '' );
			}

			$data = array(
				'crn'=>$crn,
				'amountcents'=>$amount,
				'reference'=>$referenceid,
				'currency'=>get_woocommerce_currency());
			$payment_xml = $this->generateScheduledPaymentXMLMessage($data);
			$payment_result = $this->send($payment_xml,$this->crnxmlapiurl,true);

			$result_object = simplexml_load_string($payment_result); 

			if ($result_object->Periodic->PeriodicList->PeriodicItem->responseCode == '00' || $result_object->Periodic->PeriodicList->PeriodicItem->responseCode == '08' || $result_object->Periodic->PeriodicList->PeriodicItem->responseCode == '11') {
				// Payment success!
				$order->add_order_note(
					'Subscription payment processed. NAB Transaction id: '.$result_object->Periodic->PeriodicList->PeriodicItem->txnID
					."\r\nNAB Settlement date: ".$result_object->Periodic->PeriodicList->PeriodicItem->settlementDate);
				$order->payment_complete((string)$result_object->Periodic->PeriodicList->PeriodicItem->txnID);
				$order_id = $order->get_id();
				$reference_id = $this->get_reference_id($order_id);
				$this->save_reference_id($order_id,$reference_id);
				return true;
			} else {
				$order->add_order_note(sprintf(__("NAB error whilst processing payment via XML API using CRN. Code %s - %s.", 'wc-nab'), $result_object->Status->statusCode, $result_object->Status->statusDescription) );
				$this->log(sprintf(__('Order %s: NAB error whilst processing payment via XML API using CRN. Code %s - %s.', 'wc-nab'),$order->get_order_number(),$result_object->Status->statusCode, $result_object->Status->statusDescription));
				return new WP_Error('nab_error',sprintf(__("NAB error whilst processing payment via XML API using CRN: code %s - %s.", 'wc-nab'), $result_object->Status->statusCode, $result_object->Status->statusDescription));
			}
		}

		/**
		 * Store the customer and card IDs on the order and subscriptions in the order
		 *
		 * @param int $order_id
		 * @param string $customer_id
		 */
		protected function save_subscription_meta( $order_id, $customer_id ) {
			$customer_id = wc_clean( $customer_id );
			update_post_meta( $order_id, '_nab_crn', $customer_id );

			// Also store it on the subscriptions being purchased in the order
			if (function_exists('wcs_get_subscriptions_for_order')) {
				foreach( wcs_get_subscriptions_for_order( $order_id ) as $subscription ) {
					update_post_meta( $subscription->id, '_nab_crn', $customer_id );
				}
			}
		}

		/**
		 * Check if the order has a subscription (either according to Subscriptions 1.5 or 2.0)
		 *
		 * @param string $order_id The ID of the order to check
		 * @return mixed Either 1 (Subscriptions 1.5), 2 (Subscriptions 2) or false (no order)
		 */
		function order_has_subscription($order_id) {
			// Subscriptions not loaded
			if (!class_exists('WC_Subscriptions_Order')) return false;

			// Subscriptions v2.0
			if (function_exists('wcs_order_contains_subscription')) {
				if (wcs_order_contains_subscription($order_id) || wcs_order_contains_renewal( $order_id ) || ( function_exists( 'wcs_is_subscription' ) && wcs_is_subscription( $order_id ))) {
					return 2;
				} else {
					return false;
				}
			}
			
			// Subscriptions v1.5
			if (WC_Subscriptions_Order::order_contains_subscription($order_id)) {
				return 1;
			}

			return false;
		}

		/**
		 * Filter acceptable payment statuses to allow active orders to reach the receipt/CC form page
		 * when changing payment methods
		 *
		 * @param array $statuses Acceptable order statuses
		 * @param WC_Order $order The order which is being checked
		 * @return array of statuses
		 */
		function allow_payment_method_change($statuses, $order = null) {
			if (isset($_GET['is_payment_change']) && $_GET['is_payment_change'] == '1') {
				$statuses[] = 'processing';
				$statuses[] = 'completed';
				$statuses[] = 'on-hold';
				$statuses[] = 'active';
			}
			return $statuses;
		}


		/**
		 * Update the customer token IDs for a subscription after a customer used the gateway to successfully complete the payment
		 * for an automatic renewal payment which had previously failed.
		 *
		 * @param WC_Order $original_order The original order in which the subscription was purchased.
		 * @param WC_Order $renewal_order The order which recorded the successful payment (to make up for the failed automatic payment).
		 * @return void
		 */
		function update_failing_payment_method( $subscription, $new_renewal_order ) {
			update_post_meta( $subscription->id, '_nab_crn', get_post_meta( $new_renewal_order->id, '_nab_crn', true ) );
		}

		/**
		 * Include the payment meta data required to process automatic recurring payments so that store managers can
		 * manually set up automatic recurring payments for a customer via the Edit Subscription screen in Subscriptions v2.0+.
		 *
		 * @param array $payment_meta associative array of meta data required for automatic payments
		 * @param WC_Subscription $subscription An instance of a subscription object
		 * @return array
		 */
		public function add_subscription_payment_meta( $payment_meta, $subscription ) {
			$payment_meta[ $this->id ] = array(
				'post_meta' => array(
					'_nab_crn' => array(
						'value' => get_post_meta( $subscription->id, '_nab_crn', true ),
						'label' => 'NAB Customer Reference Number (CRN)',
					),
				),
			);
			return $payment_meta;
		}

		/**
		 * Validate the payment meta data required to process automatic recurring payments so that store managers can
		 * manually set up automatic recurring payments for a customer via the Edit Subscription screen in Subscriptions 2.0+.
		 *
		 * @param string $payment_method_id The ID of the payment method to validate
		 * @param array $payment_meta associative array of meta data required for automatic payments
		 * @return array
		 */
		public function validate_subscription_payment_meta( $payment_method_id, $payment_meta ) {
			if ( $this->id === $payment_method_id ) {
				if ( ! isset( $payment_meta['post_meta']['_nab_crn']['value'] ) || empty( $payment_meta['post_meta']['_nab_crn']['value'] ) ) {
					throw new Exception( 'A "_nab_crn" value is required.' );
				}
			}
		}

		/**
         * Get user's IP address
         */
        function get_user_ip() {
            $ip = (isset($_SERVER['HTTP_X_FORWARD_FOR']) && !empty($_SERVER['HTTP_X_FORWARD_FOR'])) ? $_SERVER['HTTP_X_FORWARD_FOR'] : $_SERVER['REMOTE_ADDR'];
            if (!filter_var($ip, FILTER_FLAG_IPV4)) {
            	$ip = '';
            }
            return $ip;
        }

		public static function log( $message ) {
			if ( empty( self::$log ) ) {
				self::$log = new WC_Logger();
			}

			self::$log->add( 'woo_nab', $message );

		}
		

	}


	/**
	 * Add the NAB Transact DP gateway to WooCommerce
	 *
	 * @since 1.0.0
	 **/
	function add_nab_dp_gateway( $methods ) {
		if ( class_exists( 'WC_Subscriptions_Order' ) && !function_exists( 'wcs_create_renewal_order' ) ) {
			include_once( 'woocommerce-nab-dp-subscriptions-deprecated.php' );
			$methods[] = 'WC_Gateway_NAB_Direct_Post_Subscriptions_Deprecated';
		} else {
			$methods[] = 'WC_Gateway_NAB_Direct_Post';
		}
		return $methods;
	}
	add_filter('woocommerce_payment_gateways', 'add_nab_dp_gateway' );
}