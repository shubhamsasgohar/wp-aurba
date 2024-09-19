<?php
/**
 * Plugin Name: MaxPay
 * Description: MaxPay Payment Gateway for Woocommerce
 * Author: dinarys LLC
 * Version: 1.0
 * Text Domain: wc-maxpay-gateway
 * Domain Path: /i18n/languages/
 *
 * @package   WC-MaxPay-Gateway
 * @author    dinarys LLC
 * @category  Admin
 *
 */

use Maxpay\Lib\Maxpay\Scriney;
use Maxpay\Lib\Model\FixedProduct;
use Maxpay\Lib\Util\SignatureHelper;
use Maxpay\Lib\Util\StringHelper;

defined( 'ABSPATH' ) or exit;

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return;
}

spl_autoload_register( 'maxpay_autoloader' );

function maxpay_autoloader( $class_name ) {
    
  if ( false !== strpos( $class_name, 'Maxpay' ) ) {
    $classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'Lib' . DIRECTORY_SEPARATOR;
    $class_file = str_replace( 'Maxpay\Lib', DIRECTORY_SEPARATOR, $class_name ) . '.php';
    $class_file = str_replace( '\\', DIRECTORY_SEPARATOR, $class_file );
    $class_file = str_replace( '_', DIRECTORY_SEPARATOR, $class_file );
    require_once $classes_dir . $class_file;
  }
}


/**
 * Add the gateway to WC Available Gateways
 * 
 * @param array $gateways all available WC gateways
 * @return array $gateways all WC gateways + offline gateway
 */
function wc_maxpay_add_to_gateways( $gateways ) {
	$gateways[] = 'WC_MaxPay_Gateway';
	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'wc_maxpay_add_to_gateways' );


/**
 * Adds plugin page links
 * 
 * @param array $links all plugin links
 * @return array $links all plugin links + our custom links (i.e., "Settings")
 */
function wc_maxpay_gateway_plugin_links( $links ) {

	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=maxpay_gateway' ) . '">' . __( 'Configure', 'wc-maxpay-offline' ) . '</a>'
	);

	return array_merge( $plugin_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_maxpay_gateway_plugin_links' );


/**
 * Maxpay Payment Gateway
 *
 * Provides an MaxPay Payment Gateway;
 * We load it later to ensure WC is loaded first since we're extending it.
 *
 * @class 		WC_MaxPay_Gateway
 * @extends		WC_Payment_Gateway
 * @version		1.0.0
 * @package		WooCommerce/Classes/Payment
 * @author 		dinarys LLC
 */
add_action( 'plugins_loaded', 'wc_maxpay_gateway_init', 11 );

function wc_maxpay_gateway_init() {

	class WC_MaxPay_Gateway extends WC_Payment_Gateway {
            
                public $maxpayForm;

		/**
		 * Constructor for the gateway.
		 */
		public function __construct() {
	  
			$this->id                 = 'maxpay_gateway';
			$this->icon               = apply_filters('woocommerce_offline_icon', '');
			$this->has_fields         = false;
			$this->method_title       = __( 'MaxPay', 'wc-maxpay-offline' );
			$this->method_description = __( 'Allows using MaxPay for payments.', 'wc-maxpay-offline' );
		  
			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();
		  
			// Define user set variables
			$this->title        = $this->get_option( 'title' );
			$this->description  = $this->get_option( 'description' );
			$this->instructions = $this->get_option( 'instructions', $this->description );
			$this->public_key = $this->get_option( 'public_key' );
			$this->secret_key = $this->get_option( 'secret_key');
		  
			// Actions
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action('woocommerce_api_'.strtolower(get_class($this)), array(&$this, 'maxpay_payment_completed'));
			add_action( 'woocommerce_receipt_' . $this->id, array(
				$this,
				'pay_for_order'
			) );                       
		}                               
	
		/**
		 * Initialize Gateway Settings Form Fields
		 */
		public function init_form_fields() {
	  
			$this->form_fields = apply_filters( 'wc_offline_form_fields', array(
		  
				'enabled' => array(
					'title'   => __( 'Enable/Disable', 'wc-maxpay-offline' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Maxpay Payments', 'wc-maxpay-offline' ),
					'default' => 'yes'
				),
				
				'title' => array(
					'title'       => __( 'Title', 'wc-maxpay-offline' ),
					'type'        => 'text',
					'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'wc-maxpay-offline' ),
					'default'     => __( 'Maxpay Payments', 'wc-maxpay-offline' ),
					'desc_tip'    => true,
				),
				
				'description' => array(
					'title'       => __( 'Description', 'wc-maxpay-offline' ),
					'type'        => 'textarea',
					'description' => __( 'Payment method description that the customer will see on your checkout.', 'wc-maxpay-offline' ),
					'default'     => __( '', 'wc-maxpay-offline' ),
					'desc_tip'    => true,
				),
				
				'instructions' => array(
					'title'       => __( 'Instructions', 'wc-maxpay-offline' ),
					'type'        => 'textarea',
					'description' => __( 'Instructions that will be added to the thank you page and emails.', 'wc-maxpay-offline' ),
					'default'     => '',
					'desc_tip'    => true,
				),
				
				'public_key' => array(
					'title'       => __( 'Public key', 'wc-maxpay-offline' ),
					'type'        => 'text',
					'description' => __( 'Public key provided for your account by MaxPay gateway', 'wc-maxpay-offline' ),
					'default'     => '',
					'desc_tip'    => true,
				),
				
				'secret_key' => array(
					'title'       => __( 'Secret key', 'wc-maxpay-offline' ),
					'type'        => 'text',
					'description' => __( 'Secret key provided for your account by MaxPay gateway', 'wc-maxpay-offline' ),
					'default'     => '',
					'desc_tip'    => true,
				),
			) );
		}
	
	
		/**
		 * Process the payment and return the result
		 *
		 * @param int $order_id
		 * @return array
		 */
		public function process_payment( $order_id ) {
                    
                        $order = new WC_Order( $order_id );
                    
                        return array(
                            'result' => 'success',
                            'redirect' => $order->get_checkout_payment_url( true )
                        );
                        
		}
                
                public function prepare_form($order_id) {
                    
                    $order = new WC_Order( $order_id );
                    
                    $baseUrl = $this->get_return_url( $order );
                        
                    if( strpos( $baseUrl, '?') !== false ) {
                            $baseUrl .= '&';
                    } else {
                            $baseUrl .= '?';
                    }

                    $productData[] = [
                        "productType" => "fixedProduct",
                        "productId" => $order_id,
                        "productName" => "Order id #{$order_id}",
                        "currency" => get_woocommerce_currency(),
                        "amount" => $order->get_total()
                    ];
                    
                    $params = [
                        'customProduct' => json_encode($productData),
                        "email" => $order->billing_email,
                        "key" => $this->public_key,
                        "uniqueuserid" => $order->billing_email,
                        "success_url" => $baseUrl,
                        "decline_url" => $baseUrl,
                    ];                    
                    
					error_log(print_r($params,true));
					
					$class_methods = get_class_methods(new SignatureHelper());

					foreach ($class_methods as $method_name) {
						error_log($method_name);
					}
					
                    $signature = (new SignatureHelper())->generate($params, $this->secret_key, true);

                    $params['signature'] = $signature;
                    $params['iframe_signature'] = "psp-hpp-".$signature;
                    
                    return $params;
                }
                
                function maxpay_redirect_form_validate($order, $payment_method) {
    
                    $scriney = new Scriney($this->public_key, $this->secret_key);

                    if ($scriney->validateCallback($_POST)) {
                        return true;
                    } else {
                        return false;
                    }

                }
                
                public function pay_for_order( $order_id ) {
                    $order = new WC_Order( $order_id );
					
					
                    $scriney = new Scriney($this->public_key, $this->secret_key);
                   // echo '<p>' . __( 'Redirecting to payment provider.', 'txtdomain' ) . '</p>';
                    
                    $order->add_order_note( __( 'Order placed and user redirected.', 'txtdomain' ) );
                    $order->update_status( 'on-hold', __( 'Awaiting payment.', 'txtdomain' ) );
                    
                    WC()->cart->empty_cart();

                   // wc_enqueue_js( 'jQuery( "#submit-form" ).click();' );
                    wc_enqueue_js( 'jQuery( ".order_details" ).hide();' );
                    wc_enqueue_js( 'jQuery( ".entry-title" ).hide();' );

                    /* $formData = $this->prepare_form($order_id);        
                    $formTxt = '';
                    
					$data_text = '';
					$iframe_signature = '';
                    foreach($formData as $key => $value){
                        //$formData .= '<input type="hidden" name="'.(new StringHelper())->encodeHtmlAttribute($key).'" value="'. (new StringHelper())->encodeHtmlAttribute($value).'">';
						
						$data_text .='data-'.(new StringHelper())->encodeHtmlAttribute($key).'="'. (new StringHelper())->encodeHtmlAttribute($value).'"';
						
						if($key=="iframe_signature"){
							$iframe_signature = (new StringHelper())->encodeHtmlAttribute($value);
						}
                    }
					
					$data_text = mb_strtolower($data_text); */
					
					//echo $formData;
                    
                    /*echo '<form action="' . 'https://hpp.maxpay.com/hpp' . '" method="post" target="_top">' . $formData .
                        '<div class="btn-submit-payment" style="display: none;">
                            <button type="submit" id="submit-form"></button>
                        </div>
                    </form>';*/
					/*echo '<div>
						<script src="https://hpp.maxpay.com/paymentPage.js" class="pspScript" data-iframesrc="https://hpp.maxpay.com/hpp" data-buttontext="Pay!" data-name="Payment page"  data-type="integrated" data-width="auto" data-height="auto" data-displaybuybutton="true" data-payment_method="Credit card" '.$data_text.'></script>
						<form class="pspPaymentForm"></form>
						<iframe id="'.$iframe_signature.'"></iframe>
						</div>';*/
						
					$baseUrl = $this->get_return_url( $order );
	
					if( strpos( $baseUrl, '?') !== false ) {
							$baseUrl .= '&';
					} else {
							$baseUrl .= '?';
					}

                    echo $scriney->buildButton($order->billing_email)->setCustomProducts(
						  [
							  new FixedProduct(
								  "$order_id",
								  "Order id #{$order_id}",
								  (float)$order->get_total(),
								  get_woocommerce_currency(),
								  null,
								  null,
								  "Order id #{$order_id}"
							  )
						  ]
					  )->buildFrame();
                    return true;
                }
                
                public function maxpay_payment_completed( $order_id ) { 
                    
                    if($_POST && isset($_POST['transactionId']) && isset($_POST['productList']) && !empty($_POST['productList'])){
						
                        $order_id = (int) trim(strip_tags($_POST['productList'][0]['productId']));
                        
                        $order = new WC_Order( $order_id );
                        
                        if(($_POST['status'] == 'success') && $this->maxpay_redirect_form_validate($order, 'maxpay_gategway')){
                            
                            $order->payment_complete();
                            $order->add_order_note("Payment completed (transaction id: ".$_POST['transactionId'].")",0,true);
                            
                        }else{
							wp_redirect( get_site_url().'/failed/', 301 ); 
						}
                        
                        echo 'OK';
                        
                    }
					else{
						wp_redirect( get_site_url().'/failed/', 301 ); 
					}
                    
                    exit();

                }
        }     
        
}