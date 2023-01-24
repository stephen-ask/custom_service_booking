<?php 
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<!--Template for dispaly featured requests-->

<?php
wp_enqueue_style('bootstrap-toggle');
wp_enqueue_script('bootstrap-toggle');
?>
<div class="sf-wpbody-inr">
  <div class="sedate-title">
    <h2>
      <?php esc_html_e( 'Important things for existing buyers', 'service-finder' ); ?>
    </h2>
  </div>
  <div class="table-responsive">

   <div class="wrap">	

   		<div id="success"></div>

		<div class="provider-import-wrap">

		<form method="post" class="existing-buyers1">

			<table class="form-table">

				<tbody>

				<tr class="form-field form-required">

					<th scope="row"><div class="radio sf-radio-checkbox">
              <input id="manageshortcodes" <?php echo (service_finder_manage_shortcode() == 'yes') ? 'checked="checked"' : ''; ?> type="checkbox" name="manageshortcodes" value="yes" data-toggle="toggle" data-on="<?php esc_html_e('Yes', 'service-finder'); ?>" data-off="<?php esc_html_e('No', 'service-finder'); ?>">
              <label for="provider_location">
              
              </label>
            </div></th>

					<td>

                    	<label><?php esc_html_e('Manage Home Page Shortcodes from Themeoption', 'service-finder'); ?></label>

					</td>

				</tr>



			</tbody>

			</table>

			<span class="sf-importnote"><label><?php esc_html_e( 'Note:', 'service-finder' ); ?></label> <?php esc_html_e( 'Your csv must have same number of columns in same order as given in our sample csv.', 'service-finder' ); ?></span>
			
			</form>

            <div class="provider-form-overlay" style="display:none"></div>

            <div class="provider-loading-image" style="display:none"><img src="<?php echo esc_url(SERVICE_FINDER_BOOKING_IMAGE_URL.'/load.gif'); ?>"></div>

		</div>

	</div>

  </div>
  
  <div class="table-responsive">

   <div class="wrap">	

   		<div id="success"></div>

		<div class="provider-import-wrap">

		<form method="post" class="existing-buyers2">

			<table class="form-table">

				<tbody>

				<tr class="form-field form-required">

					<th scope="row"><a href="javascript:;" class="btn btn-primary updatecitytaxonomy"><?php esc_html_e('Create Cities', 'service-finder'); ?></a></th>

					<td>

                    	<label><?php esc_html_e('Create City Taxonomy from Existing Users', 'service-finder'); ?></label>

					</td>

				</tr>



			</tbody>

			</table>

			<span class="sf-importnote"><label><?php esc_html_e( 'Note:', 'service-finder' ); ?></label> <?php esc_html_e( 'Your csv must have same number of columns in same order as given in our sample csv.', 'service-finder' ); ?></span>
			
			</form>

            <div class="provider-form-overlay" style="display:none"></div>

            <div class="provider-loading-image" style="display:none"><img src="<?php echo esc_url(SERVICE_FINDER_BOOKING_IMAGE_URL.'/load.gif'); ?>"></div>

		</div>

	</div>

  </div>
</div>