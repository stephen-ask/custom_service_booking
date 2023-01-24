<?php

/* ShortCode for quoation replies */
add_shortcode('service_finder_quotation_replies','service_finder_fn_quotation_replies');
function service_finder_fn_quotation_replies( $atts = array(), $content = null )
{
	ob_start();
	global $wpdb,$current_user,$service_finder_Tables,$service_finder_options;
	
	wp_enqueue_script('service-finder-quote-applications');

	$quoteid = service_finder_get_data($_GET,'quoteid');	

	$quoteinfo = service_finder_get_quote_info($quoteid);
	?>
    <h2><?php echo sprintf(esc_html__('Recommended %s', 'service-finder'),service_finder_provider_replace_string()); ?></h2>
    <h6><?php echo sprintf(esc_html__('%d %s have applied to this quote', 'service-finder'),service_finder_get_number_of_replies($quoteid),service_finder_provider_replace_string()); ?></h6>
	<div class="row">
    
        <?php
		if(!empty($quoteinfo))
		{
		if($current_user->ID == $quoteinfo->customer_id)
		{
		
		$providers = service_finder_get_related_quote_providers($quoteid);
		
		$ishired = $quoteinfo->hired;
		$assignedto = $quoteinfo->assignto;
		$providerid = $quoteinfo->provider_id;
		$quoteprice = $quoteinfo->quote_price;
		$reply = $quoteinfo->reply;
		?>
        <div class="sf-job-box-wrap"> <!-- Google Map -->
            <div class="container">
                <div class="sf-job-box-wrap"> <!-- Google Map -->
                    <div class="sf-userInfoArea">
                            <div class="sf-userInfoTop">
                                <div class="sf-userInfoLeft">
                                    <?php $profileimage = service_finder_get_avatar_by_userid($current_user->ID,'medium'); ?>
                                    <div class="sf-userImage"><img src="<?php echo esc_url($profileimage); ?>" alt=""></div>
                                </div>
                                <div class="sf-userInfoRight">
                                    <ul>
                                        <li>
                                            <strong><?php echo esc_html__( 'Quote ID', 'service-finder' ); ?> :</strong>
											<span><?php echo '#'.esc_html($quoteid); ?></span>
                                        </li>
										<?php if($quoteinfo->date != ''){ ?>
                                        <li>
                                            <strong><i class="fa fa-calendar"></i><?php echo esc_html__( 'Date', 'service-finder' ); ?> :</strong>
                                            <span><?php echo service_finder_date_format($quoteinfo->date); ?></span>
                                        </li>
                                        <?php } ?>
                                        <?php if($quoteinfo->name != ''){ ?>
                                        <li>
                                            <strong><i class="fa fa-user"></i><?php echo esc_html__( 'Name', 'service-finder' ); ?> :</strong>
                                            <span><?php echo esc_html($quoteinfo->name); ?></span>
                                        </li>
                                        <?php } ?>
                                        <?php if($quoteinfo->email != ''){ ?>
                                        <li>
                                            <strong><i class="fa fa-envelope"></i><?php echo esc_html__( 'Email', 'service-finder' ); ?> :</strong>
                                            <span><?php echo esc_html($quoteinfo->email); ?></span>
                                        </li>
                                        <?php } ?>
                                        <?php if($quoteinfo->phone != ''){ ?>
                                        <li>
                                            <strong><i class="fa fa-phone"></i><?php echo esc_html__( 'Phone', 'service-finder' ); ?> :</strong>
                                            <span><?php echo esc_html($quoteinfo->phone); ?></span>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                    <?php if($quoteinfo->message != ''){ ?>
                                    <div class="sf-userInfoDes">
                                        <p><?php echo esc_html($quoteinfo->message); ?></p>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                 </div>
            </div>
        </div>
        
        <!-- Right part start -->
        <div class="col-md-12">  
        
            <div class="sf-serach-result-listing">
            	<?php
				if($providerid > 0)
				{
					service_finder_view_quote_provider($quoteinfo,$providerid,$quoteprice,$reply);
				}
				
				if(!empty($providers))
				{
					foreach($providers as $provider)
					{
						if($provider->related_provider_id != $providerid){
						$providerid = $provider->related_provider_id;
						$quoteprice = $provider->related_quote_price;
						$reply = $provider->related_reply;
						service_finder_view_quote_provider($quoteinfo,$providerid,$quoteprice,$reply);
						}
					}
				}
				
				require SERVICE_FINDER_BOOKING_LIB_DIR . '/quote-booking.php';
				?>
            </div>
        </div>
        <!-- Right part END --> 
        <?php
        }else
		{
			echo service_finder_no_access_layout(esc_html__( 'You are not authorized to', 'service-finder' ),esc_html__( 'access this page.', 'service-finder' ));
		}
		}else
		{
			echo service_finder_no_access_layout(esc_html__( 'Quotation that you are looking', 'service-finder' ),esc_html__( 'for is not exists', 'service-finder' ));
		}
		?>
    </div>
	<?php
	return ob_get_clean();
}

function service_finder_view_quote_provider($quoteinfo = array(),$providerid = '',$quoteprice = 0,$reply = '')
{
	global $wpdb,$current_user,$service_finder_Tables,$service_finder_options;
	
	$ishired = $quoteinfo->hired;
	$quoteid = $quoteinfo->id;
	$assignedto = $quoteinfo->assignto;
	$profileurl = service_finder_get_author_url($providerid);
	$profileimage = service_finder_get_avatar_by_userid($providerid,'service_finder-provider-medium');
	$providerinfo = service_finder_get_provier_info($providerid);
	$categories = service_finder_get_data($providerinfo,'category_id');
	?>
	<div class="sf-serach-result-wrap">
		<div class="sf-serach-result-left">
			<?php if(service_finder_is_featured($providerid)){ ?>
			 <div class="sf-featuerd-label"><span><?php echo esc_html__( 'Featured', 'service-finder' ); ?></span></div>
			<?php } ?> 
			<div class="sf-serach-result-propic">
				<img src="<?php echo esc_url($profileimage); ?>" alt="">
				<?php echo service_finder_check_varified_icon($providerid); ?>
			</div>
			<div class="sf-serach-result-bookNow">
				<?php
				if($ishired == 'yes'){
					if($assignedto == $providerid){
						echo '<span class="sf-hiring-status status-jobhired">'.esc_html__( 'Hired', 'service-finder' ).'</span>';
					}
				}else{
						if($quoteprice > 0)
						{
							$params = array(
								'quoteid' 	=> $quoteid,
								'providerid' 	=> $providerid,
								'quoteprice' 	=> $quoteprice,
							);
							
							$walletamount = service_finder_get_wallet_amount($current_user->ID);
							$walletsystem = service_finder_check_wallet_system();
							
							$settings = service_finder_getProviderSettings($providerid);
							
							if(service_finder_getUserRole($current_user->ID) == 'Provider' || service_finder_getUserRole($current_user->ID) == 'administrator'){
							$skipoption = true;
							}else{
							$skipoption = false;
							}
							
							$paymentoptions = '';
							$payflag = 0;
							$stripepublickey = '';
							
							if(service_finder_get_payment_goes_to() == 'provider')
							{
								ob_start();
								$stripepublickey = $settings['stripepublickey'];
								if(!empty($settings['paymentoption']))
								{
									foreach($settings['paymentoption'] as $paymentoption)
									{
									$payflag = 1;
									?>
									<div class="radio sf-radio-checkbox">
									  <input type="radio" value="<?php echo esc_attr($paymentoption); ?>" name="bookingpayment_mode" id="paymentvia<?php echo esc_attr($paymentoption); ?>" >
									  <label for="paymentvia<?php echo esc_attr($paymentoption); ?>"><?php echo '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/'.$paymentoption.'.jpg" title="'.esc_attr(ucfirst($paymentoption)).'" alt="'.esc_attr(ucfirst($paymentoption)).'">'; ?></label>
									</div>
									<?php
									}
								}elseif(!service_finder_check_wallet_system()){
									$payflag = 0;
									echo '<p>';
									echo esc_html__('There is no payment method available.','service-finder');
									echo '</p>';
								}
								
								if(service_finder_check_wallet_system())
								{
									$payflag = 1;
								}
								
								echo service_finder_add_wallet_option('bookingpayment_mode','paymentvia');
                                echo service_finder_add_skip_option('bookingpayment_mode','paymentvia');
								
								$paymentoptions = ob_get_clean();
							}

							$params = array(
								'quoteid' 	=> $quoteid,
								'skipoption' 	=> $skipoption,
								'providerid' 	=> $providerid,
								'quoteprice' 	=> $quoteprice,
								'walletamount' 	=> $walletamount,
								'walletamountwithcurrency' 	=> service_finder_money_format($walletamount),
								'walletsystem' 	=> $walletsystem,
								'adminfeetype' 	=> service_finder_get_data($service_finder_options,'admin-fee-type'),
								'adminfeefixed' 	=> service_finder_get_data($service_finder_options,'admin-fee-fixed'),
								'adminfeepercentage' 	=> service_finder_get_data($service_finder_options,'admin-fee-percentage'),
								'pay_booking_amount_to' 	=> service_finder_get_payment_goes_to(),
								'paymentoptions' 	=> $paymentoptions,
								'paymentoptionsavl' 	=> $payflag,
								'stripepublickey' 	=> $stripepublickey,
								'is_booking_free_paid' 	=> service_finder_is_booking_free_paid($providerid),
							);
							
							echo '<a href="javascript:;" class="btn btn-primary bookthisprovider" data-params="'.esc_attr(wp_json_encode( $params )).'">'.esc_html__( 'Book Now', 'service-finder' ).'</a>';
						}
				}
				?>
				<a href="<?php echo esc_url($profileurl); ?>" target="_blank" class="btn-link"><?php echo esc_html__( 'View Profile', 'service-finder' ); ?></a>
                <?php 
				if(get_user_meta($providerid,'primary_category',true) != '')
				{
					echo '<span class="sf-profilecat-label"><a href="'.esc_url(service_finder_getCategoryLink(get_user_meta($providerid,'primary_category',true))).'">'.service_finder_getCategoryName(get_user_meta($providerid,'primary_category',true)).'</a></span>';
				}
				?>
			</div>
		</div>
		<div class="sf-serach-result-right">
		
			<div class="sf-serach-result-head">
				<h3 class="sf-serach-result-title"><?php echo service_finder_getCompanyName($providerid); ?></h3>
                <span class="sf-serach-result-name"><i class="fa fa-user"></i> <?php echo service_finder_getProviderFullName($providerid); ?></span>
				 <?php if(service_finder_getAddress($providerid) != "" && $service_finder_options['show-postal-address']){ ?>
				 <div class="sf-serach-result-address"><i class="fa fa-map-marker"></i> <?php echo service_finder_getAddress($providerid); ?> </div>
				 <?php } ?> 	
				 <div class="sf-serach-result-lable-wrarp">
                    <?php
					if($reply != '')
					{
					echo '<a href="javascript:;" class="sf-serach-lable-quotation viewquotereply" data-quoterepply="'.esc_attr($reply).'">'.esc_html__( 'View Quotation Reply', 'service-finder' ).'</a>';
					}
					?>
				 </div>
				 <div class="sf-serach-rating-addto">
					<div class="sf-serach-ratings">
						<?php echo service_finder_displayRating(service_finder_getAverageRating($providerid)); ?>
						<span class="sf-serach-ratings-total">
						<?php 
						$totalreview = service_finder_get_total_reviews($providerid);
						if($totalreview > 1){
							printf( esc_html__('(%d Reviews)', 'service-finder' ), $totalreview );
						}else{
							printf( esc_html__('(%d Review)', 'service-finder' ), $totalreview );
						}
						?>
						</span>
					</div>
					<?php
					$myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID,$providerid));

					if(!empty($myfav)){
						echo '<a href="javascript:;" id="removefave-'.esc_attr($providerid).'" class="remove-job-favorite sf-serach-addToFav" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'"><i class="fa fa-heart"></i></a>';
					}else
					{
						echo '<a href="javascript:;" id="addfave-'.esc_attr($providerid).'" class="add-job-favorite sf-serach-addToFav" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'"><i class="fa fa-heart-o"></i></a>';
					}
					?>
				 </div>
				 <?php
				 if($categories != '')
				 {
					$displaycat = array();
					$categories = explode(',',$categories);
					foreach($categories as $categoryid)
					{
						$displaycat[] = '<a href="'.esc_url(service_finder_getCategoryLink($categoryid)).'">'.service_finder_getCategoryName($categoryid).'</a>';
					}
					?>
					<div class="sf-serach-categoriList">
						<?php echo implode(',',$displaycat); ?>
					 </div>
					<?php
				 }
				 ?>
			 </div>
			 
			 <div class="sf-serach-result-body">
				 <div class="sf-serach-proviText">
					<?php 
					if(service_finder_get_data($providerinfo,'bio') != ""){
						echo apply_filters('the_content', service_finder_get_data($providerinfo,'bio'));
					}
					?>
				 </div>
			 </div>
			 
			 <div class="sf-serach-result-footer clearfix">
				<?php 
				if(class_exists('aone_messaging')){
					$args = array(
								'view' => 'popup',
								'type' => 'quote',
								'targetid' => $quoteid,
								'fromid' => $current_user->ID,
								'toid' => $providerid,
							);
					do_action( 'aone_messaging_custom_send_message', $args );
				}
				?>
				<?php
				if($quoteprice > 0)
				{
					$price =  service_finder_money_format($quoteprice);
					echo '<div class="sf-serach-result-price">'.$price.'</div>';
					
				}
				?>
			 </div>
		</div>
	</div>
	<?php
}