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
/* xml sitemap builder object */
require SERVICE_FINDER_BOOKING_ADMIN_MODULE_DIR . '/xml-sitemap/xmlSitemapBuilder.php';
$xmlSitemap = new SERVICE_FINDER_XmlSitemapBuilder();

?>
<!--Template for dispaly featured requests-->
<div class="sf-wpbody-inr">
  <div class="sedate-title">
    <h2>
      <?php esc_html_e( 'XMl Sitemap settings', 'service-finder' ); ?>
    </h2>
  </div>
</div>
<div class="wrap">
 
    <p><?php esc_html_e('Your two sitemaps are active! Here you can change and customize them.', 'service-finder'); ?></p>
    <p><b><?php esc_html_e('Links to your xml and html sitemap:', 'service-finder'); ?></b></p>

    <ul>
        <li><?php printf('%1$s <a href="%2$s">%2$s</a>', __('Xml sitemap:', 'service-finder'), $xmlSitemap->getSitemapUrl('xml')); ?></li>
        <li>
            <?php esc_html_e('Html sitemap:', 'service-finder'); ?>
            <?php echo get_option('xml_sitemap_block_html') ? __('(disabled)', 'service-finder') : sprintf('<a href="%1$s">%1$s</a>', $xmlSitemap->getSitemapUrl('html')); ?>
        </li>
    </ul>
</div><!-- wrap -->