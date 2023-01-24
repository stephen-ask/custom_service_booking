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
$service_finder_options = get_option('service_finder_options');
$savedlanguages = get_option( 'sf_languages');
?>
<div class="sf-wpbody-inr">
  <div class="sedate-title">
    <h2>
      <?php esc_html_e( 'Languages', 'service-finder' ); ?>
    </h2>
  </div>
  <div class="all-languages">
  <form method="post" class="update-languages">
      <ul class="sf-all-languages">
		<?php
        $languages = service_finder_get_alllanguages();
        if(!empty($languages)){
            foreach($languages as $key => $value){
				$checked = '';
				if(!empty($savedlanguages)){
				if(in_array($key,$savedlanguages)){
					$checked = 'checked="checked"';
				}
				}
				echo '<li><input '.esc_attr($checked).' name="languages[]" type="checkbox" class="form-control" value="'.esc_attr($key).'"> <img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/flags/'.$key.'.png'.'" title=""> '.$value.'</li>';
            }
        }
        ?>
        </ul>
      <div class="form-group">
        <input type="submit" class="btn btn-primary" name="add-language" value="<?php esc_html_e('Save', 'service-finder'); ?>" />
      </div>
  </form>
  </div>
</div>
<!-- Loading area start -->
<div class="loading-area default-hidden">
  <div class="loading-box"></div>
  <div class="loading-pic"></div>
</div>
<!-- Loading area end -->
