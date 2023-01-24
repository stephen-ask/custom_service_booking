<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SERVICE_FINDER_ProfileUpgrade{

	/*Request For Feature*/
	public function service_finder_FeatureRequest($arg = ''){
			global $wpdb, $service_finder_Tables;
			
			$chkreq = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `provider_id` = "'.$arg['user_id'].'"');
			
			if(!empty($chkreq)){
				$where = array(
					'provider_id' => esc_attr($arg['user_id']),
					);
			
				$wpdb->delete($service_finder_Tables->feature,$where);
			}

			$data = array(
					'provider_id' => esc_attr($arg['user_id']),
					'days' => esc_attr($arg['featuredays']),
					'status' => 'Waiting for Approval',
					);

			$wpdb->insert($service_finder_Tables->feature,wp_unslash($data));
			
			$feature_id = $wpdb->insert_id;
			
			if ( ! $feature_id ) {
				$adminemail = get_option( 'admin_email' );
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t make request for feature... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				echo json_encode($error);
			}else{
				$msg = (!empty($service_finder_options['feature-request'])) ? $service_finder_options['feature-request'] : esc_html__('Request made successfully', 'service-finder');
			
				$success = array(
						'status' => 'success',
						'suc_message' => $msg,
						'serviceid' => $feature_id,
						);
				echo json_encode($success);
			}
			
		}
		
	/*Make Stripe Payment for Feature*/
	public function service_finder_makePayment($arg = '',$customerID = '',$txnid = '',$payment_mode = ''){
			global $wpdb, $service_finder_Tables;
			$stripetoken = (!empty($arg['stripeToken'])) ? $arg['stripeToken'] : '';
			$date = date('Y-m-d H:i:s');
			$data = array(
					'paymenttype' => $payment_mode,
					'stripe_customer_id' => $customerID,
					'paypal_transaction_id' => $txnid,
					'stripe_token' => esc_attr($stripetoken),
					'status' => 'Paid',
					'feature_status' => 'active',
					'date' => $date,
					);
			$where = array(
					'id' => esc_attr($arg['feature_id'])
			);
			$res = $wpdb->update($service_finder_Tables->feature,wp_unslash($data),$where);
			
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$arg['feature_id']));
			
			$data = array(
					'featured' => 1,
					);
			$where = array(
					'wp_user_id' => $row->provider_id,
					);
			$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
			
			service_finder_featured_payment_mail($row->provider_id);
			
			if ( ! $res) {
				$adminemail = get_option( 'admin_email' );
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t make payment for feature... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				$service_finder_Errors = json_encode($error);
				return $service_finder_Errors;
			}else{
				$msg = (!empty($service_finder_options['feature-payment'])) ? $service_finder_options['feature-payment'] : esc_html__('Payment made successfully to be featured', 'service-finder');
				
				$success = array(
						'status' => 'success',
						'suc_message' => $msg,
						);
				$service_finder_Success = json_encode($success);
				return $service_finder_Success;
			}
			
		}	
		
	/*Make Paypal Payment for Feature*/
	public function service_finder_makePaypalPayment($arg = '',$token){
			global $wpdb, $service_finder_Tables;

			$date = date('Y-m-d H:i:s');
			$data = array(
					'paypal_token' => $token,
					);
			$where = array(
					'id' => esc_attr($arg['feature_id'])
			);
			$res = $wpdb->update($service_finder_Tables->feature,wp_unslash($data),$where);
			
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$arg['feature_id']));
			
			$data = array(
					'featured' => 1,
					);
			$where = array(
					'wp_user_id' => $row->provider_id,
					);
			$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
			
			service_finder_featured_payment_mail($row->provider_id);
			
			if ( ! $res) {
				$adminemail = get_option( 'admin_email' );
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t make payment for feature... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				$service_finder_Errors = json_encode($error);
				return $service_finder_Errors;
			}else{
				$msg = (!empty($service_finder_options['feature-payment'])) ? $service_finder_options['feature-payment'] : esc_html__('Payment made successfully to be featured', 'service-finder');
				$success = array(
						'status' => 'success',
						'suc_message' => $msg,
						);
				$service_finder_Success = json_encode($success);
				return $service_finder_Success;
			}
			
		}		
				
}