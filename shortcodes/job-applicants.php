<?php

/* ShortCode for quoation replies */
add_shortcode('service_finder_job_applicants','service_finder_fn_job_applicants');
function service_finder_fn_job_applicants( $atts = array(), $content = null )
{
	ob_start();
	global $wpdb,$current_user,$service_finder_Tables,$service_finder_options;
	
	wp_enqueue_script('service-finder-job-applications');
	
	$jobid = service_finder_get_data($_GET,'jobid');
	$sendinvitation = service_finder_get_data($_GET,'sendinvitation');
	$radiussearchunit = (isset($service_finder_options['radius-search-unit'])) ? esc_attr($service_finder_options['radius-search-unit']) : 'mi';
	
	$jsdata = 'var jsdata = { 
				"jobid": '.$jobid.',
				"radiussearchunit": "'.$radiussearchunit.'",
				"stripepublickey": "'.service_finder_get_stripe_public_key().'"
				};';
	wp_add_inline_script('service-finder-job-applications', $jsdata, 'before');
	
	$showfilters = (isset($service_finder_options['job-show-filters'])) ? $service_finder_options['job-show-filters'] : true;
	$locationfilter = (isset($service_finder_options['job-location-filter'])) ? $service_finder_options['job-location-filter'] : true;
	$typefilter = (isset($service_finder_options['job-type-filter'])) ? $service_finder_options['job-type-filter'] : true;
	$quotefilter = (isset($service_finder_options['job-quote-filter'])) ? $service_finder_options['job-quote-filter'] : true;
	$ratingfilter = (isset($service_finder_options['job-rating-filter'])) ? $service_finder_options['job-rating-filter'] : true;
	$amenityfilter = (isset($service_finder_options['job-amenity-filter'])) ? $service_finder_options['job-amenity-filter'] : true;
	
	$customerinfo = service_finder_getUserInfo($current_user->ID);
	
	?>
    <div class="sf-recomm-header">
    <h2><?php echo service_finder_get_data($service_finder_options,'job-recommended-lession-text'); ?></h2>
    <h4><?php $tokentext = service_finder_get_data($service_finder_options,'job-number-of-provider-have-applied-text');
	echo str_replace('%NUMBER%',service_finder_get_number_of_applicants($jobid),$tokentext);
	?>
	</h4>
    </div>
    <div class="row">
    
        <?php
		$providers = service_finder_get_job_related_providers($jobid);
		
		$jobauthor = get_post_field( 'post_author', $jobid );
		if($jobauthor > 0 && $jobid > 0)
		{
		if($current_user->ID == $jobauthor)
		{
		$categories = service_finder_get_job_categories($jobid);
		?>
        <?php if($showfilters == true){ ?>
        <!-- Left part start -->        
        <form name="jobapplicantsfilter" id="jobapplicantsfilter">
		<?php if($quotefilter == true){ ?>
        <div class="staging-toggle-wrapper">
            <div class="staging-toggle-filter">
                <div class="form-group <?php echo ($sendinvitation == 'yes') ? '' : 'active'; ?>" id="quotereceivedyes">
                        <div class="radio sf-radio-checkbox">
                             <input type="radio" id="quote-received" name="quotereceived" value="yes" <?php echo ($sendinvitation == 'yes') ? '' : 'checked="checked"'; ?>>
                             <label for="quote-received"><?php echo esc_html__( 'View applicants', 'service-finder' ); ?></label>
                         </div>
                    </div>
                <div class="form-group <?php echo ($sendinvitation == 'yes') ? 'active' : ''; ?>" id="quotereceivedno">
                        <div class="radio sf-radio-checkbox">
                             <input type="radio" id="quote-not-received" name="quotereceived" value="no" <?php echo ($sendinvitation == 'yes') ? 'checked="checked"' : ''; ?>>
                             <label for="quote-not-received"><?php echo esc_html__( 'Send invitations', 'service-finder' ); ?></label>
                         </div>
                    </div>
            </div>
        </div>
        <?php } ?>
        <div class="col-md-4">
            <div class="sf-serach-bar-verticle">
                <?php if($locationfilter == true){ ?>
                <div class="sf-serach-bar-box">
                    <h5 class="sf-serach-bar-label"><i class="fa fa-map-marker"></i><?php echo service_finder_get_data($service_finder_options,'job-service-perform-text'); ?></h5>
                    <div class="sf-serach-bar-content">
                        <div class="form-group">
                            <div class="radio sf-radio-checkbox">
                                 <input type="checkbox" id="service_perform_customer_location" name="service_perform_at[]" value="customer_location">
                                 <label for="service_perform_customer_location" style="text-transform:none"><?php echo esc_html__( 'My location', 'service-finder' ); ?></label>
                             </div>
                        </div>  
                        <div class="form-group">
                            <div class="radio sf-radio-checkbox">
                                 <input type="checkbox" id="service_perform_provider_location" name="service_perform_at[]" value="provider_location">
                                 <label for="service_perform_provider_location" style="text-transform:none"><?php echo sprintf(esc_html__('%s location', 'service-finder'),service_finder_provider_replace_string()); ?></label>
                             </div>
                        </div>
                    </div> 
                </div>
                <div class="sf-serach-bar-box" id="filter-by-location-wrap">
                    <h5 class="sf-serach-bar-label"><i class="fa fa-map-marker"></i><?php echo service_finder_get_data($service_finder_options,'job-filter-by-location-text'); ?></h5>
                    <div class="sf-serach-bar-content">
                        <input type="text" class="form-control sf-form-control" name="filterlocation" id="filterlocation" placeholder="<?php echo esc_html__( 'Enter location', 'service-finder' ); ?>" value="<?php echo esc_html($customerinfo['address']); ?>">
                    </div> 
                    <div class="sf-serach-bar-content" id="sf-jobserach-bar-radius">
                        <input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" type="text" class="form-control sf-form-control" name="radius" placeholder="<?php echo service_finder_get_data($service_finder_options,'job-filter-radius-input-placeholder'); ?>">
                    </div> 
                </div>
                <?php } ?>
                <?php if($amenityfilter == true){ ?>
                <div class="sf-serach-bar-box">
                    <h5 class="sf-serach-bar-label"><i class="fa fa-shield"></i><?php echo esc_html__( 'Filter by amenities', 'service-finder' ); ?></h5>
                    <div class="sf-serach-bar-content">
                        <select name="amenities" class="sf-select-box form-control sf-form-control" id="filteramenities">
						<?php
						 echo '<option value="">'. esc_html__( 'Select amenity', 'service-finder' ).'</option>';
                        if(class_exists('service_finder_texonomy_plugin')){
                        $limit = 1000;
                        $amenities = service_finder_getAmenityList($limit);
                        $texonomy = 'sf-amenities';
                        if(!empty($amenities)){
                            foreach($amenities as $amenity){
                                echo '<option value="'.esc_attr($amenity->term_id).'">'. $amenity->name.'</option>';
                                $term_children = get_term_children($amenity->term_id,$texonomy);
                                $namearray = array();
                                if(!empty($term_children)){
                                    
                                    foreach ($term_children as $child) {
                                        $term = get_term_by( 'id', $child, $texonomy );
                            
                                        $namearray[$term->name]= $child;
                        
                                    }
                                    
                                    if(!empty($namearray)){
                                    ksort($namearray);
                                    
                                    foreach($namearray as $key => $value) {
                                    
                                        $term_child_id = $value;
        
                                        $term_child = get_term_by('id',$term_child_id,$texonomy);
                                        
                                        echo '<option value="'.esc_attr($term_child_id).'" data-content="<span class=\'childcat\'>'.esc_attr($term_child->name).'</span>">'. $term_child->name.'</option>';
                                        
                                    }
                                    }
                                }
                            }
                        }	
                        }
                        ?>
                      </select>
                    </div> 
                </div>
                <?php } ?>
				<?php if($typefilter == true){ ?>
                <div class="sf-serach-bar-box">
                    <h5 class="sf-serach-bar-label"><i class="fa fa-shield"></i><?php echo sprintf(esc_html__( 'Filter by %s type', 'service-finder' ),service_finder_provider_replace_string()); ?></h5>
                    <div class="sf-serach-bar-content">
                        <div class="form-group">
                            <div class="checkbox sf-radio-checkbox">
                                 <input type="checkbox" id="verified-provider" name="providertype[]" value="verified">
                                 <label for="verified-provider"><?php echo sprintf(esc_html__( 'Verified %s', 'service-finder' ),service_finder_provider_replace_string()); ?></label>
                             </div>
                        </div>  
                        <div class="form-group">
                            <div class="checkbox sf-radio-checkbox">
                                 <input type="checkbox" id="featured-provider" name="providertype[]" value="featured">
                                 <label for="featured-provider"><?php echo sprintf(esc_html__( 'Featured %s', 'service-finder' ),service_finder_provider_replace_string()); ?></label>
                             </div>
                        </div>
                    </div> 
                </div>
                <?php } ?>
                <?php if($ratingfilter == true){ ?>
                <div class="sf-serach-bar-box">
                    <h5 class="sf-serach-bar-label"><i class="fa fa-star-half-empty"></i><?php echo esc_html__( 'Filter by rating', 'service-finder' ); ?></h5>
                    <div class="sf-serach-bar-content">
                        <div class="form-group">
                            <div class="checkbox sf-radio-checkbox">
                                 <input type="checkbox" id="prorating5" name="providerrating[]" value="5">
                                 <label for="prorating5"><?php echo esc_html__( '5 Star', 'service-finder' ); ?></label>
                             </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox sf-radio-checkbox">
                                 <input type="checkbox" id="prorating4" name="providerrating[]" value="4">
                                 <label for="prorating4"><?php echo esc_html__( '4 Star', 'service-finder' ); ?></label>
                             </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox sf-radio-checkbox">
                                 <input type="checkbox" id="prorating3" name="providerrating[]" value="3">
                                 <label for="prorating3"><?php echo esc_html__( '3 Star', 'service-finder' ); ?></label>
                             </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox sf-radio-checkbox">
                                 <input type="checkbox" id="prorating2" name="providerrating[]" value="2">
                                 <label for="prorating2"><?php echo esc_html__( '2 Star', 'service-finder' ); ?></label>
                             </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox sf-radio-checkbox">
                                 <input type="checkbox" id="prorating1" name="providerrating[]" value="1">
                                 <label for="prorating1"><?php echo esc_html__( '1 Star', 'service-finder' ); ?></label>
                             </div>
                        </div>  
                    </div> 
                </div>
                <?php } ?>
                
            </div>
            <div class="sf-job-box">
                    <h5 class="sf-serach-bar-label"><i class="fa fa-star-half-empty"></i><?php echo sprintf(esc_html__( '%s Summary', 'service-finder' ),service_finder_get_data($service_finder_options,'job-text')); ?></h5>
                    <h2 class="sf-job-box-title"><?php echo get_the_title($jobid); ?></h2>
                    <?php if(!empty($categories)){ ?>
                    <div class="clear"></div>
                    <div class="sf-provider-cat sf-p-c-v2"><strong><?php esc_html_e('Categories', 'service-finder'); ?>: </strong> <?php echo implode(',', $categories ); ?></div>
                    <?php } ?>
                    <span class="sf-job-box-price">
					<?php echo service_finder_money_format(get_post_meta($jobid,'_job_cost',true)); ?><br/>
                    <span class="sf-jobdetail-link"><a class="btn btn-primary" href="<?php echo get_the_permalink($jobid); ?>" target="_blank"><?php echo sprintf(esc_html__('View %s', 'service-finder'),service_finder_get_data($service_finder_options,'job-text')) ?></a></span>
                    </span>
                    <div class="clear"></div>
                    <?php
                    $jobcontent = get_post_field( 'post_content', $jobid );
                    echo apply_filters('the_content', $jobcontent);
                    ?>
                </div>
        </div>
        </form>
        <!-- Left part END -->  
        <?php } ?>    
        
        <!-- Right part start -->
        <div class="<?php echo ($showfilters == true) ? 'col-md-8' : 'col-md-12'; ?>">  
        
            <div class="sf-serach-result-listing" id="loadfiltered">
            	<div id="loadfilteredex">
				<?php
				$totalproviders = count($providers);
				$providerdefaultflag = 0;
				if(!empty($providers))
				{
				?>
                    <div class="sf-chkallinv-outer" style="display:<?php echo ($sendinvitation == 'yes') ? 'block' : 'none'; ?>">
                        <div class="sf-chkallinv-left">
                            <label for="allinvitationrow" style="text-transform:none"><?php echo service_finder_get_data($service_finder_options,'job-send-invitation-selected-lession-provider-text'); ?></label>
                        </div>
                        <div class="sf-chkallinv-right">
                            <button id="sendallinvitations" class="btn btn-primary" data-jobid="<?php echo esc_attr($jobid); ?>">
                              <?php echo esc_html__('Send', 'service-finder') ?>
                            </button>
                        </div>
                    </div>
                    
					<?php
					$i = 1;
					foreach($providers as $provider)
					{
						$providerid = $provider->wp_user_id;
						$profileurl = service_finder_get_author_url($providerid);
						$profileimage = service_finder_get_avatar_by_userid($providerid,'service_finder-provider-medium');
						$providerinfo = service_finder_get_provier_info($providerid);
						$categories = $providerinfo->category_id;
						
						if(service_finder_has_applied_for_job($jobid,$providerid))
						{
							continue;
						}
						
						?>
						<div class="sf-serach-result-wrap moreproviderbox" <?php echo ($i >= 10) ? 'style="display:none"' : ''; ?>>
                            <div class="sf-serach-result-left">
                            	<?php if(service_finder_is_featured($providerid)){ ?>
                                 <div class="sf-featuerd-label"><span><?php echo esc_html__( 'Featured', 'service-finder' ); ?></span></div>
                                <?php } ?> 
                                <div class="sf-serach-result-propic">
                                    <img src="<?php echo esc_url($profileimage); ?>" alt="">
                                    <?php if(service_finder_is_varified_user($providerid)){ ?>
                                    <span class="sf-featured-approve">
                                        <i class="fa fa-check"></i><span><?php esc_html_e('Verified Provider', 'service-finder'); ?></span>
                                    </span>
                                    <?php } ?>
                                </div>
                                <div class="sf-serach-result-bookNow">
                                	<?php
									if(get_post_meta($jobid,'_filled',true)){
										if(get_post_meta($jobid,'_assignto',true) == $providerid){
											echo '<span class="sf-hiring-status status-jobhired">'.esc_html__( 'Hired', 'service-finder' ).'</span>';
										}
									}else{
										$jobexpire = get_post_meta($jobid,'_job_expires',true);
										
										if(strtotime(date('Y-m-d')) > strtotime( $jobexpire )){
											echo '<a href="javascript:;" class="btn btn-primary">'.esc_html__( 'Job Expired', 'service-finder' ).' <i class="fa fa-times"></i></a>';
										}else{
											if(service_finder_has_applied_for_job($jobid,$providerid))
											{
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
													
													echo service_finder_add_wallet_option('bookingpayment_mode','paymentvia');
													echo service_finder_add_skip_option('bookingpayment_mode','paymentvia');
													
													$paymentoptions = ob_get_clean();
												}

												$params = array(
													'jobid' 	=> $jobid,
													'skipoption' 	=> $skipoption,
													'providerid' 	=> $providerid,
													'jobtitle' 	=> get_the_title($jobid),
													'jobprice' 	=> service_finder_get_job_quote_price($providerid,$jobid),
													'jobhours' 	=> get_post_meta( $jobid, '_job_hours', true ),
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
									}
									?>
                                    <a href="<?php echo esc_url($profileurl); ?>" target="_blank" class="btn-link"><?php echo esc_html__( 'View Profile', 'service-finder' ); ?></a>
                                    <?php 
									if(get_user_meta($providerid,'primary_category',true) != '')
									{
										echo '<span class="sf-profilecat-label">'.service_finder_getCategoryName(get_user_meta($providerid,'primary_category',true)).'</span>';
									}
									?>
                                    <div class="checkbox sf-radio-checkbox" data-toggle="tooltip" title="<?php echo esc_html__('Send Invitation', 'service-finder') ?>">
                                         <input type="checkbox" id="invitationrow-<?php echo esc_attr($providerid); ?>" class="invitationrow" value="<?php echo esc_attr($providerid); ?>">
                                         <label for="invitationrow-<?php echo esc_attr($providerid); ?>" style="text-transform:none;"><?php echo esc_html__( 'Add to multi-invite', 'service-finder' ); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="sf-serach-result-right">
                            
                                <div class="sf-serach-result-head">
                                    <h3 class="sf-serach-result-title"><?php echo service_finder_getCompanyName($providerid); ?></h3>
                                    <span class="sf-serach-result-name"><i class="fa fa-user"></i> <?php echo service_finder_getProviderFullName($providerid); ?></span>
                                     <?php 
									 if($service_finder_options['show-postal-address']){ 
									 if(($provider->service_perform_at == 'provider_location' || $provider->service_perform_at == 'both') && service_finder_getAddress($providerid) != "")
									 {
									 $providerlat = get_user_meta($providerid,'providerlat',true); 
									 $providerlng = get_user_meta($providerid,'providerlng',true); 
									 $locationzoomlevel = get_user_meta($providerid,'locationzoomlevel',true); 
									 ?>
                                     <div class="sf-serach-result-address"><i class="fa fa-map-marker"></i> <?php echo service_finder_getAddress($providerid); ?> </div>
                                     <button class="btn btn-primary btn-sm margin-b-10" style="margin-bottom:10px;" data-tool="tooltip" id="viewjoblocation" data-locationzoomlevel="<?php echo esc_attr($locationzoomlevel); ?>" data-providerlat="<?php echo esc_attr($providerlat); ?>" data-providerlng="<?php echo esc_attr($providerlng); ?>" type="button">
                                      <i class="fa fa-map-o"></i> <?php echo esc_html__('View Map','service-finder'); ?>
                                      </button>
									 <?php 
									 }elseif($provider->service_perform_at != 'provider_location' && service_finder_getshortAddress($providerid) != "")
									 {
									 ?>
									 <div class="sf-serach-result-address"><i class="fa fa-map-marker"></i> <?php echo service_finder_getshortAddress($providerid); ?> </div>
									 <?php
									 }
									 ?>
                                     <?php
                                     if($provider->service_perform_at == 'provider_location' || $provider->service_perform_at == 'customer_location' || $provider->service_perform_at == 'both')
									 {
									 echo '<h4>'.esc_html__( 'Available Locations', 'service-finder') .'</h4>';
									 ?>
									 <?php
                                     if($provider->service_perform_at == 'provider_location')
									 {
									 ?>
									 <div class="sf-serach-result-address"><i class="fa fa-map-pin"></i> <?php echo esc_html__( 'Provider Location', 'service-finder' ); ?> </div>
									 <?php
									 }elseif($provider->service_perform_at == 'customer_location')
									 {
									 ?>
									 <div class="sf-serach-result-address"><i class="fa fa-car"></i> <?php echo esc_html__( 'Your Location', 'service-finder' ); ?> </div>
									 <?php
									 }elseif($provider->service_perform_at == 'both')
									 {
									 ?>
									 <div class="sf-serach-result-address"><i class="fa fa-map-pin"></i> <?php echo esc_html__( 'Provider Location', 'service-finder' ); ?> </div>
									 <div class="sf-serach-result-address"><i class="fa fa-car"></i> <?php echo esc_html__( 'Your Location', 'service-finder' ); ?> </div>
									 <?php
									 }
									 ?>
									 <?php
									 }
									 } ?> 	
                                     <div class="sf-serach-result-lable-wrarp">
                                        <?php
                                        if(service_finder_has_sent_invitation($jobid,$providerid))
										{
											echo '<span class="sf-serach-lable-invitation">'.esc_html__( 'Invitation Sent', 'service-finder' ).'</span>';
										}else
										{
											if(!service_finder_has_applied_for_job($jobid,$providerid))
											{
											echo '<span id="jobinvitation-'.$jobid.'-'.$providerid.'"><a class="sf-serach-lable-invitation" href="javascript:;" data-action="invite" data-redirect="no" data-jobid="'.esc_attr($jobid).'" data-providerid="'.esc_attr($providerid).'" data-toggle="modal" data-target="#invite-job">'.sprintf(esc_html__('Invite for %s', 'service-finder'),service_finder_get_data($service_finder_options,'job-text')).'</a><span>';
											}
										}
										?>
                                        <?php
                                        if(service_finder_has_applied_for_job($jobid,$providerid))
										{
											echo '<a href="javascript:;" class="sf-serach-lable-quotation provider_description" data-jobid="'.esc_attr($jobid).'" data-providerid="'.esc_attr($providerid).'">'.esc_html__( 'View Quotation', 'service-finder' ).'</a>';
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
                                    
                                 </div>
                                 
                                 <div class="sf-serach-result-body">
                                     <div class="sf-serach-proviText">
                                        <?php 
										if($providerinfo->bio != ""){
											echo apply_filters('the_content', $providerinfo->bio);
										}
										?>
                                     </div>
                                 </div>
                                 
                                 <div class="sf-serach-result-footer clearfix">
                                    <?php 
									if(class_exists('aone_messaging')){
										$args = array(
													'view' => 'popup',
													'type' => 'job',
													'targetid' => $jobid,
													'fromid' => $current_user->ID,
													'toid' => $providerid,
												);
										do_action( 'aone_messaging_custom_send_message', $args );
									}
									?>
                                    <?php
									if(service_finder_has_applied_for_job($jobid,$providerid))
									{
										$price =  service_finder_money_format(service_finder_get_job_quote_price($providerid,$jobid));
										echo '<div class="sf-serach-result-price">'.$price.'</div>';
										
									}
									?>
                                 </div>
                            </div>
                        </div>
						<?php
						$providerdefaultflag = 1;
					$i++;
					}
					
					if($totalproviders > 10)
					{
					?>
					<div id="loadmorejobproviders">
						<a href="javascript:;"><?php esc_html_e( 'Load More','makeover' ); ?> <i class="fa fa-refresh"></i></a>
					</div>
					<?php
					}
				}
				
				if($providerdefaultflag == 0)
				{
					if($sendinvitation == 'yes')
					{
						if(service_finder_get_data($service_finder_options,'job-no-result-html-send-invitations') != '')
						{
						echo '<div class="sf-noresult-outer">';
						echo service_finder_get_data($service_finder_options,'job-no-result-html-send-invitations');
						echo '</div>';
						}else{
						echo '<div class="sf-noresult-outer">';
						echo esc_html__('No Results Found.', 'service-finder');
						echo '</div>';
						}					
					}else{
						if(service_finder_get_data($service_finder_options,'job-no-result-html-view-applicants') != '')
						{
						echo '<div class="sf-noresult-outer">';
						echo service_finder_get_data($service_finder_options,'job-no-result-html-view-applicants');
						echo '</div>';
						}else{
						echo '<div class="sf-noresult-outer">';
						echo esc_html__('No Results Found.', 'service-finder');
						echo '</div>';
						}
					}
					
				}
				?>
				</div>
            </div>
        </div>
        <?php
		require SERVICE_FINDER_BOOKING_LIB_DIR . '/job-booking.php';
		?>
        <!-- Right part END --> 
        <?php
        }else
		{
			echo service_finder_no_access_layout(esc_html__( 'You are not authorized to', 'service-finder' ),esc_html__( 'access this page.', 'service-finder' ));
		}
		}else
		{
			echo service_finder_no_access_layout(esc_html__( 'Job that you are looking', 'service-finder' ),esc_html__( 'for is not exists', 'service-finder' ));
		}
		?>
    </div>
	<?php

	return ob_get_clean();
}

