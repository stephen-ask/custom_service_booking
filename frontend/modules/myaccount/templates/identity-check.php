<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

$service_finder_options = get_option('service_finder_options');
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Params = service_finder_plugin_global_vars('service_finder_Params');
$url = str_replace('/','\/',$service_finder_Params['homeUrl']);
$adminajaxurl = str_replace('/','\/',admin_url('admin-ajax.php'));

if(service_finder_getUserRole($current_user->ID) == 'Provider'){
$userInfo = service_finder_getCurrentUserInfo();
}else{
$userInfo = service_finder_getUserInfo($globalproviderid);
}

$restrictmyaccount = (isset($service_finder_options['restrict-my-account'])) ? esc_attr($service_finder_options['restrict-my-account']) : '';
$approvalwaitingmsg = (!empty($service_finder_options['msg-identity-approval-waiting'])) ? esc_attr($service_finder_options['msg-identity-approval-waiting']) : esc_html__('Your identity has been uploaded and waiting for admin approval.', 'service-finder');
$identityapprovedmsg = (!empty($service_finder_options['msg-identity-approved'])) ? esc_attr($service_finder_options['msg-identity-approved']) : esc_html__('Your profile is verified and this badge will be published on your profile picture.', 'service-finder');

$identityapproved = $userInfo['identity'];
?>
<div id="identityCheck" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <?php if(!$restrictmyaccount || $identityapproved == 'approved'){ ?>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <?php }else{
			echo '<a href="'.esc_url(home_url()).'" class="gotohome"><i class="fa fa-home"></i> '.esc_html__('GO TO HOME', 'service-finder').'</a>';
			} ?>    
                <h4 class="modal-title"><?php esc_html_e('Upload Identity Proof', 'service-finder'); ?></h4>
            </div>
            <div class="modal-body">
                <?php echo (isset($service_finder_options['identity-check-description'])) ? $service_finder_options['identity-check-description'] : ''; ?>
                <form name="identitycheck" class="identitycheck">
        
        		<div class="row">
            <div class="col-md-12">
              <div class="rwmb-field rwmb-plupload_image-wrapper">
                <div class="rwmb-input">
                  <ul class="rwmb-images rwmb-uploaded" data-field_id="sfidentityuploader" data-delete_nonce="" data-reorder_nonce="" data-force_delete="0" data-max_file_uploads="25">
                    <?php
                $identity = service_finder_getProviderAttachments($globalproviderid,'identity');
				$hiddenidentityclass = '';
				if(!empty($identity)){
				foreach($identity as $id){
					
					$fileicon = new SERVICE_FINDER_ImageSpace();
					$arr  = $fileicon->get_icon_for_attachment($id->attachmentid);
					
					$hiddenidentityclass = 'hidden';

					$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
					
					$html = sprintf('<li id="item_%s">
					<img src="%s" />
					<div class="rwmb-image-bar">
						<a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
						<input type="hidden" name="identityattachmentid[]" value="%s">
					</div>
				</li>',
				esc_attr($id->attachmentid),
				esc_url($arr['src']),
				esc_attr($i18n_delete), esc_attr($id->attachmentid),
				esc_attr($id->attachmentid)
				);
					echo $html;	
				}
				}
				?>
                  </ul>
                  <div id="sfidentityuploader-dragdrop" class="RWMB-drag-drop drag-drop hide-if-no-js new-files <?php echo esc_attr($hiddenidentityclass); ?>" data-upload_nonce="" data-js_options="{&quot;runtimes&quot;:&quot;html5,silverlight,flash,html4&quot;,&quot;file_data_name&quot;:&quot;async-upload&quot;,&quot;browse_button&quot;:&quot;sfidentityuploader-browse-button&quot;,&quot;drop_element&quot;:&quot;sfidentityuploader-dragdrop&quot;,&quot;multiple_queues&quot;:true,&quot;max_file_size&quot;:&quot;8388608b&quot;,&quot;url&quot;:&quot;<?php echo esc_url($adminajaxurl); ?>&quot;,&quot;flash_swf_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.flash.swf&quot;,&quot;silverlight_xap_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.silverlight.xap&quot;,&quot;multipart&quot;:true,&quot;urlstream_upload&quot;:true,&quot;filters&quot;:[{&quot;title&quot;:&quot;Allowed  Files&quot;,&quot;extensions&quot;:&quot;doc,docx,jpg,jpeg,png,gif,pdf,xls,xlsx,rtf,txt,ppt,pptx&quot;}],&quot;multipart_params&quot;:{&quot;field_id&quot;:&quot;sfidentityuploader&quot;,&quot;action&quot;:&quot;file_upload&quot;}}">
                    <div class = "drag-drop-inside text-center">
                      <p class="drag-drop-info">
                        <?php esc_html_e('Drop files here (Valid Formats: doc,docx,pdf,xls,xlsx,rtf,txt,ppt,pptx,jpg,jpeg,png)', 'service-finder'); ?>
                      </p>
                      <p><?php esc_html_e('or', 'service-finder'); ?></p>
                      
                      <p class="drag-drop-buttons">
                        <input id="sfidentityuploader-browse-button" type="button" value="<?php esc_html_e('Select Files', 'service-finder'); ?>" class="button btn btn-default" />
                      </p>
                      
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php if(empty($identity) || $identityapproved == 'unapproved'){ ?>
            <div class="col-md-12">
                  <div class="form-group">
                    <input type="hidden" name="user_id" value="<?php echo esc_attr($globalproviderid); ?>" />
                    <input type="submit" class="btn btn-primary" value="<?php esc_html_e('Upload','service-finder'); ?>">
                  </div>
                </div>
            <?php } ?>    
            
            <?php
            if($identityapproved == 'approved')
			{
				echo '<div class="alert alert-info clear aon-alert-success aon-alert-identity" role="alert">';
				echo esc_html($identityapprovedmsg);
				echo '</div>';
				echo '<div class="text-center sf-approveidentify-icon">';
				echo service_finder_check_varified_icon($globalproviderid);
				echo '</div>';
			}
			if($identityapproved == 'unapproved')
			{
				$declinereason = get_user_meta( $globalproviderid, 'identity_decline_reason', true );
				echo '<div class="alert alert-info clear aon-alert-warning aon-alert-identity">';
				echo esc_html__('Your identity has been rejected by admin','service-finder');
				echo '<br/>';
				echo '<strong>';
				echo esc_html__('Decline reason is: ','service-finder');
				echo '</strong> ';
				echo esc_html($declinereason);
				echo '</div>';
			}
			if(!empty($identity) && $identityapproved != 'approved' && $identityapproved != 'unapproved')
			{
			echo '<div class="alert alert-info clear">'.esc_html__('Awaiting Identity verification','service-finder').'</div>';
			}
			?>
          </div>
                </form>
            </div>
        </div>
    </div>
</div>