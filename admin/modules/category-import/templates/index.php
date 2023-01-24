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


<div class="sf-wpbody-inr">
  <div class="sedate-title">
    <h2>
      <?php esc_html_e( 'Import Provider Category to Jobs Category', 'service-finder' ); ?>
    </h2>
  </div>
  <div class="table-responsive">
   <div class="wrap">	
		<div class="provider-import-wrap">
			<a href="javascript:;" class="button-primary import-jobcategory sf-job-import" data-type="job"> <?php esc_html_e( 'Start Importing Provider Category to Job Category', 'service-finder' ); ?></a>
            <a href="javascript:;" class="button-primary import-jobcategory sf-question-import" data-type="question"> <?php esc_html_e( 'Start Importing Provider Category to Questions Category', 'service-finder' ); ?></a>
            <a href="javascript:;" class="button-primary import-jobcategory sf-article-import" data-type="article"> <?php esc_html_e( 'Start Importing Provider Category to Articles Category', 'service-finder' ); ?></a>
            <div class="provider-form-overlay" style="display:none"></div>
            <div class="provider-loading-image" style="display:none"><img src="<?php echo esc_url(SERVICE_FINDER_BOOKING_IMAGE_URL.'/load.gif'); ?>"></div>
		</div>

	</div>
  </div>
</div>