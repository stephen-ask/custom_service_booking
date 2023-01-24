<?php
// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

if(!class_exists('SF_Woopayments') && class_exists('WooCommerce')) { 
class SF_Woopayments
{
	
	
	private $checkout_info = array();
	private $product_id;
	private $orderid;
	private $wooaction;
	
    public function __construct()
    {
	global $service_finder_options;
	$woopayment = (isset($service_finder_options['woocommerce-payment'])) ? esc_html($service_finder_options['woocommerce-payment']) : false;
	$this->product_id = (isset($service_finder_options['woo-product-id'])) ? esc_html($service_finder_options['woo-product-id']) : '';
	
        $_this = $this;
		add_action(
                    'wp_ajax_sf_add_to_woo_cart',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_add_to_woo_cart' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_nopriv_sf_add_to_woo_cart',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_add_to_woo_cart' ) );
                    }
						
                );
				
		if ( $woopayment ) {
            add_action( 'woocommerce_add_order_item_meta',      array( $this, 'add_booking_meda_data' ), 10, 3 );
            add_action( 'woocommerce_after_order_itemmeta',     array( $this, 'booking_meda_data' ), 10, 1 );
            add_action( 'woocommerce_before_calculate_totals',  array( $this, 'before_calculate_price' ), 10, 1 );
			add_action( 'woocommerce_order_item_meta_end',      array( $this, 'booking_meda_data' ), 10, 1 );
			add_action( 'woocommerce_checkout_update_customer', array( $this, 'custom_checkout_update_customer' ), 10, 2 );
            
			add_action( 'woocommerce_order_status_cancelled',   array( $this, 'cancelOrder' ), 10, 1 );
            add_action( 'woocommerce_order_status_completed',   array( $this, 'paymentComplete' ), 10, 1 );
            add_action( 'woocommerce_order_status_on-hold',     array( $this, 'paymentComplete' ), 10, 1 );
            add_action( 'woocommerce_order_status_processing',  array( $this, 'paymentComplete' ), 10, 1 );
            add_action( 'woocommerce_order_status_refunded',    array( $this, 'cancelOrder' ), 10, 1 );
			add_action( 'woocommerce_deleted_order_items',    	array( $this, 'deletedOrder' ), 10, 1 );

            add_filter( 'woocommerce_checkout_get_value',       array( $this, 'update_billing_info' ), 10, 2 );
            add_filter( 'woocommerce_get_item_data',            array( $this, 'update_cart_meta_item' ), 10, 2 );
            add_filter( 'woocommerce_quantity_input_args',      array( $this, 'remove_quantity_field' ), 10, 2 );
			//add_filter( 'template_redirect',      			array( $this, 'redirect_after_payment' ), 10, 2 );
			add_filter( 'woocommerce_thankyou',      			array( $this, 'redirect_after_payment' ), 10, 2 );
			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'disable_available_payment_gateways' ), 10, 2 );
			//add_filter( 'woocommerce_is_sold_individually',      array( $this, 'remove_all_quantity_fields' ), 10, 2 );
			
			/*Remove product name and product link from cart page*/
			add_filter('woocommerce_cart_item_permalink','__return_false');
			add_filter( 'woocommerce_cart_item_name', '__return_false' );
			
			/*Remove cart item quantity field from checkout page*/
			add_filter('woocommerce_order_item_permalink','__return_false');
			add_filter( 'woocommerce_order_item_name', '__return_false' );
			add_filter('woocommerce_order_item_quantity_html','__return_false');
			
			/*Remove cart item quantity field from checkout page*/
			add_filter('woocommerce_checkout_cart_item_quantity','__return_false');

        }
    }
	
	/*public function remove_all_quantity_fields( $return, $product ) { return true; }*/
	
	public function custom_checkout_update_customer( $customer, $data ){

		if ( ! is_user_logged_in() || is_admin() ) return;
	
		// Get the user ID
		$user_id = $customer->get_id();
	
		// Get the default wordpress first name and last name (if they exist)
		$user_first_name = get_user_meta( $user_id, 'first_name', true );
		$user_last_name = get_user_meta( $user_id, 'last_name', true );
	
		if( empty( $user_first_name ) || empty( $user_last_name ) ) return;
	
		// We set the values by defaul worpress ones, before it's saved to DB
		$customer->set_first_name( $user_first_name );
		$customer->set_last_name( $user_last_name );
	}
	
	public function service_finder_add_to_woo_cart(){
	global $wpdb, $service_finder_Tables, $service_finder_Params, $current_user, $service_finder_options;
	
	$wootype = (!empty($_REQUEST['wootype'])) ? sanitize_text_field($_REQUEST['wootype']) : '';

	switch($wootype){
	case 'booking':
			$this->booking_add_to_cart($_REQUEST);
			break;
	case 'signup':
			$this->signup_add_to_cart($_REQUEST);
			break;
	case 'upgrade':
			$this->signup_add_to_cart($_REQUEST);
			break;
	case 'featured':
			$this->featured_add_to_cart($_REQUEST);
			break;
	case 'joblimit':
			$this->joblimit_add_to_cart($_REQUEST);
			break;
	case 'jobpostlimit':
			$this->jobpostlimit_add_to_cart($_REQUEST);
			break;		
	case 'claimbusiness':
			$this->claimbusiness_add_to_cart($_REQUEST);
			break;
	case 'invoice':
			$this->invoice_add_to_cart($_REQUEST);
			break;
	case 'wallet':
			$this->wallet_add_to_cart($_REQUEST);
			break;														
	}
	exit(0);
	}
	
	public function booking_add_to_cart( $postdata ){
	global $current_user, $service_finder_options;
		if(is_user_logged_in()){
			$wp_user_id = $current_user->ID;
		}else{
			$wp_user_id = 'NULL';
		}
		$providerid = (!empty($postdata['provider'])) ? sanitize_text_field($postdata['provider']) : 0;
		$productid = get_user_meta( $providerid, '_vendor_product_id', true );
		$is_vendor = get_user_meta( $providerid, 'is_vendor', true );
		if($productid != "" && $productid > 0 && $is_vendor = 'yes'){
		$this->product_id = $productid;
		}
		
		$firstname = (!empty($postdata['firstname'])) ? sanitize_text_field($postdata['firstname']) : '';
		$lastname = (!empty($postdata['lastname'])) ? sanitize_text_field($postdata['lastname']) : '';
		
		$fullname = $firstname.' '.$lastname;
		
		$totalcost = (!empty($postdata['totalcost'])) ? sanitize_text_field($postdata['totalcost']) : 0;
		$totaldiscount = (!empty($postdata['totaldiscount'])) ? sanitize_text_field($postdata['totaldiscount']) : 0;
		
		if(floatval($totalcost) >= floatval($totaldiscount)){
		$totalcost = floatval($totalcost) - floatval($totaldiscount);
		}else{
		$totalcost = floatval($totalcost);
		}
		
		if( class_exists( 'WC_Vendors' ) ) {
		$provider_commission = WCV_Commission::get_commission_rate( $this->product_id );
		$admin_fee_percentage = 100 - floatval($provider_commission);
		
		$adminfee = $totalcost * ($admin_fee_percentage/100);	
		
		}else{
					
		$admin_fee_type = (!empty($service_finder_options['admin-fee-type'])) ? $service_finder_options['admin-fee-type'] : 0;
		$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;
		$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;
		
		$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
		$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
		
		$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
		
		if($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'customer'){
			if($admin_fee_type == 'fixed'){
				$adminfee = $admin_fee_fixed;
			}elseif($admin_fee_type == 'percentage'){
				$adminfee = $totalcost * ($admin_fee_percentage/100);	
			}
		
			$totalcost = $totalcost + $adminfee;
		}elseif($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'provider'){
			
			if($admin_fee_type == 'fixed'){
				$adminfee = $admin_fee_fixed;
			}elseif($admin_fee_type == 'percentage'){
				$adminfee = $totalcost * ($admin_fee_percentage/100);	
			}
			
		}else{
			$adminfee = 0;
		}		
		}	
					
		$bookingdata = array(
						'wootype' => (!empty($postdata['wootype'])) ? sanitize_text_field($postdata['wootype']) : '',
						'choose_customer' => (!empty($postdata['choose_customer'])) ? sanitize_text_field($postdata['choose_customer']) : '',
						'bookingdate' => (!empty($postdata['selecteddate'])) ? sanitize_text_field($postdata['selecteddate']) : '',
						'boking-slot' => (!empty($postdata['boking-slot'])) ? sanitize_text_field($postdata['boking-slot']) : '',
						'jobid' => (!empty($postdata['jobid'])) ? sanitize_text_field($postdata['jobid']) : '',
						'quoteid' => (!empty($postdata['quoteid'])) ? sanitize_text_field($postdata['quoteid']) : '',
						'provider' => (!empty($postdata['provider'])) ? sanitize_text_field($postdata['provider']) : '',
						'memberid' => (!empty($postdata['memberid'])) ? sanitize_text_field($postdata['memberid']) : '',
						'servicearr' => (!empty($postdata['servicearr'])) ? $postdata['servicearr'] : '',
						'totalcost' => (!empty($postdata['totalcost'])) ? sanitize_text_field($postdata['totalcost']) : 0,
						'totaldiscount' => (!empty($postdata['totaldiscount'])) ? sanitize_text_field($postdata['totaldiscount']) : 0,
						'couponcode' => (!empty($postdata['couponcode'])) ? sanitize_text_field($postdata['couponcode']) : '',
						'adminfee' => $adminfee,
						'zipcode' => (!empty($postdata['zipcode'])) ? sanitize_text_field($postdata['zipcode']) : '',
						'region' => (!empty($postdata['region'])) ? sanitize_text_field($postdata['region']) : '',
						'shortdesc' => (!empty($postdata['shortdesc'])) ? sanitize_text_field($postdata['shortdesc']) : '',
						'wp_user_id' => $wp_user_id,
						'name' => $fullname, 
						'firstname' => (!empty($postdata['firstname'])) ? sanitize_text_field($postdata['firstname']) : '',
						'lastname' => (!empty($postdata['lastname'])) ? sanitize_text_field($postdata['lastname']) : '',
						'phone' => (!empty($postdata['phone'])) ? sanitize_text_field($postdata['phone']) : '',
						'phone2' => (!empty($postdata['phone2'])) ? sanitize_text_field($postdata['phone2']) : '',
						'email' => (!empty($postdata['email'])) ? sanitize_text_field($postdata['email']) : '',
						'address' => (!empty($postdata['address'])) ? sanitize_text_field($postdata['address']) : '',
						'apt' => (!empty($postdata['apt'])) ? sanitize_text_field($postdata['apt']) : '',
						'city' => (!empty($postdata['city'])) ? sanitize_text_field($postdata['city']) : '',
						'state' => (!empty($postdata['state'])) ? sanitize_text_field($postdata['state']) : '',
						'country' => (!empty($postdata['country'])) ? sanitize_text_field($postdata['country']) : '',
						'zipcode' => (!empty($postdata['zipcode'])) ? sanitize_text_field($postdata['zipcode']) : '',
						'region' => (!empty($postdata['region'])) ? sanitize_text_field($postdata['region']) : '',
						'shortdesc' => (!empty($postdata['shortdesc'])) ? sanitize_text_field($postdata['shortdesc']) : '',
						'userfname' => get_user_meta($wp_user_id,'first_name',true ),
						'userlname' => get_user_meta($wp_user_id,'last_name',true ),
						'service_perform_at' => (!empty($postdata['service_perform_at'])) ? sanitize_text_field($postdata['service_perform_at']) : '',
						'location' => (!empty($postdata['location'])) ? sanitize_text_field($postdata['location']) : ''
					);			
		
		WC()->cart->empty_cart(); 
		WC()->cart->add_to_cart( $this->product_id, 1, '', array(), array( 'wooextradata' => $bookingdata ) );
		$response = array( 'success' => true );
		wp_send_json( $response );
	}
	
	public function signup_add_to_cart( $postdata ){
		$response = null;
		$packagedata = $this->get_signup_package_info( $postdata );

		$result = array_merge($postdata, $packagedata);
		
		WC()->cart->empty_cart(); 
		WC()->cart->add_to_cart( $this->product_id, 1, '', array(), array( 'wooextradata' => $result ) );
		$response = array( 'success' => true );
		wp_send_json( $response );
	}
	
	public function featured_add_to_cart( $postdata ){
	global $wpdb,$service_finder_Tables;	
		$response = null;
		
		$feature_id = (isset($postdata['feature_id'])) ? sanitize_text_field($postdata['feature_id']) : '';
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$feature_id));
		
		$extradata = array(
						'amount' => (!empty($row->amount)) ? $row->amount : 0,
						'provider_id' => (!empty($row->provider_id)) ? $row->provider_id : 0,
						'feature_id' => (!empty($postdata['feature_id'])) ? sanitize_text_field($postdata['feature_id']) : '',
						'wootype' => (!empty($postdata['wootype'])) ? sanitize_text_field($postdata['wootype']) : '',
					);		
		
		WC()->cart->empty_cart(); 
		WC()->cart->add_to_cart( $this->product_id, 1, '', array(), array( 'wooextradata' => $extradata ) );
		$response = array( 'success' => true );
		wp_send_json( $response );
	}
	
	public function jobpostlimit_add_to_cart( $postdata ){
	global $wpdb,$service_finder_Tables,$service_finder_options;	
		$response = null;
		
		$customer_id = (isset($postdata['customer_id'])) ? sanitize_text_field($postdata['customer_id']) : '';
		$plan = (isset($postdata['plan'])) ? sanitize_text_field($postdata['plan']) : '';
		
		$planprice = (!empty($service_finder_options['job-post-plan'.$plan.'-price'])) ? $service_finder_options['job-post-plan'.$plan.'-price'] : '';
		$planlimit = (!empty($service_finder_options['job-post-plan'.$plan.'-limit'])) ? $service_finder_options['job-post-plan'.$plan.'-limit'] : 0;
		$planname = (!empty($service_finder_options['job-post-plan'.$plan.'-name'])) ? $service_finder_options['job-post-plan'.$plan.'-name'] : 0;
		
		$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$customer_id.'"');
		if(!empty($row)){
			$paidlimit = $planlimit + $row->paid_limits;
			$available_limits = $planlimit + $row->available_limits;
		}else{
			$paidlimit = $planlimit;
			$available_limits = $planlimit;
		}
		
		$extradata = array(
						'planprice' => $planprice,
						'planlimit' => $planlimit,
						'customer_id' => $customer_id,
						'plan' => $plan,
						'planname' => $planname,
						'paidlimit' => $paidlimit,
						'available_limits' => $available_limits,
						'wootype' => (!empty($postdata['wootype'])) ? sanitize_text_field($postdata['wootype']) : '',
					);		
		
		WC()->cart->empty_cart(); 
		WC()->cart->add_to_cart( $this->product_id, 1, '', array(), array( 'wooextradata' => $extradata ) );
		$response = array( 'success' => true );
		wp_send_json( $response );
	}
	
	public function joblimit_add_to_cart( $postdata ){
	global $wpdb,$service_finder_Tables,$service_finder_options;	
		$response = null;
		
		$provider_id = (isset($postdata['provider_id'])) ? sanitize_text_field($postdata['provider_id']) : '';
		$plan = (isset($postdata['plan'])) ? sanitize_text_field($postdata['plan']) : '';
		
		$planprice = (!empty($service_finder_options['plan'.$plan.'-price'])) ? $service_finder_options['plan'.$plan.'-price'] : '';
		$planlimit = (!empty($service_finder_options['plan'.$plan.'-limit'])) ? $service_finder_options['plan'.$plan.'-limit'] : 0;
		$planname = (!empty($service_finder_options['plan'.$plan.'-name'])) ? $service_finder_options['plan'.$plan.'-name'] : 0;
		
		$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$provider_id.'"');
		if(!empty($row)){
			$paidlimit = $planlimit + $row->paid_limits;
			$available_limits = $planlimit + $row->available_limits;
		}else{
			$paidlimit = $planlimit;
			$available_limits = $planlimit;
		}
		
		$extradata = array(
						'planprice' => $planprice,
						'planlimit' => $planlimit,
						'provider_id' => $provider_id,
						'plan' => $plan,
						'planname' => $planname,
						'paidlimit' => $paidlimit,
						'available_limits' => $available_limits,
						'wootype' => (!empty($postdata['wootype'])) ? sanitize_text_field($postdata['wootype']) : '',
					);		
		
		WC()->cart->empty_cart(); 
		WC()->cart->add_to_cart( $this->product_id, 1, '', array(), array( 'wooextradata' => $extradata ) );
		$response = array( 'success' => true );
		wp_send_json( $response );
	}
	
	public function claimbusiness_add_to_cart( $postdata ){
		$response = null;
		$packagedata = $this->get_signup_package_info( $postdata );
		
		$result = array_merge($postdata, $packagedata);
		
		WC()->cart->empty_cart(); 
		WC()->cart->add_to_cart( $this->product_id, 1, '', array(), array( 'wooextradata' => $result ) );
		$response = array( 'success' => true );
		wp_send_json( $response );
	}
	
	public function invoice_add_to_cart( $postdata ){
	global $wpdb,$service_finder_Tables;	
		$response = null;
		
		WC()->cart->empty_cart(); 
		WC()->cart->add_to_cart( $this->product_id, 1, '', array(), array( 'wooextradata' => $postdata ) );
		$response = array( 'success' => true );
		wp_send_json( $response );
	}
	
	public function wallet_add_to_cart( $postdata ){
	global $wpdb,$service_finder_Tables;	
		$response = null;
		
		WC()->cart->empty_cart(); 
		WC()->cart->add_to_cart( $this->product_id, 1, '', array(), array( 'wooextradata' => $postdata ) );
		$response = array( 'success' => true );
		wp_send_json( $response );
	}
	
	public function get_signup_package_info( $postdata ){
	global $service_finder_options;
	
		$roleNum = 1;
		$rolePrice = '0';
		$price = '0';
		$packageName = '';
		$upgrade = false;
		
		$wootype = (!empty($postdata['wootype'])) ? sanitize_text_field($postdata['wootype']) : '';
		
		if($wootype == 'upgrade'){
			$upgrade = true;
			$currentRole =  get_user_meta($postdata['user_id'],'provider_role',true);
			$currentPayType = get_user_meta($postdata['user_id'],'pay_type',true);
			if($currentPayType == 'single'){
				$paidAmount =  get_user_meta($postdata['user_id'],'profile_amt',true);
			}
			$userId = $postdata['user_id'];
		} elseif($wootype == 'claimbusiness'){
			$upgrade = false;
			$userId = (isset($postdata['profileid'])) ? esc_html($postdata['profileid']) : '';
			$currentRole =  get_user_meta($userId,'provider_role',true);
			$currentPayType = get_user_meta($userId,'pay_type',true);
			if($currentPayType == 'single'){
				$paidAmount =  get_user_meta($userId,'profile_amt',true);
			}

		} else {
			$userId = '';
			$currentPayType = '';
			$currentRole = '';
		}
		
		$role = (isset($postdata['provider-role'])) ? sanitize_text_field($postdata['provider-role']) : '';
		if(isset($role)){
			if (($role == "package_1") || ($role == "package_2") || ($role == "package_3")){
				$roleNum = intval(substr($role, 8));
				switch ($role) {
					case "package_1":
						if(isset($service_finder_options['package1-price']) && trim($service_finder_options['package1-price']) !== '0') {
							$rolePrice = $service_finder_options['package1-price'];
							$packageName = $service_finder_options['package1-name'];
							if($service_finder_options['payment-type'] == 'single'){
							$expire_limit = $service_finder_options['package1-expday'];
							}
							if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
							$price = floatval($service_finder_options['package1-price']) - floatval($paidAmount);							
							}else{
							$price = trim($service_finder_options['package1-price']);								
							}
						}
						break;
					case "package_2":
						if(isset($service_finder_options['package2-price']) && trim($service_finder_options['package2-price']) !== '0') {
							$rolePrice = $service_finder_options['package2-price'];
							$packageName = $service_finder_options['package2-name'];
							if($service_finder_options['payment-type'] == 'single'){
							$expire_limit = $service_finder_options['package2-expday'];
							}
							if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
							$price = floatval($service_finder_options['package2-price']) - floatval($paidAmount);							
							}else{
							$price = trim($service_finder_options['package2-price']);								
							}
						}
						break;
					case "package_3":
						if(isset($service_finder_options['package3-price']) && trim($service_finder_options['package3-price']) !== '0') {
							$rolePrice = $service_finder_options['package3-price'];
							$packageName = $service_finder_options['package3-name'];
							if($service_finder_options['payment-type'] == 'single'){
							$expire_limit = $service_finder_options['package3-expday'];
							}
							if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
							$price = floatval($service_finder_options['package3-price']) - floatval($paidAmount);							
							}else{
							$price = trim($service_finder_options['package3-price']);								
							}
						}
						break;
					default:
						break;
				}
				
				return $response = array(
					'rolePrice' => $rolePrice,
					'packageName' => $packageName,
					'expire_limit' => $expire_limit,
					'price' => $price,
					'roleNum' => $roleNum,
					'role' => $role,
					'currentRole' => $currentRole,
				);
			}
		}
	
	}
	
 	public function add_booking_meda_data( $item_id, $values, $wc_key ){
        if ( isset ( $values['wooextradata'] ) ) {
            wc_update_order_item_meta( $item_id, 'wooextradata', $values['wooextradata'] );
        }
    }
	
	public function booking_meda_data( $item_id ){
        $data = wc_get_order_item_meta( $item_id, 'wooextradata' );
        if ( $data ) {
            $other_data = $this->update_cart_meta_item( array(), array( 'wooextradata' => $data ) );
			if(!empty($other_data)){
            echo '<br/>' . $other_data[0]['name'] . '<br/>' . nl2br( $other_data[0]['value'] );
			}
        }
    }
	
	public function before_calculate_price( $cart_object ){
	global $service_finder_options;
	$deposit = $due = 0;
        foreach ( $cart_object->cart_contents as $wc_key => $wc_item ) {
            if ( isset ( $wc_item['wooextradata'] ) ) {
			
				$wootype = $wc_item['wooextradata']['wootype'];
				switch($wootype){
				case 'booking':
						$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
						$admin_fee_type = (!empty($service_finder_options['admin-fee-type'])) ? $service_finder_options['admin-fee-type'] : 0;
						$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;
						$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;
						
						$admin_fee_label = (!empty($service_finder_options['admin-fee-label'])) ? $service_finder_options['admin-fee-label'] : esc_html__('Admin Fee', 'service-finder');
						$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
						$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
						
						if(!class_exists( 'WC_Vendors' ) && $charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'customer'){
						
						$totalcost = $wc_item['wooextradata']['totalcost'];
						$totaldiscount = $wc_item['wooextradata']['totaldiscount'];
						$adminfee = $wc_item['wooextradata']['adminfee'];
		
						if(floatval($totalcost) >= floatval($totaldiscount)){
						$totalcost = floatval($totalcost) - floatval($totaldiscount);
						}else{
						$totalcost = floatval($totalcost);
						}
						
						$totalamount = floatval($totalcost) + floatval($adminfee);
						
						}else{
						
						$totalcost = $wc_item['wooextradata']['totalcost'];
						$totaldiscount = $wc_item['wooextradata']['totaldiscount'];
		
						if(floatval($totalcost) >= floatval($totaldiscount)){
						$totalcost = floatval($totalcost) - floatval($totaldiscount);
						}else{
						$totalcost = floatval($totalcost);
						}
						
						$totalamount = floatval($totalcost);
						}
						
						if(service_finder_has_pay_only_admin_fee() && $adminfee > 0)
						{
							$totalamount = $adminfee;
						}
				
						$wc_item['data']->set_price( $totalamount );
						break;
				case 'signup':
						$wc_item['data']->set_price( $wc_item['wooextradata']['price'] );
						break;
				case 'upgrade':
						$wc_item['data']->set_price( $wc_item['wooextradata']['price'] );
						break;
				case 'featured':
						$wc_item['data']->set_price( $wc_item['wooextradata']['amount'] );
						break;
				case 'joblimit':
						$wc_item['data']->set_price( $wc_item['wooextradata']['planprice'] );
						break;
				case 'jobpostlimit':
						$wc_item['data']->set_price( $wc_item['wooextradata']['planprice'] );
						break;		
				case 'claimbusiness':
						$wc_item['data']->set_price( $wc_item['wooextradata']['price'] );
						break;
				case 'invoice':
						$wc_item['data']->set_price( $wc_item['wooextradata']['amount'] );
						break;
				case 'wallet':
						$wc_item['data']->set_price( $wc_item['wooextradata']['amount'] );
						break;														
				}
                
            }
        }
    }
	
	public function paymentComplete( $order_id ){
		global $wpdb;
        $order = new \WC_Order( $order_id );
		$order_status = $order->get_status();
		if ( $order->get_status() != 'failed' ) {
        foreach ( $order->get_items() as $item_id => $order_item ) {
            $wooextradata = wc_get_order_item_meta( $item_id, 'wooextradata' );
			$wootype = $wooextradata['wootype'];
			switch($wootype){
			case 'booking':
					require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
					$saveBooking = new SERVICE_FINDER_BookNow();
					$saveBooking->service_finder_SaveWooBooking($wooextradata,$order_id,$item_id);
					$wooextradata['processed'] = true;	
					wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
					
					if($order_status == 'completed'){
					$wooextradata['completed'] = true;	
					wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
					}
					break;
			case 'signup':
					$this->signup_after_payment($wooextradata,$order_id,$item_id);
					break;
			case 'upgrade':
					$userId = $wooextradata['user_id'];
					$this->upgrade_after_payment($userId,$wooextradata,$order_id,$item_id);
					break;
			case 'featured':
					$this->update_after_featured_payment($wooextradata,$order_id,$item_id);
					break;
			case 'joblimit':
					$this->update_after_joblimit_payment($wooextradata,$order_id,$item_id);
					break;
			case 'jobpostlimit':
					$this->update_after_jobpostlimit_payment($wooextradata,$order_id,$item_id);
					break;		
			case 'claimbusiness':
					$userId = $wooextradata['profileid'];
					$this->update_after_claimed_payment($userId,$wooextradata,$order_id,$item_id);
					break;
			case 'invoice':
					$this->update_after_invoice_payment($wooextradata,$order_id,$item_id);
					break;
			case 'wallet':
					$this->update_after_wallet_payment($wooextradata,$order_id,$item_id);
					break;														
			}
			
        }
		}
    }
	
	public function cancelOrder( $order_id ){
		global $wpdb;
        $order = new \WC_Order( $order_id );
		if ( $order->get_status() != 'failed' ) {
        foreach ( $order->get_items() as $item_id => $order_item ) {
            $wooextradata = wc_get_order_item_meta( $item_id, 'wooextradata' );
			$wootype = $wooextradata['wootype'];
			switch($wootype){
			case 'booking':
					$this->booking_payment_cancel($wooextradata,$order_id,$item_id);
					break;
			case 'signup':
					$this->signup_payment_cancel($wooextradata,$order_id,$item_id);
					break;
			case 'upgrade':
					$this->upgrade_payment_cancel($wooextradata,$order_id,$item_id);
					break;
			case 'featured':
					$this->featured_payment_cancel($wooextradata,$order_id,$item_id);
					break;
			case 'joblimit':
					$this->update_after_joblimit_cancel_payment($wooextradata,$order_id,$item_id);
					break;
			case 'jobpostlimit':
					$this->update_after_jobpostlimit_cancel_payment($wooextradata,$order_id,$item_id);
					break;		
			case 'claimbusiness':
					$userId = $wooextradata['profileid'];
					$this->claimed_payment_cancel($userId,$wooextradata,$order_id,$item_id);
					break;
			case 'invoice':
					$this->invoice_payment_cancel($wooextradata,$order_id,$item_id);
					break;
			case 'invoice':
					$this->wallet_payment_cancel($wooextradata,$order_id,$item_id);
					break;														
			}
			
        }
		}
    }
	
	public function deletedOrder( $order_id ){
		global $wpdb;

        $order = new \WC_Order( $order_id );
		if ( $order->get_status() != 'failed' ) {
        foreach ( $order->get_items() as $item_id => $order_item ) {
            $wooextradata = wc_get_order_item_meta( $item_id, 'wooextradata' );
			$wootype = $wooextradata['wootype'];
			switch($wootype){
			case 'upgrade':
					$this->delete_upgrade_request($order_id);
					break;	
			default:
					break;														
			}
			
        }
		}
    }
	
	public function delete_upgrade_request( $order_id ){
		global $wpdb, $service_finder_Tables;

		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'usermeta WHERE `meta_value` = %d AND `meta_key` = "order_id"',$order_id));
		
		if(!empty($row)){
			$userid = $row->user_id;
			delete_user_meta($userid,'upgrade_request_status');
			delete_user_meta($userid,'order_id');
		}

	}
	
	public function wallet_payment_cancel( $wooextradata,$order_id,$item_id ){
		
		global $service_finder_options, $wpdb, $service_finder_Tables; 
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		
		if ( $wooextradata && ! isset ( $wooextradata['completed'] ) ) {
		
			$wooextradata['cancelled'] = true;	
			wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );

		}

	}
	
	public function signup_payment_cancel( $wooextradata,$order_id,$item_id ){
		
		global $service_finder_options, $wpdb, $service_finder_Tables; 
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		
		if ( $wooextradata && ! isset ( $wooextradata['completed'] ) ) {
		
		$row = $wpdb->get_row($wpdb->prepare('SELECT `user_id` FROM '.$wpdb->prefix.'usermeta WHERE `meta_value` = %d AND `meta_key` = "order_id"',$order_id));
		if(!empty($row)){
		$userId = $row->user_id;
		
		wp_delete_user( $userId );
		$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->providers." WHERE wp_user_id = %d",$userId);
		$query=$wpdb->query($sql);
		
		$wooextradata['cancelled'] = true;	
		wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
		}
		
		}

	}
	
	public function upgrade_payment_cancel( $wooextradata,$order_id,$item_id ){
		
		global $service_finder_options, $wpdb, $service_finder_Tables; 
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		
		if ( $wooextradata && ! isset ( $wooextradata['completed'] ) ) {
		
			$row = $wpdb->get_row($wpdb->prepare('SELECT `user_id` FROM '.$wpdb->prefix.'usermeta WHERE `meta_value` = %d AND `meta_key` = "order_id"',$order_id));
	
			if(!empty($row)){
			$userId = $row->user_id;
			update_user_meta($userId, 'upgrade_request_status','cancelled');
			
			$wooextradata['cancelled'] = true;	
			wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			}

		}

	}
	
	public function claimed_payment_cancel( $wooextradata,$order_id,$item_id ){
		
		global $service_finder_options, $wpdb, $service_finder_Tables; 
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		
		if ( $wooextradata && ! isset ( $wooextradata['completed'] ) ) {
		
			$row = $wpdb->get_row($wpdb->prepare('SELECT `user_id` FROM '.$wpdb->prefix.'usermeta WHERE `meta_value` = %d AND `meta_key` = "claimed_order_id"',$order_id));
			if(!empty($row)){
			
			update_user_meta($userId, 'claimed_request_status','cancelled');
			
			$wooextradata['cancelled'] = true;	
			wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			}

		}

	}
	
	public function invoice_payment_cancel( $wooextradata,$order_id,$item_id ){
		
		global $service_finder_options, $wpdb, $service_finder_Tables; 
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		
		if ( $wooextradata && ! isset ( $wooextradata['completed'] ) ) {
		
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->invoice.' WHERE `status` = "on-hold" AND `txnid` = %d',$order_id));
			if(!empty($row)){
			
				$invoiceid = $row->id;
				$provider_id = $row->provider_id;
				$customer_email = $row->customer_email;
				
				$data = array(
				'payment_mode' => 'woocommerce',
				'payment_type' => $payment_method,
				'status' => 'canceled',
				'txnid' => $order_id,
				);
				
				$where = array(
				'id' => $invoiceid
				);
				
				$wpdb->update($service_finder_Tables->invoice,wp_unslash($data),$where);
				
				if(function_exists('service_finder_add_notices')) {
		
					$noticedata = array(
							'provider_id' => $provider_id,
							'target_id' => $invoiceid, 
							'topic' => 'Invoice Cancelled',
							'title' => esc_html__('Invoice Cancelled', 'service-finder'),
							'notice' => sprintf( esc_html__('Payment failed via wire transfer.', 'service-finder'), $customer_email ),
							);
					service_finder_add_notices($noticedata);
				
				}
				
				service_finder_SendInvoicePaidMailToProvider($invoiceid);
				service_finder_SendInvoicePaidMailToCustomer($invoiceid);
				
				$wooextradata['cancelled'] = true;	
				wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			
			
			}

		}

	}
	
	public function booking_payment_cancel( $wooextradata,$order_id,$item_id ){
		
		global $service_finder_options, $wpdb, $service_finder_Tables; 
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		
		if ( $wooextradata && ! isset ( $wooextradata['completed'] ) ) {
		
			$existingbooking = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `payment_type` = "woocommerce" AND `order_id` = %d',$order_id));
			if(!empty($existingbooking)){
				$updatedata = array(
							'status' => 'Cancel'
							);
				$where = array(
							'order_id' => $order_id,
							'payment_type' => 'woocommerce'
							);			
			
				$wpdb->update($service_finder_Tables->bookings,wp_unslash($updatedata),$where);
				
				$wooextradata['cancelled'] = true;	
				wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			}

		}

	}
	
	public function featured_payment_cancel( $wooextradata,$order_id,$item_id ){
		
		global $service_finder_options, $wpdb, $service_finder_Tables; 
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		
		if ( $wooextradata && ! isset ( $wooextradata['completed'] ) ) {
		
			$existingrequest = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `payment_mode` = "woocommerce" AND `status` = "on-hold" AND `txnid` = %d',$order_id));
			if(!empty($existingrequest)){
				$data = array(
						'status' => $order_status,
						);
				$where = array(
						'id' => esc_attr($existingrequest->id)
				);
				$res = $wpdb->update($service_finder_Tables->feature,wp_unslash($data),$where);
	
				$wooextradata['cancelled'] = true;	
				wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			}

		}

	}
	
	public function update_after_joblimit_cancel_payment( $wooextradata,$order_id,$item_id ){
		
		global $service_finder_options, $wpdb, $service_finder_Tables; 
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		
		
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `txn_id` = %d',$order_id));
		if(!empty($row)){
			
			$requeststatus = get_user_meta($row->provider_id,'job_connect_request_status',true);
			
			if($requeststatus == 'pending'){
			
			$data = array(
					'payment_status' => $order_status,
					);
			$where = array(
					'txn_id' => $row->txn_id
			);
			$res = $wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);
			
			$paydate = date('Y-m-d h:i:s');
			$txndata = array(
					'payment_status' => $order_status,
					);
			$where = array(
					'txn_id' => $row->txn_id
			);
			$res = $wpdb->update($service_finder_Tables->transaction,wp_unslash($data),$where);
			
			update_user_meta($row->provider_id, 'job_connect_request_status','cancelled');
			//delete_user_meta($row->provider_id, 'job_connect_request');
			
			$this->send_mail_after_joblimit_connect_purchase_declined( $row->provider_id );
			
			}
					
		}

	}
	
	public function update_after_jobpostlimit_cancel_payment( $wooextradata,$order_id,$item_id ){
		global $service_finder_options, $wpdb, $service_finder_Tables; 
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		
		
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `txn_id` = %d',$order_id));
		if(!empty($row)){
			
			$requeststatus = get_user_meta($row->provider_id,'job_connect_request_status',true);
			
			if($requeststatus == 'pending'){
			
			$data = array(
					'payment_status' => $order_status,
					);
			$where = array(
					'txn_id' => $row->txn_id
			);
			$res = $wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);
			
			$paydate = date('Y-m-d h:i:s');
			$txndata = array(
					'payment_status' => $order_status,
					);
			$where = array(
					'txn_id' => $row->txn_id
			);
			$res = $wpdb->update($service_finder_Tables->transaction,wp_unslash($data),$where);
			
			update_user_meta($row->provider_id, 'job_connect_request_status','cancelled');
			//delete_user_meta($row->provider_id, 'job_connect_request');
			
			$this->send_mail_after_jobpost_limit_connect_purchase_declined( $row->provider_id );
			
			}
					
		}
		
	}
	
	public function send_mail_after_joblimit_connect_purchase_declined( $userid ){
		global $wpdb, $service_finder_Tables, $service_finder_options;
		
		$email = service_finder_getProviderEmail($userid);
		
		$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');	
		
		$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$userid.'"');
		$payment_method = '';
		if(!empty($row)){
		$payment_method = $row->payment_method;
		}
		
		$requestdata = get_user_meta($userid,'job_connect_request',true);
		$upgrade_plan = $requestdata['current_plan'];
		$upgrade_plan = (!empty($service_finder_options['plan'.$upgrade_plan.'-name'])) ? $service_finder_options['plan'.$upgrade_plan.'-name'] : '';
		
		if(!empty($service_finder_options['joblimit-connect-purchase-declined-message'])){
			$message = $service_finder_options['joblimit-connect-purchase-declined-message'];
		}else{
			$message = esc_html__('Dear ', 'service-finder').esc_html($providerreplacestring).',';
			$message .= esc_html__('Your Job Connect Upgraded Request Declined', 'service-finder');
			$message .= esc_html__('Name: ', 'service-finder').'%USERNAME%';
			$message .= esc_html__('Plan Name: ', 'service-finder').'%PLANNAME%';
			$message .= esc_html__('Payment Method: ', 'service-finder').'%PAYMENTMETHOD%';
		}
		
		if($service_finder_options['joblimit-connect-purchase-declined-subject'] != ""){
			$msg_subject = $service_finder_options['joblimit-connect-purchase-declined-subject'];
		}else{
			$msg_subject = esc_html__('Job Connect Request Declined Notification', 'service-finder');
		}
		
		$tokens = array('%USERNAME%','%PLANNAME%','%PAYMENTMETHOD%');
		$replacements = array(service_finder_getProviderName($userid),$upgrade_plan,service_finder_translate_static_status_string($payment_method));
		$msg_body = str_replace($tokens,$replacements,$message);
		
		if(function_exists('service_finder_add_notices')) {
	
			$noticedata = array(
					'provider_id' => $userid,
					'target_id' => $row->id, 
					'topic' => 'Job Post Connect',
					'title' => esc_html__('Job Post Connect', 'service-finder'),
					'notice' => esc_html__('Request for job post connect plan upgrade cancelled.', 'service-finder')
					);
			service_finder_add_notices($noticedata);
		
		}
		
		service_finder_wpmailer($email,$msg_subject,$msg_body);
	}
	
	public function send_mail_after_jobpost_limit_connect_purchase_declined( $userid ){
		global $wpdb, $service_finder_Tables, $service_finder_options;
		
		$email = service_finder_getCustomerEmail($userid);
		
		$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customer', 'service-finder');	
		
		$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$userid.'"');
		$payment_method = '';
		if(!empty($row)){
		$payment_method = $row->payment_method;
		}
		
		$requestdata = get_user_meta($userid,'job_connect_request',true);
		$upgrade_plan = $requestdata['current_plan'];
		$upgrade_plan = (!empty($service_finder_options['job-post-plan'.$upgrade_plan.'-name'])) ? $service_finder_options['job-post-plan'.$upgrade_plan.'-name'] : '';
		
		$msg_body = $message;
		$msg_subject = esc_html__('Job Connect Request Declined Notification', 'service-finder');
		
		if(!empty($service_finder_options['jobpost-connect-purchase-declined-message'])){
			$message = $service_finder_options['jobpost-connect-purchase-declined-message'];
		}else{
			$message = esc_html__('Dear ', 'service-finder').esc_html($customerreplacestring).',';
			$message .= esc_html__('Your Job Connect Upgraded Request Declined', 'service-finder');
			$message .= esc_html__('Name: ', 'service-finder').'%USERNAME%';
			$message .= esc_html__('Plan Name: ', 'service-finder').'%PLANNAME%';
			$message .= esc_html__('Payment Method: ', 'service-finder').'%PAYMENTMETHOD%';
		}
		
		if($service_finder_options['jobpost-connect-purchase-declined-subject'] != ""){
			$msg_subject = $service_finder_options['jobpost-connect-purchase-declined-subject'];
		}else{
			$msg_subject = esc_html__('Job Connect Request Declined Notification', 'service-finder');
		}
		
		$tokens = array('%USERNAME%','%PLANNAME%','%PAYMENTMETHOD%');
		$replacements = array(service_finder_getProviderName($userid),$upgrade_plan,service_finder_translate_static_status_string($payment_method));
		$msg_body = str_replace($tokens,$replacements,$message);
		
		if(function_exists('service_finder_add_notices')) {
	
			$noticedata = array(
					'customer_id' => $userid,
					'target_id' => $row->id, 
					'topic' => 'Job Post Connect',
					'title' => esc_html__('Job Post Connect', 'service-finder'),
					'notice' => esc_html__('Request for job post connect plan upgrade cancelled.', 'service-finder')
					);
			service_finder_add_notices($noticedata);
		
		}
		
		service_finder_wpmailer($email,$msg_subject,$msg_body);
	
	}
	
	public function signup_after_payment( $wooextradata,$order_id,$item_id ){
		global $service_finder_options, $wpdb, $service_finder_Tables;
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		
		$row = $wpdb->get_row($wpdb->prepare('SELECT `user_id` FROM '.$wpdb->prefix.'usermeta WHERE `meta_value` = %d AND `meta_key` = "order_id"',$order_id));
		if(!empty($row)){
		$userId = $row->user_id;
		}else{
		$userId = service_finder_sedateUserRegistration($wooextradata);
		}
		
		if ( $wooextradata && ! isset ( $wooextradata['processed'] ) ) {
			if($order_status == 'on-hold'){
				$data = array(
							'account_blocked' => 'yes',
						);
				$where = array(
							'wp_user_id' => $userId,
						);		
		
				$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
				
				
			}elseif($order_status == 'completed' || $order_status == 'processing'){
				$data = array(
							'account_blocked' => '',
						);
				$where = array(
							'wp_user_id' => $userId,
						);		
		
				$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
			}
			
			if($order_status == 'completed' || $order_status == 'on-hold' || $order_status == 'processing'){
				$this->update_signup_data( $userId,$wooextradata,$order_id,$payment_method );
			} 
			
			$wooextradata['processed'] = true;	
			wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			
			if($order_status == 'completed'){
			$wooextradata['completed'] = true;	
			wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			}
		}else{
		
			if(($order_status == 'completed' || $order_status == 'processing') && $wooextradata && ! isset ( $wooextradata['completed'] ) && ! isset ( $wooextradata['cancelled'] )){
				
				$data = array(
							'account_blocked' => '',
						);
				$where = array(
							'wp_user_id' => $userId,
						);		
		
				$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
				
				$this->update_signup_data( $userId,$wooextradata,$order_id,$payment_method );
				
				$wooextradata['completed'] = true;	
			    wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			}
		
		}
		
		

	}
	
	public function update_signup_data( $userId,$wooextradata,$order_id,$payment_method ){
		// set role
		$user = new WP_User( $userId );
		$user->set_role('Provider');
		
		update_user_meta( $userId, 'provider_activation_time', array( 'role' => $wooextradata['role'], 'time' => time()) );
		
		update_user_meta($userId, 'order_id', $order_id);
		if($wooextradata['expire_limit'] > 0){
			update_user_meta($userId, 'expire_limit', $wooextradata['expire_limit']);
		}else{
			delete_user_meta($userId, 'expire_limit');
		}
		
		update_user_meta( $userId, 'provider_role', $wooextradata['role'] );
		update_user_meta($userId, 'profile_amt',$wooextradata['rolePrice']);
		update_user_meta( $userId, 'pay_type', 'single' );
		$roleNum = $wooextradata['roleNum'];
		$roleName = $service_finder_options['package'.$roleNum.'-name'];
		update_user_meta( $userId, 'payment_type', 'woocommerce' );
		update_user_meta( $userId, 'payment_mode', $payment_method );
		
		$paymode = 'woocommerce';
		$userInfo = service_finder_getUserInfo($userId);
		$username = $wooextradata['signup_user_name'];
		$useremail = $wooextradata['signup_user_email'];
		$args = array(
				'username' => (!empty($username)) ? $username : '',
				'email' => (!empty($useremail)) ? $useremail : '',
				'address' => (!empty($userInfo['address'])) ? $userInfo['address'] : '',
				'city' => (!empty($userInfo['city'])) ? $userInfo['city'] : '',
				'country' => (!empty($userInfo['country'])) ? $userInfo['country'] : '',
				'zipcode' => (!empty($userInfo['zipcode'])) ? $userInfo['zipcode'] : '',
				'category' => (!empty($userInfo['categoryname'])) ? $userInfo['categoryname'] : '',
				'package_name' => $roleName,
				'payment_type' => $paymode
				);
				
		service_finder_sendProviderEmail($args);
		service_finder_sendRegMailToUser($username,$useremail);
	}
	
	public function upgrade_after_payment( $userId,$wooextradata,$order_id,$item_id ){
		global $service_finder_options, $wpdb, $service_finder_Tables; 
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		
		if ( $wooextradata && ! isset ( $wooextradata['processed'] ) ) {
			if($order_status == 'on-hold'){
			
			$wiredupgrade = array();
			$user = new WP_User( $userId );
			$user->set_role('Provider');
			
			$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$userId));
			$userInfo = service_finder_getUserInfo($userId);
			
			$roleNum = $wooextradata['roleNum'];
			$expire_limit = $wooextradata['expire_limit'];
			
			$wiredupgrade['payment_type'] = 'woocommerce';
			$wiredupgrade['payment_mode'] = $payment_method;
			
			$wiredupgrade['current_package'] = $wooextradata['currentRole'];
			
			$wiredupgrade['price'] = $wooextradata['price'];
			$wiredupgrade['time'] = time();
			
			$invoiceid = $order_id;
						
			$wiredupgrade['wired_invoiceid'] = $invoiceid;
			
			$wiredupgrade['recurring_profile_type'] = '';
			$wiredupgrade['provider_role'] = $wooextradata['role'];
			
			if($expire_limit > 0){
			$wiredupgrade['expire_limit'] = $expire_limit;
			}
			
			$roleNum = intval(substr($wooextradata['role'], 8));
			$roleName = (!empty($service_finder_options['package'.$roleNum.'-name'])) ? $service_finder_options['package'.$roleNum.'-name'] : '';
			
			if($roleNum == 0){
				update_user_meta($userId, 'trial_package', 'yes');
				$wiredupgrade['trial_package'] = 'yes';
			}
			
			$userInfo = service_finder_getUserInfo($userId);
			$paymentstatus = 'Wire Transfer';
			
			update_user_meta($userId, 'upgrade_request',$wiredupgrade);
			update_user_meta($userId, 'upgrade_request_status','pending');
			update_user_meta($userId, 'order_id', $order_id);
			update_user_meta( $userId, 'payment_mode', 'woocommerce' );
	
			$primarycategory = get_user_meta($userId, 'primary_category',true);
			$args = array(
					'username' => $userdata->user_login,
					'email' => $userdata->user_email,
					'address' => $userInfo['address'],
					'city' => $userInfo['city'],
					'country' => $userInfo['country'],
					'zipcode' => $userInfo['zipcode'],
					'category' => service_finder_getCategoryNameviaSql($primarycategory),
					'package_name' => $roleName,
					'payment_type' => $paymentstatus
					);
					
			service_finder_sendWiredUpgradeMailToProvider($userdata->user_login,$userdata->user_email,$args,$invoiceid);
			service_finder_sendProviderWiredUpgradeEmail($args,$invoiceid);		
			
			}elseif($order_status == 'completed' || $order_status == 'processing'){
			
			$user = new WP_User( $userId );
			$user->set_role('Provider');
			
			update_user_meta( $userId, 'provider_activation_time', array( 'role' => $wooextradata['role'], 'time' => time()) );
			
			update_user_meta($userId, 'order_id', $order_id);
			if($wooextradata['expire_limit'] > 0){
				update_user_meta($userId, 'expire_limit', $wooextradata['expire_limit']);
			}else{
				delete_user_meta($userId, 'expire_limit');
			}
			update_user_meta( $userId, 'provider_role', $wooextradata['role'] );
			update_user_meta($userId, 'profile_amt',$wooextradata['rolePrice']);
			update_user_meta( $userId, 'pay_type', 'single' );
			$roleNum = $wooextradata['roleNum'];
			$roleName = $service_finder_options['package'.$roleNum.'-name'];
			update_user_meta( $userId, 'payment_type', 'woocommerce' );
			update_user_meta( $userId, 'payment_mode', $wooextradata['payment_mode'] );
			
			$paymode = 'woocommerce';
			$userInfo = service_finder_getUserInfo($userId);
			
			$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$userId));
			
			$username = $userdata->user_login;
			$useremail = $userdata->user_email;
			$args = array(
					'username' => (!empty($username)) ? $username : '',
					'email' => (!empty($useremail)) ? $useremail : '',
					'address' => (!empty($userInfo['address'])) ? $userInfo['address'] : '',
					'city' => (!empty($userInfo['city'])) ? $userInfo['city'] : '',
					'country' => (!empty($userInfo['country'])) ? $userInfo['country'] : '',
					'zipcode' => (!empty($userInfo['zipcode'])) ? $userInfo['zipcode'] : '',
					'category' => (!empty($userInfo['categoryname'])) ? $userInfo['categoryname'] : '',
					'package_name' => $roleName,
					'payment_type' => $paymode
					);
					
			service_finder_sendProviderUpgradeEmail($args);
			service_finder_sendUpgradeMailToUser($username,$useremail,$args);
			
			}
			$wooextradata['processed'] = true;	
			wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			
			if($order_status == 'completed'){
			$wooextradata['completed'] = true;	
			wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			}
		}else{
		
			$row = $wpdb->get_row($wpdb->prepare('SELECT `user_id` FROM '.$wpdb->prefix.'usermeta WHERE `meta_value` = %d AND `meta_key` = "order_id"',$order_id));
			if(!empty($row)){
			$requeststatus = get_user_meta($row->user_id,'upgrade_request_status',true);
			
			if($requeststatus == 'pending' && ($order_status == 'completed' || $order_status == 'processing') && $wooextradata && ! isset ( $wooextradata['completed'] ) && ! isset ( $wooextradata['cancelled'] )){
			
			$userId = $row->user_id;
			$requestdata = get_user_meta($userId,'upgrade_request',true);
			
			update_user_meta( $userId, 'payment_type', 'woocommerce' );
			update_user_meta( $userId, 'payment_mode', $requestdata['payment_mode'] );
			update_user_meta( $userId, 'wired_invoiceid', $requestdata['wired_invoiceid'] );
			update_user_meta( $userId, 'recurring_profile_type', $requestdata['recurring_profile_type'] );
			update_user_meta( $userId, 'provider_role', $requestdata['provider_role'] );
			
			if($requestdata['expire_limit'] > 0){
				update_user_meta($userId, 'expire_limit', $requestdata['expire_limit']);
			}else{
				delete_user_meta($userId, 'expire_limit');
			}
			
			if($requestdata['trial_package'] == 'yes'){
				update_user_meta($userId, 'trial_package', 'yes');
			}
			update_user_meta( $userId, 'provider_activation_time', array( 'role' => $requestdata['provider_role'], 'time' => time()) );
			update_user_meta($userId, 'upgrade_request_status','approve');
			
			$email = service_finder_getProviderEmail($userId);
		
			$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');	
			
			if(!empty($service_finder_options['send-to-provider-upgrade-request-approval'])){
				$message = $service_finder_options['send-to-provider-upgrade-request-approval'];
			}else{
				$message = 'Dear '.esc_html($providerreplacestring).',
				Congratulations! Your account upgraded Successfully';
			}
			
			$msg_body = $message;
			if(!empty($service_finder_options['send-to-provider-upgrade-request-approval-subject'])){
				$msg_subject = $service_finder_options['send-to-provider-upgrade-request-approval-subject'];
			}else{
				$msg_subject = 'Account Upgrade Notification';
			}
			
			service_finder_wpmailer($email,$msg_subject,$msg_body);

			$wooextradata['completed'] = true;	
		    wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			}
			
			}
		
		}

	}
	
	public function update_after_featured_payment( $wooextradata,$order_id,$item_id ){
		global $service_finder_options, $wpdb, $service_finder_Tables; 
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		
		if ( $wooextradata && ! isset ( $wooextradata['processed'] ) ) {
			if($order_status == 'on-hold'){
			$date = date('Y-m-d H:i:s');
			$data = array(
					'payment_mode' => 'woocommerce',
					'paymenttype' => $payment_method,
					'txnid' => $order_id,
					'status' => 'on-hold',
					'date' => $date,
					);
			$where = array(
					'id' => esc_attr($wooextradata['feature_id'])
			);
			$res = $wpdb->update($service_finder_Tables->feature,wp_unslash($data),$where);
		}elseif($order_status == 'completed' || $order_status == 'processing'){
			$date = date('Y-m-d H:i:s');
			$data = array(
					'payment_mode' => 'woocommerce',
					'paymenttype' => $payment_method,
					'txnid' => $order_id,
					'status' => 'Paid',
					'feature_status' => 'active',
					'date' => $date,
					);
			$where = array(
					'id' => esc_attr($wooextradata['feature_id'])
			);
			$res = $wpdb->update($service_finder_Tables->feature,wp_unslash($data),$where);
			
			$getfeature = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$wooextradata['feature_id']));
	
			$data = array(
					'featured' => 1,
					);
			
			$where = array(
					'wp_user_id' => $getfeature->provider_id,
					);
			$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
			
			service_finder_featured_payment_mail($getfeature->provider_id);
		}
		 	$wooextradata['processed'] = true;	
			wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			
			if($order_status == 'completed'){
			$wooextradata['completed'] = true;	
			wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			}
		}else{
			$existingrequest = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `payment_mode` = "woocommerce" AND `status` = "on-hold" AND `txnid` = %d',$order_id));
			if(!empty($existingrequest) && ($order_status == 'completed' || $order_status == 'processing') && $wooextradata && ! isset ( $wooextradata['completed'] ) && ! isset ( $wooextradata['cancelled'] )){
				$date = date('Y-m-d H:i:s');
				$data = array(
						'status' => 'Paid',
						'feature_status' => 'active',
						'date' => $date,
						);
				$where = array(
						'id' => esc_attr($existingrequest->id)
				);
				$res = $wpdb->update($service_finder_Tables->feature,wp_unslash($data),$where);
				
				$getfeature = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$existingrequest->id));
	
				$data = array(
						'featured' => 1,
						);
				
				$where = array(
						'wp_user_id' => $getfeature->provider_id,
						);
				$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
				
				service_finder_featured_payment_mail($getfeature->provider_id);

				$wooextradata['completed'] = true;	
			    wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			}
		}
		
	}
	
	public function update_after_wallet_payment( $wooextradata,$order_id,$item_id ){
		global $service_finder_options, $wpdb, $service_finder_Tables; 
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		
		if ( $wooextradata && ! isset ( $wooextradata['processed'] ) ) {
			if($order_status == 'on-hold'){
			
			$args = array(
				'user_id' => base64_decode($wooextradata['user_id']),
				'amount' => $wooextradata['amount'],
				'txn_id' => $order_id,
				'action' => 'credit',
				'payment_mode' => 'woocommerce',
				'payment_method' => $payment_method,
				'payment_status' => 'on-hold'
				);
				
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->wallet_transaction.' WHERE `txn_id` = %d',$order_id));	
			
			if(!empty($row)){
				service_finder_update_wallet_history($args,$order_id);
			}else{
				service_finder_add_wallet_history($args);
			}
			
			
		}elseif($order_status == 'completed' || $order_status == 'processing'){
			service_finder_add_wallet_amount(base64_decode($wooextradata['user_id']),$wooextradata['amount']);
			
			$args = array(
				'user_id' => base64_decode($wooextradata['user_id']),
				'amount' => $wooextradata['amount'],
				'txn_id' => $order_id,
				'action' => 'credit',
				'payment_mode' => 'woocommerce',
				'payment_method' => $payment_method,
				'payment_status' => 'completed'
				);
				
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->wallet_transaction.' WHERE `txn_id` = %d',$order_id));	
			
			if(!empty($row)){
				service_finder_update_wallet_history($args,$order_id);
			}else{
				service_finder_add_wallet_history($args);
			}
		}
		 	$wooextradata['processed'] = true;	
			wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			
			if($order_status == 'completed'){
			$wooextradata['completed'] = true;	
			wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			}
		}else{
			
			if(($order_status == 'completed' || $order_status == 'processing') && $wooextradata && ! isset ( $wooextradata['completed'] ) && ! isset ( $wooextradata['cancelled'] )){
				service_finder_add_wallet_amount(base64_decode($wooextradata['user_id']),$wooextradata['amount']);
				
				$args = array(
					'user_id' => base64_decode($wooextradata['user_id']),
					'amount' => $wooextradata['amount'],
					'txn_id' => $order_id,
					'action' => 'credit',
					'payment_mode' => 'woocommerce',
					'payment_method' => $payment_method,
					'payment_status' => 'completed'
					);
					
				$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->wallet_transaction.' WHERE `txn_id` = %d',$order_id));	
				
				if(!empty($row)){
					service_finder_update_wallet_history($args,$order_id);
				}else{
					service_finder_add_wallet_history($args);
				}

				$wooextradata['completed'] = true;	
			    wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			}
		}
		
	}
	
	public function update_after_joblimit_payment( $wooextradata,$order_id,$item_id ){
		global $service_finder_options, $wpdb, $service_finder_Tables; 
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		
		if ( $wooextradata && ! isset ( $wooextradata['processed'] ) ) {
		
			if($order_status == 'on-hold'){
			$wired = array();
			
			$paydate = date('Y-m-d h:i:s');
			
			$wired['date'] = $paydate;
			$wired['paid_limits'] = $wooextradata['paidlimit'];
			$wired['available_limits'] = $wooextradata['available_limits'];
			$wired['current_plan'] = $wooextradata['plan'];
			$wired['planprice'] = $wooextradata['planprice'];
			$wired['limit'] = $wooextradata['planlimit'];
			
			delete_user_meta($wooextradata['provider_id'], 'job_connect_request');
			delete_user_meta($wooextradata['provider_id'], 'job_connect_request_status');
			
			update_user_meta($wooextradata['provider_id'], 'job_connect_request',$wired);
			update_user_meta($wooextradata['provider_id'], 'job_connect_request_status','pending');
			
			$data = array(
					'txn_id' => $order_id,
					'payment_type' => 'woocommerce',
					'payment_method' => $payment_method,
					'payment_status' => $order_status,
					);
			$where = array(
					'provider_id' => $wooextradata['provider_id']
			);
			$res = $wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);
			
			$paydate = date('Y-m-d h:i:s');
			$txndata = array(
					'provider_id' => $wooextradata['provider_id'],
					'payment_date' => $paydate,
					'plan' => $wooextradata['plan'],
					'amount' => $wooextradata['planprice'],
					'limit' => $wooextradata['planlimit'],
					'txn_id' => $order_id,
					'payment_type' => 'woocommerce',
					'payment_method' => $payment_method,
					'payment_status' => $order_status,
					);
			$wpdb->insert($service_finder_Tables->transaction,wp_unslash($txndata));
			
		}elseif($order_status == 'completed' || $order_status == 'processing'){
			$data = array(
					'paid_limits' => $wooextradata['paidlimit'],
					'available_limits' => $wooextradata['available_limits'],
					'txn_id' => $order_id,
					'payment_type' => 'woocommerce',
					'payment_method' => $payment_method,
					'payment_status' => $order_status,
					'current_plan' => $wooextradata['plan'],
					);
			$where = array(
					'provider_id' => $wooextradata['provider_id']
			);
			$res = $wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);
			
			$paydate = date('Y-m-d h:i:s');
			$txndata = array(
					'provider_id' => $wooextradata['provider_id'],
					'payment_date' => $paydate,
					'txn_id' => $order_id,
					'plan' => $wooextradata['plan'],
					'amount' => $wooextradata['planprice'],
					'limit' => $wooextradata['planlimit'],
					'payment_type' => 'woocommerce',
					'payment_method' => $payment_method,
					'payment_status' => $order_status,
					);
			$wpdb->insert($service_finder_Tables->transaction,wp_unslash($txndata));
			
			$this->send_mail_after_joblimit_connect_purchase( $wooextradata['provider_id'] );
		}
		
			 $wooextradata['processed'] = true;	
			 wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
		
		}else{
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `txn_id` = %d',$order_id));
			if(!empty($row)){
			$requeststatus = get_user_meta($row->provider_id,'job_connect_request_status',true);
			
			if($requeststatus == 'pending' && ($order_status == 'completed' || $order_status == 'processing')){
			$userId = $row->provider_id;
			$requestdata = get_user_meta($userId,'job_connect_request',true);
			
			$data = array(
					'paid_limits' => $requestdata['paid_limits'],
					'available_limits' => $requestdata['available_limits'],
					'payment_type' => 'woocommerce',
					'payment_method' => $payment_method,
					'payment_status' => $order_status,
					'current_plan' => $requestdata['current_plan'],
					);
			$where = array(
					'provider_id' => $userId
			);
			$res = $wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);
			
			$paydate = date('Y-m-d h:i:s');
			$txndata = array(
					'provider_id' => $userId,
					'payment_date' => $paydate,
					'txn_id' => $order_id,
					'plan' => $requestdata['current_plan'],
					'amount' => $requestdata['planprice'],
					'limit' => $requestdata['limit'],
					'payment_type' => 'woocommerce',
					'payment_method' => $payment_method,
					'payment_status' => $order_status,
					);
			$where = array(
					'txn_id' => $order_id
			);
			$res = $wpdb->update($service_finder_Tables->transaction,wp_unslash($txndata),$where);
			
			update_user_meta($userId, 'job_connect_request_status','approve');
			//delete_user_meta($userId, 'job_connect_request');
			
			$this->send_mail_after_joblimit_connect_purchase( $userId );
			
			return;
			}
			}
		}
		
	}
	
	public function update_after_jobpostlimit_payment( $wooextradata,$order_id,$item_id ){
		global $service_finder_options, $wpdb, $service_finder_Tables; 
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		if ( $wooextradata && ! isset ( $wooextradata['processed'] ) ) {
			if($order_status == 'on-hold'){
			$wired = array();
			
			$paydate = date('Y-m-d h:i:s');
			
			$wired['date'] = $paydate;
			$wired['paid_limits'] = $wooextradata['paidlimit'];
			$wired['available_limits'] = $wooextradata['available_limits'];
			$wired['current_plan'] = $wooextradata['plan'];
			$wired['planprice'] = $wooextradata['planprice'];
			$wired['limit'] = $wooextradata['planlimit'];
			
			delete_user_meta($wooextradata['customer_id'], 'job_connect_request');
			delete_user_meta($wooextradata['customer_id'], 'job_connect_request_status');
			
			update_user_meta($wooextradata['customer_id'], 'job_connect_request',$wired);
			update_user_meta($wooextradata['customer_id'], 'job_connect_request_status','pending');
			
			$data = array(
					'txn_id' => $order_id,
					'payment_type' => 'woocommerce',
					'payment_method' => $payment_method,
					'payment_status' => $order_status,
					);
			$where = array(
					'provider_id' => $wooextradata['customer_id']
			);
			$res = $wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);
			
			$paydate = date('Y-m-d h:i:s');
			$txndata = array(
					'provider_id' => $wooextradata['customer_id'],
					'payment_date' => $paydate,
					'plan' => $wooextradata['plan'],
					'amount' => $wooextradata['planprice'],
					'limit' => $wooextradata['planlimit'],
					'txn_id' => $order_id,
					'payment_type' => 'woocommerce',
					'payment_method' => $payment_method,
					'payment_status' => $order_status,
					);
			$wpdb->insert($service_finder_Tables->transaction,wp_unslash($txndata));
			
		}elseif($order_status == 'completed' || $order_status == 'processing'){
			$data = array(
					'paid_limits' => $wooextradata['paidlimit'],
					'available_limits' => $wooextradata['available_limits'],
					'txn_id' => $order_id,
					'payment_type' => 'woocommerce',
					'payment_method' => $payment_method,
					'payment_status' => $order_status,
					'current_plan' => $wooextradata['plan'],
					);
			$where = array(
					'provider_id' => $wooextradata['customer_id']
			);
			$res = $wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);
			
			$paydate = date('Y-m-d h:i:s');
			$txndata = array(
					'provider_id' => $wooextradata['customer_id'],
					'payment_date' => $paydate,
					'txn_id' => $order_id,
					'plan' => $wooextradata['plan'],
					'amount' => $wooextradata['planprice'],
					'limit' => $wooextradata['planlimit'],
					'payment_type' => 'woocommerce',
					'payment_method' => $payment_method,
					'payment_status' => $order_status,
					);
			$wpdb->insert($service_finder_Tables->transaction,wp_unslash($txndata));
			
			$this->send_mail_after_jobpost_limit_connect_purchase( $wooextradata['customer_id'] );
		}
			 $wooextradata['processed'] = true;	
			 wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
		}else{
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `txn_id` = %d',$order_id));
			if(!empty($row)){
			$requeststatus = get_user_meta($row->provider_id,'job_connect_request_status',true);
			
			if($requeststatus == 'pending' && ($order_status == 'completed' || $order_status == 'processing')){
			$userId = $row->provider_id;
			$requestdata = get_user_meta($userId,'job_connect_request',true);
			
			$data = array(
					'paid_limits' => $requestdata['paid_limits'],
					'available_limits' => $requestdata['available_limits'],
					'payment_type' => 'woocommerce',
					'payment_method' => $payment_method,
					'payment_status' => $order_status,
					'current_plan' => $requestdata['current_plan'],
					);
			$where = array(
					'provider_id' => $userId
			);
			$res = $wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);
			
			$paydate = date('Y-m-d h:i:s');
			$txndata = array(
					'provider_id' => $userId,
					'payment_date' => $paydate,
					'plan' => $requestdata['current_plan'],
					'amount' => $requestdata['planprice'],
					'limit' => $requestdata['limit'],
					'payment_method' => 'woocommerce',
					'payment_status' => $order_status,
					);
			$where = array(
					'txn_id' => $order_id
			);
			$res = $wpdb->update($service_finder_Tables->transaction,wp_unslash($txndata),$where);
			
			update_user_meta($userId, 'job_connect_request_status','approve');
			//delete_user_meta($userId, 'job_connect_request');
			
			$this->send_mail_after_jobpost_limit_connect_purchase( $userId );
			
			return;
			}
			}
		}
		
	}
	
	public function send_mail_after_joblimit_connect_purchase( $userid ){
		global $wpdb, $service_finder_options, $service_finder_Tables;
		
		$email = service_finder_getProviderEmail($userid);
		
		$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');	
		
		$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$userid.'"');
		$payment_method = '';
		$current_plan = '';
		if(!empty($row)){
		$payment_method = $row->payment_method;
		$current_plan = $row->current_plan;
		}
		
		$upgrade_plan = (!empty($service_finder_options['plan'.$current_plan.'-name'])) ? $service_finder_options['plan'.$current_plan.'-name'] : '';
		
		if(!empty($service_finder_options['joblimit-connect-purchase-message'])){
			$message = $service_finder_options['joblimit-connect-purchase-message'];
		}else{
			$message = esc_html__('Dear ', 'service-finder').esc_html($providerreplacestring).',';
			$message .= esc_html__('Congratulations! Your Job Coonect Upgraded Successfully', 'service-finder');
			$message .= esc_html__('Name: ', 'service-finder').'%USERNAME%';
			$message .= esc_html__('Plan Name: ', 'service-finder').'%PLANNAME%';
			$message .= esc_html__('Payment Method: ', 'service-finder').'%PAYMENTMETHOD%';
		}
		
		if($service_finder_options['joblimit-connect-purchase-subject'] != ""){
			$msg_subject = $service_finder_options['joblimit-connect-purchase-subject'];
		}else{
			$msg_subject = esc_html__('Job Connect Approval Notification', 'service-finder');
		}
		
		$tokens = array('%USERNAME%','%PLANNAME%','%PAYMENTMETHOD%');
		$replacements = array(service_finder_getProviderName($userid),$upgrade_plan,service_finder_translate_static_status_string($payment_method));
		$msg_body = str_replace($tokens,$replacements,$message);
		
		if(function_exists('service_finder_add_notices')) {
	
			$noticedata = array(
					'provider_id' => $userid,
					'target_id' => $row->id, 
					'topic' => 'Job Post Connect',
					'title' => esc_html__('Job Post Connect', 'service-finder'),
					'notice' => esc_html__('Your job post connect plan upgraded.', 'service-finder')
					);
			service_finder_add_notices($noticedata);
		
		}
		
		service_finder_wpmailer($email,$msg_subject,$msg_body);
	}
	
	public function send_mail_after_jobpost_limit_connect_purchase( $userid ){
		global $wpdb, $service_finder_options, $service_finder_Tables;
		
		$email = service_finder_getCustomerEmail($userid);
			
		$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customer', 'service-finder');	
		
		$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$userid.'"');
		$payment_method = '';
		$current_plan = '';
		if(!empty($row)){
		$payment_method = $row->payment_method;
		$current_plan = $row->current_plan;
		}
		
		$upgrade_plan = (!empty($service_finder_options['job-post-plan'.$current_plan.'-name'])) ? $service_finder_options['job-post-plan'.$current_plan.'-name'] : '';
		
		if(!empty($service_finder_options['jobpost-connect-purchase-message'])){
			$message = $service_finder_options['jobpost-connect-purchase-message'];
		}else{
			$message = esc_html__('Dear ', 'service-finder').esc_html($customerreplacestring).',';
			$message .= esc_html__('Congratulations! Your Job Post Connect Upgraded Successfully', 'service-finder');
			$message .= esc_html__('Name: ', 'service-finder').'%USERNAME%';
			$message .= esc_html__('Plan Name: ', 'service-finder').'%PLANNAME%';
			$message .= esc_html__('Payment Method: ', 'service-finder').'%PAYMENTMETHOD%';
		}
		
		if($service_finder_options['jobpost-connect-purchase-subject'] != ""){
			$msg_subject = $service_finder_options['jobpost-connect-purchase-subject'];
		}else{
			$msg_subject = esc_html__('Job Post Connect Approval Notification', 'service-finder');
		}
		
		$tokens = array('%USERNAME%','%PLANNAME%','%PAYMENTMETHOD%');
		$replacements = array(service_finder_getCustomerName($userid),$upgrade_plan,service_finder_translate_static_status_string($payment_method));
		$msg_body = str_replace($tokens,$replacements,$message);
		
		if(function_exists('service_finder_add_notices')) {
	
			$noticedata = array(
					'customer_id' => $userid,
					'target_id' => $row->id, 
					'topic' => 'Job Post Connect',
					'title' => esc_html__('Job Post Connect', 'service-finder'),
					'notice' => esc_html__('Your job post connect plan upgraded.', 'service-finder')
					);
			service_finder_add_notices($noticedata);
		
		}
		
		service_finder_wpmailer($email,$msg_subject,$msg_body);
	
	}
	
	public function update_after_claimed_payment( $userId,$wooextradata,$order_id ){
		global $service_finder_options, $wpdb, $service_finder_Tables; 
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		
		if ( $wooextradata && ! isset ( $wooextradata['processed'] ) ) {
			if($order_status == 'on-hold'){
		
			$wiredclaimed = array();
			
			$roleNum = $wooextradata['roleNum'];
			$expire_limit = $wooextradata['expire_limit'];
			
			$wiredclaimed['payment_type'] = 'woocommerce';
			$wiredclaimed['payment_mode'] = $payment_method;
			
			$wiredclaimed['rolePrice'] = $wooextradata['rolePrice'];
			$wiredclaimed['pay_type'] = 'single';
			$wiredclaimed['roleNum'] = $wooextradata['roleNum'];
			$wiredclaimed['expire_limit'] = $wooextradata['expire_limit'];
			$wiredclaimed['role'] = $wooextradata['role'];
			$wiredclaimed['price'] = $wooextradata['price'];
			
			$wiredclaimed['claimedbusinessid'] = $wooextradata['claimedbusinessid'];
			
			$wiredclaimed['time'] = time();
			
			$invoiceid = $order_id;
						
			$wiredclaimed['wired_invoiceid'] = $invoiceid;
			
			$wiredclaimed['recurring_profile_type'] = '';
			
			if($expire_limit > 0){
			$wiredclaimed['expire_limit'] = $expire_limit;
			}
			
			$data = array(
			'payment_type' => 'woocommerce',
			'payment_method' => $payment_method,
			'payment_status' => 'on-hold',
			'txn_id' => $order_id,
			);
			
			$where = array(
			'id' => $wooextradata['claimedbusinessid']
			);
			
			$wpdb->update($service_finder_Tables->claim_business,wp_unslash($data),$where);
			
			update_user_meta($userId, 'claimed_request',$wiredclaimed);
			update_user_meta($userId, 'claimed_request_status','pending');
			update_user_meta($userId, 'claimed_order_id', $order_id);
		
		}elseif($order_status == 'completed' || $order_status == 'processing'){
		
		// set role
		$user = new WP_User( $userId );
		$user->set_role('Provider');
		
		update_user_meta( $userId, 'provider_activation_time', array( 'role' => $wooextradata['role'], 'time' => time()) );
		
		update_user_meta($userId, 'order_id', $order_id);
		if($wooextradata['expire_limit'] > 0){
			update_user_meta($userId, 'expire_limit', $wooextradata['expire_limit']);
		}else{
			delete_user_meta($userId, 'expire_limit');
		}
		update_user_meta( $userId, 'provider_role', $wooextradata['role'] );
		update_user_meta($userId, 'profile_amt',$wooextradata['rolePrice']);
		update_user_meta( $userId, 'pay_type', 'single' );
		$roleNum = $wooextradata['roleNum'];
		$roleName = $service_finder_options['package'.$roleNum.'-name'];
		update_user_meta( $userId, 'payment_mode', $payment_method );
		update_user_meta( $userId, 'payment_type', 'woocommerce' );
		
		$paymode = 'woocommerce';
		$userInfo = service_finder_getUserInfo($userId);
		
		$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$userId));
		
		$data = array(
		'payment_type' => 'woocommerce',
		'payment_method' => $payment_method,
		'payment_status' => 'paid',
		'txn_id' => $order_id,
		);
		
		$where = array(
		'id' => $wooextradata['claimedbusinessid']
		);
		
		$wpdb->update($service_finder_Tables->claim_business,wp_unslash($data),$where);
		
		$username = $userdata->user_login;
		$useremail = $userdata->user_email;
		
		$args = array(
				'username' => (!empty($userdata->user_login)) ? $userdata->user_login : '',
				'email' => (!empty($userdata->user_email)) ? $userdata->user_email : '',
				'package_name' => $roleName,
				'payment_type' => $paymode,
				'payment_mode' => $payment_method
				);
		service_finder_update_job_limit($userId);
		
		service_finder_after_claimedpayment_user($userId,$wooextradata['claimedbusinessid']);
		service_finder_after_claimedpayment_admin($args,$wooextradata['claimedbusinessid']);
		
		}
			
			$wooextradata['processed'] = true;	
			wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			
			if($order_status == 'completed'){
			$wooextradata['completed'] = true;	
			wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			}
		}else{
			$row = $wpdb->get_row($wpdb->prepare('SELECT `user_id` FROM '.$wpdb->prefix.'usermeta WHERE `meta_value` = %d AND `meta_key` = "claimed_order_id"',$order_id));
			if(!empty($row)){
			$requeststatus = get_user_meta($row->user_id,'claimed_request_status',true);
			
			if($requeststatus == 'pending' && ($order_status == 'completed' || $order_status == 'processing') && $wooextradata && ! isset ( $wooextradata['completed'] ) && ! isset ( $wooextradata['cancelled'] )){
			$userId = $row->user_id;
			$requestdata = get_user_meta($userId,'claimed_request',true);
			
			$user = new WP_User( $userId );
			$user->set_role('Provider');
			
			update_user_meta( $userId, 'provider_activation_time', array( 'role' => $requestdata['role'], 'time' => time()) );
			
			if($requestdata['expire_limit'] > 0){
				update_user_meta($userId, 'expire_limit', $requestdata['expire_limit']);
			}else{
				delete_user_meta($userId, 'expire_limit');
			}
			update_user_meta( $userId, 'provider_role', $requestdata['role'] );
			update_user_meta($userId, 'profile_amt',$requestdata['rolePrice']);
			update_user_meta( $userId, 'pay_type', 'single' );
			$roleNum = $requestdata['roleNum'];
			$roleName = $service_finder_options['package'.$roleNum.'-name'];
			update_user_meta( $userId, 'payment_mode', $payment_method );
			update_user_meta( $userId, 'payment_type', 'woocommerce' );
			
			$paymode = 'woocommerce';
			$userInfo = service_finder_getUserInfo($userId);
			
			$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$userId));
			
			$username = $userdata->user_login;
			$useremail = $userdata->user_email;
			
			$data = array(
			'payment_type' => 'woocommerce',
			'payment_method' => $payment_method,
			'payment_status' => 'paid',
			'txn_id' => $order_id,
			);
			
			$where = array(
			'id' => $requestdata['claimedbusinessid']
			);
			
			$wpdb->update($service_finder_Tables->claim_business,wp_unslash($data),$where);
			
			$args = array(
					'username' => (!empty($userdata->user_login)) ? $userdata->user_login : '',
					'email' => (!empty($userdata->user_email)) ? $userdata->user_email : '',
					'package_name' => $roleName,
					'payment_type' => $paymode,
					'payment_mode' => $payment_method
					);
			service_finder_update_job_limit($userId);
			
			update_user_meta($userId, 'claimed_request_status','completed');
			
			service_finder_after_claimedpayment_user($userId,$requestdata['claimedbusinessid']);
			service_finder_after_claimedpayment_admin($args,$requestdata['claimedbusinessid']);
			
			$wooextradata['cancelled'] = true;	
			wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
			}
			}
		}
	}
	
	public function update_after_invoice_payment( $wooextradata,$order_id ){
		global $service_finder_options, $wpdb, $service_finder_Tables; 
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		
		if ( $wooextradata && ! isset ( $wooextradata['processed'] ) ) {
		if($order_status == 'on-hold'){
			$data = array(
			'payment_mode' => 'woocommerce',
			'payment_type' => $payment_method,
			'status' => 'on-hold',
			'txnid' => $order_id,
			);
			
			$where = array(
			'id' => $wooextradata['invoiceid']
			);
			
			$wpdb->update($service_finder_Tables->invoice,wp_unslash($data),$where);
			
		}elseif($order_status == 'completed' || $order_status == 'processing'){
			$data = array(
			'payment_mode' => 'woocommerce',
			'payment_type' => $payment_method,
			'status' => 'paid',
			'txnid' => $order_id,
			);
			
			$where = array(
			'id' => $wooextradata['invoiceid']
			);
			
			$wpdb->update($service_finder_Tables->invoice,wp_unslash($data),$where);
			
			if(function_exists('service_finder_add_notices')) {
	
				$noticedata = array(
						'provider_id' => $wooextradata['provider'],
						'target_id' => $wooextradata['invoiceid'], 
						'topic' => 'Invoice Paid',
						'title' => esc_html__('Invoice Paid', 'service-finder'),
						'notice' => sprintf( esc_html__('Invoice paid by %s', 'service-finder'), $wooextradata['email'] ),
						);
				service_finder_add_notices($noticedata);
			
			}
			
			service_finder_SendInvoicePaidMailToProvider($wooextradata['invoiceid']);
			service_finder_SendInvoicePaidMailToCustomer($wooextradata['invoiceid']);
		}
		
		$wooextradata['processed'] = true;	
		wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
		
		if($order_status == 'completed'){
		$wooextradata['completed'] = true;	
		wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
		}
		}else{
		
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->invoice.' WHERE `status` = "on-hold" AND `txnid` = %d',$order_id));
		if(!empty($row)){
		if(($order_status == 'completed' || $order_status == 'processing') && $wooextradata && ! isset ( $wooextradata['completed'] ) && ! isset ( $wooextradata['cancelled'] )){
			$invoiceid = $row->id;
			$provider_id = $row->provider_id;
			$customer_email = $row->customer_email;
			
			$data = array(
			'payment_mode' => 'woocommerce',
			'payment_type' => $payment_method,
			'status' => 'paid',
			'txnid' => $order_id,
			);
			
			$where = array(
			'id' => $invoiceid
			);
			
			$wpdb->update($service_finder_Tables->invoice,wp_unslash($data),$where);
			
			if(function_exists('service_finder_add_notices')) {
	
				$noticedata = array(
						'provider_id' => $provider_id,
						'target_id' => $invoiceid, 
						'topic' => 'Invoice Paid',
						'title' => esc_html__('Invoice Paid', 'service-finder'),
						'notice' => sprintf( esc_html__('Invoice paid by %s', 'service-finder'), $customer_email ),
						);
				service_finder_add_notices($noticedata);
			
			}
			
			service_finder_SendInvoicePaidMailToProvider($invoiceid);
			service_finder_SendInvoicePaidMailToCustomer($invoiceid);
			
			$wooextradata['completed'] = true;	
		    wc_update_order_item_meta( $item_id, 'wooextradata', $wooextradata );
		
		}
		}
		
		}

	}
	
	public function remove_quantity_field( $args, $product ){
        
		if ( $product->get_id() == $this->product_id ) {
            $args['max_value'] = $args['input_value'];
            $args['min_value'] = $args['input_value'];
        }

        return $args;
    }
	
	public function update_cart_meta_item( $other_data, $wc_item ){
	global $service_finder_options;
		if ( isset( $wc_item['wooextradata'] )) {
			
			$wootype = $wc_item['wooextradata']['wootype'];
			$this->wooaction = $wootype;
			switch($wootype){
			case 'booking':
					$temp = explode('-',$wc_item['wooextradata']['boking-slot']);
					if(!empty($temp)){
					$starttime = (!empty($temp[0])) ? date('h:i a',strtotime($temp[0])) : '';
					$endtime = (!empty($temp[1])) ? date('h:i a',strtotime($temp[1])) : '';
					if($starttime != "" && $endtime != ""){
					$timeperiod = $starttime.' - '.$endtime;
					}else{
					$timeperiod = $starttime;
					}
					
					$multidate = (service_finder_booking_date_method($wc_item['wooextradata']['provider']) == 'multidate') ? 'yes' : 'no';
					if($multidate == 'yes'){
					$servicearr = (!empty($wc_item['wooextradata']['servicearr'])) ? $wc_item['wooextradata']['servicearr'] : '';
					$servicearr = trim($servicearr,'%%');
					$serviceitems = explode('%%',$servicearr);
					
					$summary = service_finder_booking_services_woosummary($serviceitems,$wc_item['wooextradata']['provider']);
					
					$other_data = array();
					
					$other_data[] = array('name' => esc_html__('Provider Name','service-finder'), 'value' => service_finder_getProviderName($wc_item['wooextradata']['provider']));
					$other_data[] = array('name' => esc_html__('Booked Services','service-finder'), 'value' => $summary);
					
					
					
					}else{	
					$servicearr = (!empty($wc_item['wooextradata']['servicearr'])) ? $wc_item['wooextradata']['servicearr'] : '';
					$servicearr = trim($servicearr,'%%');
					$serviceitems = explode('%%',$servicearr);
					$servicenames = array();
					if(!empty($serviceitems))
					{
						foreach($serviceitems as $serviceitem){
						
							$singleserviceitems = explode('-',$serviceitem);
							if(!empty($singleserviceitems[0])){
							$serviceid = $singleserviceitems[0];
							$servicenames[] = service_finder_getServiceName($serviceid);
							}
						}
					}
					
					if($wc_item['wooextradata']['jobid'] > 0)
					{
					$other_data[] = array('name' => esc_html__('Booking Type','service-finder'), 'value' => esc_html__('Job','service-finder'));
					$other_data[] = array('name' => esc_html__('Job Title','service-finder'), 'value' => get_the_title($wc_item['wooextradata']['jobid']));
					}elseif($wc_item['wooextradata']['quoteid'] > 0)
					{
					$other_data[] = array('name' => esc_html__('Booking Type','service-finder'), 'value' => esc_html__('Quotation','service-finder'));
					}
					
					$other_data[] = array('name' => service_finder_provider_replace_string().' '.esc_html__('Name','service-finder'), 'value' => service_finder_getProviderFullName($wc_item['wooextradata']['provider']));
					
					if(!empty($servicenames)){
					$services = implode(', ',$servicenames);
					$other_data[] = array('name' => esc_html__('Services','service-finder'), 'value' => $services);
					}
					$other_data[] = array('name' => esc_html__('Schedule Date','service-finder'), 'value' => date('Y-m-d',strtotime($wc_item['wooextradata']['bookingdate'])));
					$other_data[] = array('name' => esc_html__('Time','service-finder'), 'value' => $timeperiod);
					}
					
					$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
					$admin_fee_type = (!empty($service_finder_options['admin-fee-type'])) ? $service_finder_options['admin-fee-type'] : 0;
					$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;
					$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;
					
					$admin_fee_label = (!empty($service_finder_options['admin-fee-label'])) ? $service_finder_options['admin-fee-label'] : esc_html__('Admin Fee', 'service-finder');
					$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
					$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';

					if(!class_exists( 'WC_Vendors' ) && $charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'customer'){
					
					$other_data[] = array('name' => esc_html__('Booking Amount','service-finder'), 'value' => service_finder_currencysymbol().$wc_item['wooextradata']['totalcost']);
					$other_data[] = array('name' => sprintf( esc_html__('%s', 'service-finder'), $admin_fee_label ), 'value' => service_finder_currencysymbol().$wc_item['wooextradata']['adminfee']);
					$other_data[] = array('name' => esc_html__('Total Amount', 'service-finder'), 'value' => service_finder_currencysymbol().(floatval($wc_item['wooextradata']['totalcost']) + floatval($wc_item['wooextradata']['adminfee'])));
					
					}
					
					}
					break;
			case 'signup':
					$other_data[] = array('name' => esc_html__('Category','service-finder'), 'value' => service_finder_getCategoryNameviaSql($wc_item['wooextradata']['signup_category']));
					if(isset($wc_item['wooextradata'])){
					$other_data[] = array('name' => esc_html__('Package','service-finder'), 'value' => $wc_item['wooextradata']['packageName']);
					}
					break;
			case 'upgrade':
					if(isset($wc_item['wooextradata'])){
					$other_data[] = array('name' => esc_html__('Package','service-finder'), 'value' => $wc_item['wooextradata']['packageName']);
					}
					break;
			case 'featured':
					if(isset($wc_item['wooextradata'])){
					$other_data[] = array('name' => esc_html__('Request','service-finder'), 'value' => esc_html__('Featured Service','service-finder'));
					}
					break;
			case 'joblimit':
					if(isset($wc_item['wooextradata'])){
					$other_data[] = array('name' => esc_html__('Job Limit Plan','service-finder'), 'value' => $wc_item['wooextradata']['planname']);
					$other_data[] = array('name' => esc_html__('Number of Connects','service-finder'), 'value' => $wc_item['wooextradata']['planlimit']);
					}
					break;
			case 'jobpostlimit':
					if(isset($wc_item['wooextradata'])){
					$other_data[] = array('name' => esc_html__('Job Post Limit Plan','service-finder'), 'value' => $wc_item['wooextradata']['planname']);
					$other_data[] = array('name' => esc_html__('Number of Job Post Connects','service-finder'), 'value' => $wc_item['wooextradata']['planlimit']);
					}
					break;			
			case 'claimbusiness':
					if(isset($wc_item['wooextradata'])){
					$other_data[] = array('name' => esc_html__('Package','service-finder'), 'value' => $wc_item['wooextradata']['packageName']);
					}
					break;
			case 'invoice':
					if(isset($wc_item['wooextradata'])){
					$other_data[] = array('name' => esc_html__('Request','service-finder'), 'value' => esc_html__('Invoice Payment','service-finder'));
					}
					break;
			case 'wallet':
					if(isset($wc_item['wooextradata'])){
					$other_data[] = array('name' => esc_html__('Request','service-finder'), 'value' => esc_html__('Add amount to wallet','service-finder'));
					}
					break;														
			}
				
		 	}
		
        return $other_data;
    }
	
	public function update_billing_info( $null, $field_name )
    {
	
        if ( empty( $this->checkout_info ) ) {
            foreach ( WC()->cart->get_cart() as $wc_key => $wc_item ) {
                if ( array_key_exists( 'wooextradata', $wc_item ) ) {
				
                    $wootype = $wc_item['wooextradata']['wootype'];
					switch($wootype){
					case 'booking':
							$this->checkout_info = array(
								'billing_first_name' => $wc_item['wooextradata']['firstname'],
								'billing_last_name'  => $wc_item['wooextradata']['lastname'],
								'billing_email'      => $wc_item['wooextradata']['email'],
								'billing_phone'      => $wc_item['wooextradata']['phone'],
								'billing_address_1'      => $wc_item['wooextradata']['address'],
								'billing_address_2'      => $wc_item['wooextradata']['apt'],
								'billing_city'      => $wc_item['wooextradata']['city'],
								'billing_state'      => $wc_item['wooextradata']['state'],
								'billing_country'      => $wc_item['wooextradata']['country'],
								'billing_postcode'      => $wc_item['wooextradata']['zipcode'],
							);
							break;
					case 'signup':
							$this->checkout_info = array(
								'billing_first_name' => $wc_item['wooextradata']['signup_first_name'],
								'billing_last_name'  => $wc_item['wooextradata']['signup_last_name'],
								'billing_company'  => $wc_item['wooextradata']['signup_company_name'],
								'billing_email'      => $wc_item['wooextradata']['signup_user_email'],
								'billing_phone'      => $wc_item['wooextradata']['signup_phone'],
								'billing_address_1'      => (isset($wc_item['wooextradata']['signup_address'])) ? $wc_item['wooextradata']['signup_address'] : '',
								'billing_address_2'      => (isset($wc_item['wooextradata']['signup_apt'])) ? $wc_item['wooextradata']['signup_apt'] : '',
								'billing_city'      => (isset($wc_item['wooextradata']['signup_city'])) ? $wc_item['wooextradata']['signup_city'] : '',
								'billing_state'      => (isset($wc_item['wooextradata']['signup_state'])) ? $wc_item['wooextradata']['signup_state'] : '',
								'billing_country'      => (isset($wc_item['wooextradata']['signup_country'])) ? $wc_item['wooextradata']['signup_country'] : '',
								'billing_postcode'      => (isset($wc_item['wooextradata']['signup_zipcode'])) ? $wc_item['wooextradata']['signup_zipcode'] : '',
							);
							break;
					case 'upgrade':
							$userInfo = service_finder_getUserInfo($wc_item['wooextradata']['user_id']);
							$this->checkout_info = array(
								'billing_first_name' => $userInfo['fname'],
								'billing_last_name'  => $userInfo['lname'],
								'billing_email'      => $userInfo['email'],
								'billing_phone'      => $userInfo['phone'],
								'billing_address_1'      => $userInfo['address'],
								'billing_address_2'      => $userInfo['apt'],
								'billing_city'      => $userInfo['city'],
								'billing_state'      => $userInfo['state'],
								'billing_country'      => $userInfo['country'],
								'billing_postcode'      => $userInfo['zipcode'],
							);
							break;
					case 'featured':
							$userInfo = service_finder_getUserInfo($wc_item['wooextradata']['provider_id']);
							$this->checkout_info = array(
								'billing_first_name' => $userInfo['fname'],
								'billing_last_name'  => $userInfo['lname'],
								'billing_email'      => $userInfo['email'],
								'billing_phone'      => $userInfo['phone'],
								'billing_address_1'      => $userInfo['address'],
								'billing_address_2'      => $userInfo['apt'],
								'billing_city'      => $userInfo['city'],
								'billing_state'      => $userInfo['state'],
								'billing_country'      => $userInfo['country'],
								'billing_postcode'      => $userInfo['zipcode'],
							);
							break;
					case 'joblimit':
							$userInfo = service_finder_getUserInfo($wc_item['wooextradata']['provider_id']);
							$this->checkout_info = array(
								'billing_first_name' => $userInfo['fname'],
								'billing_last_name'  => $userInfo['lname'],
								'billing_email'      => $userInfo['email'],
								'billing_phone'      => $userInfo['phone'],
								'billing_address_1'      => $userInfo['address'],
								'billing_address_2'      => $userInfo['apt'],
								'billing_city'      => $userInfo['city'],
								'billing_state'      => $userInfo['state'],
								'billing_country'      => $userInfo['country'],
								'billing_postcode'      => $userInfo['zipcode'],
							);
							break;
					case 'jobpostlimit':
							$userInfo = service_finder_getUserInfo($wc_item['wooextradata']['customer_id']);
							$this->checkout_info = array(
								'billing_first_name' => $userInfo['fname'],
								'billing_last_name'  => $userInfo['lname'],
								'billing_email'      => $userInfo['email'],
								'billing_phone'      => $userInfo['phone'],
								'billing_address_1'      => $userInfo['address'],
								'billing_address_2'      => $userInfo['apt'],
								'billing_city'      => $userInfo['city'],
								'billing_state'      => $userInfo['state'],
								'billing_country'      => $userInfo['country'],
								'billing_postcode'      => $userInfo['zipcode'],
							);
							break;			
					case 'claimbusiness':
							$userInfo = service_finder_getUserInfo($wc_item['wooextradata']['profileid']);
							$this->checkout_info = array(
								'billing_first_name' => $userInfo['fname'],
								'billing_last_name'  => $userInfo['lname'],
								'billing_email'      => $userInfo['email'],
								'billing_phone'      => $userInfo['phone'],
								'billing_address_1'      => $userInfo['address'],
								'billing_address_2'      => $userInfo['apt'],
								'billing_city'      => $userInfo['city'],
								'billing_state'      => $userInfo['state'],
								'billing_country'      => $userInfo['country'],
								'billing_postcode'      => $userInfo['zipcode'],
							);
							break;
					case 'invoice':
							$userInfo = service_finder_getUserInfo($wc_item['wooextradata']['provider']);
							$this->checkout_info = array(
								'billing_first_name' => $userInfo['fname'],
								'billing_last_name'  => $userInfo['lname'],
								'billing_email'      => $userInfo['email'],
								'billing_phone'      => $userInfo['phone'],
								'billing_address_1'      => $userInfo['address'],
								'billing_address_2'      => $userInfo['apt'],
								'billing_city'      => $userInfo['city'],
								'billing_state'      => $userInfo['state'],
								'billing_country'      => $userInfo['country'],
								'billing_postcode'      => $userInfo['zipcode'],
							);
							break;	
					case 'wallet':
							$userInfo = service_finder_getUserInfo(base64_decode($wc_item['wooextradata']['user_id']));
							$this->checkout_info = array(
								'billing_first_name' => $userInfo['fname'],
								'billing_last_name'  => $userInfo['lname'],
								'billing_email'      => $userInfo['email'],
								'billing_phone'      => $userInfo['phone'],
								'billing_address_1'      => $userInfo['address'],
								'billing_address_2'      => $userInfo['apt'],
								'billing_city'      => $userInfo['city'],
								'billing_state'      => $userInfo['state'],
								'billing_country'      => $userInfo['country'],
								'billing_postcode'      => $userInfo['zipcode'],
							);
							break;													
					}
                }
            }
        }
        if ( array_key_exists( $field_name, $this->checkout_info ) ) {
            return $this->checkout_info[ $field_name ];
        }

        return null;
    }
	
	public function redirect_after_payment($order_id){
		global $wp, $service_finder_options, $wpdb, $current_user;
		
		if ( is_checkout() ) {
        $order = new \WC_Order( $order_id );
		if ( $order->get_status() != 'failed' ) {
        foreach ( $order->get_items() as $item_id => $order_item ) {
            $wooextradata = wc_get_order_item_meta( $item_id, 'wooextradata' );
			
			$wootype = $wooextradata['wootype'];
			switch($wootype){
			case 'booking':
					$pageid = (!empty($service_finder_options['woo-booking-redirect'])) ? $service_finder_options['woo-booking-redirect'] : '';
					break;
			case 'signup':
					$pageid = (!empty($service_finder_options['woo-signup-redirect'])) ? $service_finder_options['woo-signup-redirect'] : '';
					break;
			case 'upgrade':
					$pageid = (!empty($service_finder_options['woo-upgrade-redirect'])) ? $service_finder_options['woo-upgrade-redirect'] : '';
					break;
			case 'featured':
					$pageid = (!empty($service_finder_options['woo-featured-redirect'])) ? $service_finder_options['woo-featured-redirect'] : '';
					break;
			case 'joblimit':
					$pageid = (!empty($service_finder_options['woo-jobconnect-redirect'])) ? $service_finder_options['woo-jobconnect-redirect'] : '';
					break;
			case 'jobpostlimit':
					$pageid = (!empty($service_finder_options['woo-jobpostconnect-redirect'])) ? $service_finder_options['woo-jobpostconnect-redirect'] : '';
					break;		
			case 'claimbusiness':
					$pageid = (!empty($service_finder_options['woo-claimed-redirect'])) ? $service_finder_options['woo-claimed-redirect'] : '';
					break;
			case 'invoice':
					$pageid = (!empty($service_finder_options['woo-invoice-redirect'])) ? $service_finder_options['woo-invoice-redirect'] : '';
					break;
			case 'wallet':
					if(service_finder_getUserRole($current_user->ID) == 'Customer'){
					$pageid = (!empty($service_finder_options['woo-customer-wallet-redirect'])) ? $service_finder_options['woo-customer-wallet-redirect'] : '';
					
					if($pageid == '')
					{
					$accounturl = service_finder_get_url_by_shortcode('[service_finder_my_account]');
					
					$url = add_query_arg( array('action' => 'wallet','woocheckout' => 'success'), $accounturl );
					wp_redirect( esc_url($url) );
					exit;
					}
					
					}else{
					$pageid = (!empty($service_finder_options['woo-provider-wallet-redirect'])) ? $service_finder_options['woo-provider-wallet-redirect'] : '';
					
					/*if($pageid == '')
					{
					$accounturl = service_finder_get_url_by_shortcode('[service_finder_my_account]');
					
					$url = add_query_arg( array('woocheckout' => 'success'), $accounturl );
					wp_redirect( esc_url($url) );
					exit;
					}*/
					}
					break;		
			default:
					$pageid = 'no';
			}
			
			if($pageid != 'no'){
			$url = add_query_arg( array('woocheckout' => 'success'), get_permalink($pageid) );
			wp_redirect($url);
			exit;
			}
			
        }
		}
		}
    
    }
	
	public function disable_available_payment_gateways($gateways){
		
		switch($this->wooaction){
		case 'booking':
				break;
		case 'signup':
				unset($gateways['cod']);
				break;
		case 'upgrade':
				unset($gateways['cod']);
				break;
		case 'featured':
				unset($gateways['cod']);
				break;
		case 'joblimit':
				unset($gateways['cod']);
				break;
		case 'claimbusiness':
				unset($gateways['cod']);
				break;
		case 'invoice':
				unset($gateways['cod']);
				break;
		case 'wallet':
				unset($gateways['cod']);
				break;		
		}
		
		return $gateways;
    }
    
}
new SF_Woopayments();
}
