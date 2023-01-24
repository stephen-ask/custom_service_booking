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

class SERVICE_FINDER_sedateAdmin {

	/* Call the construct when class initialize */    
	public function __construct() {
        add_action( 'admin_menu', array( $this, 'service_finder_addAdminMenu' ) );

        // Admin Manager
        $this->dashboard       	   	   = new SERVICE_FINDER_dashboard();
		$this->bookingManager     	   = new SERVICE_FINDER_sedateBookingManager();
		$this->bookingCalendar     	   = new SERVICE_FINDER_sedateBookingCalendar();
		$this->providers     	   	   = new SERVICE_FINDER_sedateProviders();
		$this->customers     	   	   = new SERVICE_FINDER_sedateCustomers();
		$this->featured     	   	   = new SERVICE_FINDER_sedateFeatured();
		$this->invoice     	   	   	   = new SERVICE_FINDER_sedateInvoice();
		$this->quotations     	   	   = new SERVICE_FINDER_sedateQuotations();
		$this->payout     	   	   	   = new SERVICE_FINDER_PAYOUT_HISTORY();
		$this->claimbusiness     	   = new SERVICE_FINDER_sedateClaimBusiness();
		$this->ratinglabels     	   = new SERVICE_FINDER_ratingLabels();
		$this->upgraderequest     	   = new SERVICE_FINDER_upgradeRequest();
		$this->jobconnectrequest       = new SERVICE_FINDER_jobconnectRequest();
		$this->notifications	       = new SERVICE_FINDER_notifications();
		$this->providerimport          = new SERVICE_FINDER_providerImport();
		$this->xmlsitemap       	   = new SERVICE_FINDER_xmlSitemap();
		$this->catimport       	   	   = new SERVICE_FINDER_CATEGORY_IMPORT();
		$this->walletrequest       	   = new SERVICE_FINDER_WALLET_REQUEST();
		$this->languages       	   	   = new SERVICE_FINDER_LANGUAGES();
		$this->existingbuyers          = new SERVICE_FINDER_EXISTING_BUYERS();
		
    }

    /*Add menus for admin panel*/
	public function service_finder_addAdminMenu() {
        global $wpdb, $current_user;
		$service_finder_options = get_option('service_finder_options');
		$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');
		$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customers', 'service-finder');	
		$claimbusinessstr = (!empty($service_finder_options['string-claim-business'])) ? $service_finder_options['string-claim-business'] : esc_html__('Claim Business Requests', 'service-finder');	
		
		$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';

        // For Menu Translatins
        $dashboard       = esc_html__( 'Dashboard', 'service-finder' );
		$bookings       = esc_html__( 'Bookings', 'service-finder' );
		$bookingCalendar       = esc_html__( 'Calendar View', 'service-finder' );
		$bookingProviders       = esc_html( $providerreplacestring );
		$providersIdentity       = esc_html( $providerreplacestring ).' '.esc_html__( 'Identity Check', 'service-finder' );
		$bookingCustomers       =  esc_html( $customerreplacestring );
		$featuredRequests       = esc_html__( 'Featured Requests', 'service-finder' );
		$invoices       = esc_html__( 'Invoices', 'service-finder' );
		$cities       = esc_html__( 'Cities', 'service-finder' );
		$quotations       = esc_html__( 'Quotations', 'service-finder' );
		$payout       = esc_html__( 'Stripe Payout History', 'service-finder' );
		$masspaypayout       = esc_html__( 'Masspay Payout History', 'service-finder' );
		$claimbusiness       = $claimbusinessstr;
		$ratinglabels       = esc_html__( 'Rating Labels', 'service-finder' );
		$upgraderequest       = esc_html__( 'Upgrade Package Request', 'service-finder' );
		$jobconnectrequest       = esc_html__( 'Job Connect Request', 'service-finder' );
		$notifications       = esc_html__( 'Notifications', 'service-finder' );
		$providerimport       = esc_html__( 'Provider/Category Import', 'service-finder' );
		$xmlsitemap       = esc_html__( 'XML Sitemap', 'service-finder' );
		$catimport       = esc_html__( 'Category Import', 'service-finder' );
		$walletrequest       = esc_html__( 'Wallet Request', 'service-finder' );
		$languages       = esc_html__( 'Languages', 'service-finder' );
		$existingbuyers       = esc_html__( 'Important Things for Existing Buyers', 'service-finder' );

        if ( in_array( 'administrator', $current_user->roles ) || current_user_can( 'editor' ) || current_user_can( 'author' ) || current_user_can( 'contributor' ) ) {
            if ( function_exists( 'add_options_page' ) ) {
                $position = '80.0000001' . mt_rand( 1, 1000 ); // Position always is under `Settings`
				
				add_menu_page( esc_html__( 'Service Finder', 'service-finder' ), esc_html__( 'Service Finder', 'service-finder' ), 'manage_options', 'service-finder', '','', $position );
                add_submenu_page( 'service-finder', $dashboard, $dashboard, 'manage_options', 'dashboard',array( $this->dashboard, 'service_finder_index' ) );
				add_submenu_page( 'service-finder', $bookings, $bookings, 'manage_options', 'bookings',array( $this->bookingManager, 'service_finder_index' ) );
				add_submenu_page( 'service-finder', $bookingCalendar, $bookingCalendar, 'manage_options', 'booking-calendar',array( $this->bookingCalendar, 'service_finder_index' ) );
				add_submenu_page( 'service-finder', $bookingProviders, $bookingProviders, 'manage_options', 'providers',array( $this->providers, 'service_finder_index' ) );
				if($identitycheck){
				add_submenu_page( 'service-finder', $bookingProviders, $providersIdentity, 'manage_options', 'identity-check',array( $this->providers, 'service_finder_identitycheck' ) );
				}
				add_submenu_page( 'service-finder', $bookingCustomers, $bookingCustomers, 'manage_options', 'customers',array( $this->customers, 'service_finder_index' ) );
				add_submenu_page( 'service-finder', $languages, $languages, 'manage_options', 'languages',array( $this->languages, 'service_finder_index' ) );
				add_submenu_page( 'service-finder', $featuredRequests, $featuredRequests, 'manage_options', 'featured-requests',array( $this->featured, 'service_finder_index' ) );
				add_submenu_page( 'service-finder', $invoices, $invoices, 'manage_options', 'invoices',array( $this->invoice, 'service_finder_index' ) );
				
				add_submenu_page( 'service-finder', $quotations, $quotations, 'manage_options', 'quotations',array( $this->quotations, 'service_finder_index' ) );
				add_submenu_page( 'service-finder', $payout, $payout, 'manage_options', 'stripe-payout-history',array( $this->payout, 'service_finder_index' ) );
				add_submenu_page( 'service-finder', $masspaypayout, $masspaypayout, 'manage_options', 'masspay-payout-history',array( $this->payout, 'service_finder_masspay' ) );
				add_submenu_page( 'service-finder', $claimbusiness, $claimbusiness, 'manage_options', 'claimbusiness',array( $this->claimbusiness, 'service_finder_index' ) );
				add_submenu_page( 'service-finder', $ratinglabels, $ratinglabels, 'manage_options', 'ratinglabels',array( $this->ratinglabels, 'service_finder_index' ) );
				add_submenu_page( 'service-finder', $upgraderequest, $upgraderequest, 'manage_options', 'upgraderequest',array( $this->upgraderequest, 'service_finder_index' ) );
				add_submenu_page( 'service-finder', $jobconnectrequest, $jobconnectrequest, 'manage_options', 'jobconnectrequest',array( $this->jobconnectrequest, 'service_finder_index' ) );
				add_submenu_page( 'service-finder', $walletrequest, $walletrequest, 'manage_options', 'wallet-wired-request',array( $this->walletrequest, 'service_finder_index' ) );
				add_submenu_page( 'service-finder', $notifications, $notifications, 'manage_options', 'notifications',array( $this->notifications, 'service_finder_index' ) );
				add_submenu_page( 'service-finder', $providerimport, $providerimport, 'manage_options', 'providerimport',array( $this->providerimport, 'service_finder_index' ) );
				add_submenu_page( 'service-finder', $catimport, $catimport, 'manage_options', 'job-category-import',array( $this->catimport, 'service_finder_index' ) );
				add_submenu_page( 'service-finder', $xmlsitemap, $xmlsitemap, 'manage_options', 'xmlsitemap',array( $this->xmlsitemap, 'service_finder_index' ) );
				
				add_submenu_page( 'service-finder', $existingbuyers, $existingbuyers, 'manage_options', 'existingbuyers',array( $this->existingbuyers, 'service_finder_index' ) );
				
				global $submenu;
                unset( $submenu[ 'service-finder' ][ 0 ] );

            }
        }
    }

} 