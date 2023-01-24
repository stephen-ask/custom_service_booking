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

/**
 * Class SERVICE_FINDER_sedateFeatured
 */
class SERVICE_FINDER_xmlSitemap extends SERVICE_FINDER_sedateManager{


	private static $version = '1.0.1'; // only changes when needed
	
	/*Actions for wp ajax call*/
	protected function service_finder_registerWpActions() {
     	$_this = $this;
	}
	
	/*Initial Function*/
	public function service_finder_index()
    {
    	/*Action for wp ajax call*/
    	$this->service_finder_registerWpActions();
        $this->service_finder_render( 'index','xml-sitemap' );
   	
    }
    
    // Runs on plugin activation
    public static function service_finder_activate_sitemaps () {
        self::service_finder_rewrite_rules();
        /* xml sitemap builder object */
		require SERVICE_FINDER_BOOKING_ADMIN_MODULE_DIR . '/xml-sitemap/xmlSitemapBuilder.php';
		$xmlSitemap = new SERVICE_FINDER_XmlSitemapBuilder();
		$xmlSitemap->service_finder_migrate_from();
        update_option('xml_sitemap_version', self::$version);
    }

    // Runs on plugin deactivation
    public static function service_finder_deactivate_sitemaps () {
        service_finder_flush_rewrite_rules();
    }

    // Registers most hooks
    public static function registerHooks () {
       // register_activation_hook(__FILE__, array(__CLASS__, 'service_finder_activate_sitemaps'));
      //  register_deactivation_hook(__FILE__, array(__CLASS__, 'service_finder_deactivate_sitemaps'));
        add_action('init', array(__CLASS__, 'service_finder_rewrite_rules'), 1);
        add_filter('query_vars', array(__CLASS__, 'service_finder_add_xml_sitemap_query'), 1);
        add_filter('template_redirect', array(__CLASS__, 'service_finder_generate_xml_sitemap_content'), 1);
    }


    // Rewrite rules for sitemaps
    public static function service_finder_rewrite_rules () {
        add_rewrite_rule('sitemap\.xml$', 'index.php?xmlsitemap=xml', 'top');
        add_rewrite_rule('sitemap\.html$', 'index.php?xmlsitemap=html', 'top');
    }

    // Adds custom query
    public static function service_finder_add_xml_sitemap_query ($vars) {
        $vars[] = 'xmlsitemap';
        return $vars;
    }

    // Generates the content if sitemap request
    public static function service_finder_generate_xml_sitemap_content () {
        global $wp_query;

        if (isset($wp_query->query_vars['xmlsitemap']) && in_array(($q = $wp_query->query_vars['xmlsitemap']), array('xml', 'html'))) {
            $wp_query->is_404 = false;

            if ($q === 'html') {
                if ($htmlOpt = get_option('xml_sitemap_block_html')) {
                    if ($htmlOpt === '404') {
                        $wp_query->is_404 = true;
                        status_header(404);
                    }
                    return;
                }
            } else {
                header('Content-type: application/xml; charset=utf-8');
            }
            /* xml sitemap builder object */
			require SERVICE_FINDER_BOOKING_ADMIN_MODULE_DIR . '/xml-sitemap/xmlSitemapBuilder.php';
			$xmlSitemap = new SERVICE_FINDER_XmlSitemapBuilder();
            $xmlSitemap->service_finder_generate_xml_sitemap($q);
            exit;
        }
    }
}
SERVICE_FINDER_xmlSitemap::registerHooks();