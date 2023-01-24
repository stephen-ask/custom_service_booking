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

/**
 * Class SERVICE_FINDER_sedateQuotations
 */
class SERVICE_FINDER_PAYOUT_HISTORY extends SERVICE_FINDER_sedateManager{

	
	/*Initial Function*/
	public function service_finder_index()
    {
        
		/*Rander providers template*/
		$this->service_finder_render( 'index','payout-history' );
		
		/*Action for wp ajax call*/
		$this->service_finder_registerWpActions();
		
    }
	
	/*Initial Function*/
	public function service_finder_masspay()
    {
		
		$this->service_finder_render( 'masspay','payout-history' );
		
		/*Action for wp ajax call*/
		$this->service_finder_registerWpActions();
		
    }
	
	/*Actions for wp ajax call*/
	protected function service_finder_registerWpActions() {
       $_this = $this;
	   add_action(
                    'wp_ajax_get_admin_payout_history',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_get_admin_payout_history' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_get_admin_masspay_payout_history',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_get_admin_masspay_payout_history' ) );
                    }
						
                );		
    }
	
	/*Display payout history into datatable*/
	public function service_finder_get_admin_payout_history(){
		global $wpdb, $service_finder_Tables;
		$requestData= $_REQUEST;

		$payouts = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->payout_history);
		
		$data = array();
		
		foreach($payouts as $result){
			$nestedData=array(); 
			$userLink = service_finder_get_author_url($result->provider_id);
			$nestedData['postid'] = $result->id;
			$nestedData['bookingid'] = '#'.$result->booking_id;
			$nestedData['providername'] = '<a href="'.esc_url($userLink).'" target="_blank">'.service_finder_getProviderName($result->provider_id).'</a>';
			$nestedData['createdon'] = $result->created;
			$nestedData['availableon'] = $result->arrival_date;
			$nestedData['amount'] = service_finder_money_format($result->amount);
			$nestedData['stripeconnectmethod'] = $result->stripe_connect_type;
			$nestedData['connectaccountid'] = $result->connected_account_id;
			$nestedData['status'] = $result->status;
			
			$data[] = $nestedData;
		}
		
		$json_data = array( "data" => $data );
	
		echo json_encode($json_data);
	
		exit;
	}
	
	/*Display masspay payout history into datatable*/
	public function service_finder_get_admin_masspay_payout_history(){
	extract($_POST);
	global $wpdb;
	
	$args = array(
		'post_status' => array( 'payout-failed', 'completed' ),
		'post_type' => 'payout_transaction',
		'posts_per_page' => -1,
	);
	$the_query = new WP_Query( $args );
	$total = $the_query->found_posts;
	$data = array();
	if ( $the_query->have_posts() ) 
	{
		$nestedData = array(); 
		while ( $the_query->have_posts() ) : $the_query->the_post();
			$post_id = get_the_id();		
			
			$userid =  get_the_author_meta( 'ID' );
			
			$nestedData['postid'] = $post_id;
			
			$nestedData['bookingid'] = '#'.get_post_meta($post_id,'bookingid',true);

			$nestedData['providername'] = service_finder_getProviderName($userid);
			
			$nestedData['payoutdate'] = date("Y-m-d H:i:s",strtotime(get_post_meta($post_id,'paid_date',true)));
			
			$nestedData['amount'] = service_finder_money_format(get_post_meta($post_id,'payout_amount',true));
			
			$nestedData['status'] = get_post_status($post_id);
			
			$shortmsg = get_post_meta($post_id,'L_SHORTMESSAGE',true);
			$longmsg = get_post_meta($post_id,'L_LONGMESSAGE',true);
			$remark = $shortmsg.' <i class="tip-info fa fa-question" data-toggle="tooltip" title="" data-original-title="'.$longmsg.'"></i>';
			
			$nestedData['remark'] = $remark;
			
			$data[] = $nestedData;
		
		endwhile;
		wp_reset_postdata();
	}
	
	$json_data = array( "data" => $data );
	
	echo json_encode($json_data);

	exit;
	}
	
}