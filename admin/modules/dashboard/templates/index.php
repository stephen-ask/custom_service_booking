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



<div class="sf-wpbody-inr">

  <?php 

  $totalfeatured = service_finder_getFeaturedProviders(1000);

  $totalfeatured = count($totalfeatured);

  $totalpaidproviders =  service_finder_total_paid_providers();

  $totalusers = count_users();

  $totalproviders = (!empty($totalusers['avail_roles']['Provider'])) ? $totalusers['avail_roles']['Provider'] : 0;
  $totalcustomers = (!empty($totalusers['avail_roles']['Customer'])) ? $totalusers['avail_roles']['Customer'] : 0;

  $total = $totalproviders + $totalcustomers;

  $totalproviders = ( $totalproviders > 0) ?  $totalproviders : 0;

  $totalcustomers = ( $totalcustomers > 0) ?  $totalcustomers : 0;

  ?>

  <ul class="sf-users-stats">

	  <li><div><i class="sl sl-icon-people text-orange"></i> <span><?php echo esc_html__('Total no of user', 'service-finder'); ?></span> <strong><?php echo esc_html($total); ?></strong></div></li>

      <li><div><i class="sl sl-icon-user text-green"></i> <span><?php echo esc_html__('Total no. of providers', 'service-finder'); ?></span> <strong><?php echo esc_html($totalproviders); ?></strong></div></li>

      <li><div><i class="sl sl-icon-user  text-pink"></i> <span><?php echo esc_html__('Total no. of paid providers', 'service-finder'); ?></span> <strong><?php echo esc_html($totalpaidproviders); ?></strong></div></li>

      <li><div><i class="sl sl-icon-user  text-purple"></i> <span><?php echo esc_html__('Total no. of featured providers', 'service-finder'); ?></span> <strong><?php echo esc_html($totalfeatured); ?></strong></div></li>

      <li><div><i class="sl sl-icon-people text-red"></i> <span><?php echo esc_html__('Total no. of customers', 'service-finder'); ?></span> <strong><?php echo esc_html($totalcustomers); ?></strong></div></li>

  </ul>

</div>

