<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

$service_finder_ThemeParams = service_finder_plugin_global_vars('service_finder_ThemeParams');
$service_finder_options = get_option('service_finder_options');
?>
<!-- Modal Login & Register-->

<!-- Modal -->
<div id="invite-job" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php esc_html_e('Invite for JOB', 'service-finder'); ?></h4>
      </div>
      <div class="modal-body">
      <div class="row ">
           <form class="inviteforjob" method="post">
            <div class="col-md-12">
              <div class="form-group">
                <div class="input-group">
                  <?php
				  $args = array(
						'post_type'           => 'job_listing',
						'post_status'         => array( 'publish' ),
						'ignore_sticky_posts' => 1,
						'posts_per_page'      => -1,
						'orderby'             => 'date',
						'order'               => 'desc',
						'author'              => get_current_user_id(),
						'meta_query' 		  => array(
													array(
														'key'     => '_filled',
														'value'   => 1,
														'compare' => '!=',
													),
												),
						);
					
					$jobs = new WP_Query( $args );
					
					if ( $jobs->have_posts() ) {
						echo '<select name="invitedjob" class="form-control sf-form-control sf-select-box">';
						while ( $jobs->have_posts() ) {
							$jobs->the_post();
							echo '<option value="'.get_the_id().'">' . get_the_title() . '</option>';
						}
						echo '</select>';
						wp_reset_postdata();
					} else {
						echo esc_html__('There are no jobs. Please create it from ','service-finder');
						echo '<a href="'.esc_url(service_finder_get_url_by_shortcode('[submit_job_form')).'">here</a>';
					}
				  ?>
                </div>
              </div>
            </div>
            <?php if ( $jobs->have_posts() ) { ?>
            <div class="col-md-12">
              <div class="form-group">
                <input type="hidden" name="provider_id" value="<?php echo esc_html($author); ?>">
                <input type="submit" class="btn btn-primary btn-block" name="submit" value="<?php esc_html_e('Send Invitation', 'service-finder'); ?>" />
              </div>
            </div>
            <?php } ?>
          </form>
        </div>
      </div>
    </div>

  </div>
</div>