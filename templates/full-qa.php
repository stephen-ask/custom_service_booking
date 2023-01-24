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
?>
<div class="container">
  <div class="section-content provider-content">
  	<div class="row">
	  <div class="col-md-12">
	  <?php 
      do_action('service_finder_question_answer',$author); 
      ?>
	  </div>	
      <!-- Right part start -->
	</div>	                            
  </div>
</div>
<?php get_footer(); ?>

