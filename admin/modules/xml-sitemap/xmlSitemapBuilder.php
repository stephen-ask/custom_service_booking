<?php defined('ABSPATH') or die;

/*
 * XML Sitemap builder
 */

class SERVICE_FINDER_XmlSitemapBuilder {
    private $home = null;
    private $xml = false;
    private $html = false;
    private $posts = '';
    private $pages = '';
    private $other = false;
    private $blockedUrls = null;
    private $tags;
    private $authors;
    private $orderby;
    private $pattern;
    private $homeUrl;
    private $pluginUrl;
    private $categories;
    private $providerscategories;
    private $lastUpdated;

    // Constructor
    public function __construct () {
        $this->homeUrl = esc_url(home_url('/'));
        $this->pluginUrl = esc_url(plugins_url('/sf-booking/') ."admin/modules/xml-sitemap/");
        $this->category = get_option('xml_sitemap_categories') ? array(0 => 0) : true;
        $this->providerscategory = get_option('xml_sitemap_providerscategories') ? array(0 => 1) : true;
        $this->tag = get_option('xml_sitemap_tags') ? array(0 => 0) : true;
        $this->author = get_option('xml_sitemap_authors') ? array(0 => 0) : true;
        $this->orderby = get_option('xml_sitemap_order_by');
        @date_default_timezone_set(get_option('timezone_string'));
    }
    
    
    

    // Generates an xml or html sitemap
    public function service_finder_generate_xml_sitemap ($type) {
        if ($type === 'xml' || $type === 'html') {
            $this->$type = true;
            $this->pattern = $this->xml ? 'Y-m-d\TH:i:sP' : 'Y-m-d H:i';
            $this->service_finder_get_pages();
            $this->service_finder_set_blocked_urls();
            $this->service_finder_set_last_updated();
            $this->service_finder_generate_content();
            $this->service_finder_print_output();
        }
    }

  // Returns a sitemap url
    public function service_finder_get_xml_sitemap_url ($type) {
        return esc_url(home_url('/') . (get_option('permalink_structure') ? 'sitemap.' : '?xmlsitemap=') . $type);
    }

    // Returns default order option
    public function service_finder_get_default_order () {
    	 return array('home' => array('title' => __('Home', 'service-finder'), 'i' => 1), 'posts' => array('title' => __('Posts', 'service-finder'), 'i' => 2), 'pages' => array('title' => __('Pages', 'service-finder'), 'i' => 3), 'other' => array('title' => __('Other', 'service-finder'), 'i' => 4), 'categories' => array('title' => __('Categories', 'service-finder'), 'i' => 5), 'tags' => array('title' => __('Tags', 'service-finder'), 'i' => 6), 'authors' => array('title' => __('Authors', 'service-finder'), 'i' => 7), 'providerscategories' => array('title' => __('Providers Category', 'service-finder'), 'i'=>8));
    }

    // Updates all options
    public function service_finder_set_options ($otherUrls, $blockUrls, $attrLink, $providerscategories, $categories, $tags, $authors, $orderArray, $lastUpdated, $blockHtml, $orderby, $title) {
        @date_default_timezone_set(get_option('timezone_string'));
        update_option('xml_sitemap_other_urls', $this->service_finder_add_urls($otherUrls, get_option('xml_sitemap_other_urls')));
        update_option('xml_sitemap_block_urls', $this->service_finder_add_urls($blockUrls));
        update_option('xml_sitemap_attr_link', intval($attrLink));
        update_option('xml_sitemap_categories', intval($categories));
        update_option('xml_sitemap_providerscategories', intval($providerscategories));
        update_option('xml_sitemap_tags', intval($tags));
        update_option('xml_sitemap_authors', intval($authors));
        update_option('xml_sitemap_block_html', sanitize_text_field($blockHtml));
        update_option('xml_sitemap_last_updated', sanitize_text_field(stripslashes($lastUpdated)));
        update_option('xml_sitemap_order_by', sanitize_text_field($orderby));
        update_option('xml_sitemap_title', sanitize_text_field(stripslashes($title)));

        if (($orderArray = $this->service_finder_check_order($orderArray)) && uasort($orderArray, array($this, 'service_finder_sort_array'))) { // sort the array here
            update_option('xml_sitemap_order', $orderArray);
        }
    }


    // Adds new urls to add and block pages
    public function service_finder_add_urls ($urls, $oldUrls = null) {
        $newUrls = array();

        if ($urls = explode("\n", trim($urls))) {
            foreach ($urls as $url){
                if ($url = esc_url(trim($url))) {
                    $isOld = false;
                    if ($oldUrls && is_array($oldUrls)) {
                        foreach ($oldUrls as $oldUrl) {
                            if ($oldUrl['url'] === $url && !$isOld) {
                                array_push($newUrls, $oldUrl);
                                $isOld = true;
                            }
                        }
                    }
                    if (!$isOld && strlen($url) < 2000) {
                        array_push($newUrls, array('url' => $url, 'date' => time()));
                    }
                }
            }
        }
        return $newUrls;
    }

    // Checks if orderArray is valid
    public function service_finder_check_order ($orderArray) {
        if (is_array($orderArray)) {
            foreach ($orderArray as $key => $val) {
                if (!is_array($val) || !preg_match('/^[1-7]{1}$/', $val['i']) || (!($orderArray[$key]['title'] = sanitize_text_field(stripslashes($val['title']))))) {
                    return false;
                }
            }
            return $orderArray;
        }
        return false;
    }

    // Sort function for order option
    public function service_finder_sort_array ($a, $b) {
        return $a['i'] - $b['i'];
    }

    // Deletes old or current sitemap files and fixes order option for older plugin versions
    public function service_finder_migrate_from() {
        if ($order = get_option('xml_sitemap_order')) {
            foreach ($order as $key => $val) {
                if (!is_array($val)) {
                    unset($order[$key]);
                    $order[lcfirst($key)] = array('title' => $key, 'i' => $val);
                }
            }
        } else {
            $order = $this->service_finder_get_default_order();
        }
        update_option('xml_sitemap_order', $order);
        return $order;
    }

    // Gets custom urls
    public function service_finder_get_pages () {
        if ($others = get_option('xml_sitemap_other_urls')) {
            if ($this->orderby === 'modified') {
                uasort($others, array($this, 'sortDate'));
            }
            $xml = array();
            foreach ($others as $other) {
                if ($other && is_array($other)) {
                    $xml[] = $this->service_finder_get_xml(esc_url($other['url']), date($this->pattern, is_int($other['date']) ? $other['date'] : strtotime($other['date'])));
                }
            }
            $this->other = $this->service_finder_sortToString($xml);
        }
    }

    // Sets up blocked urls into an array
    public function service_finder_set_blocked_urls () {
        if (($blocked = get_option('xml_sitemap_block_urls')) && is_array($blocked)) {
            $this->blockedUrls = array();
            foreach ($blocked as $block) {
                $this->blockedUrls[$block['url']] = true;
            }
        }
    }

    // Sets the "last updated" text
    public function service_finder_set_last_updated () {
        $this->lastUpdated = esc_html(($updated = get_option('xml_sitemap_last_updated')) ? $updated : __('Last updated', 'service-finder'));
    }

    // Checks if blocked url that shouldn't be displayed
    public function service_finder_isBlockedUrl($url) {
        return $this->blockedUrls && isset($this->blockedUrls[$url]);
    }

    // Returns xml or html
    public function service_finder_get_xml ($url, $date) {
        return $this->xml ? "<url>\n\t<loc>$url</loc>\n\t<lastmod>$date</lastmod>\n</url>\n" : "<li><a href=\"$url\"><span class=\"link\">$url</span><span class=\"date\">$date</span></a></li>";
    }

    // Generates the sitemaps content
    public function service_finder_generate_content () {
        $query = new WP_Query(array(
            'post_type' => 'any',
            'post_status' => 'publish',
            'posts_per_page' => 50000, // limit 50k posts
            'has_password' => false,
            'orderby' => $this->orderby ? ($this->orderby === 'parent' ? array('type' => 'DESC', 'parent' => 'DESC') : sanitize_text_field($this->orderby)) : 'date',
            'order' => $this->orderby === 'name' ? 'ASC' : 'DESC'
        ));
      
        while ($query->have_posts()) {
            $query->the_post();

            $url = esc_url(get_permalink());
            $date = get_the_modified_date($this->pattern);
            $this->service_finder_get_categories_providers_author($date);
			if (!$this->service_finder_isBlockedUrl($url)) {
                if (!$this->home && $url === $this->homeUrl) {
                    $this->home = $this->service_finder_get_xml($url, $date);

                } elseif (get_post_type() === 'page') {
                    $this->pages .= $this->service_finder_get_xml($url, $date);

                } else { // posts (also all custom post types are added here)
                	if (get_post_type() != 'sf_comment_rating') {
                    	$this->posts .= $this->service_finder_get_xml($url, $date);
                    }
                }
            }
        }
        wp_reset_postdata();
    }

     // Gets a posts Providers Categories, categories, tags and author, and compares for last modified date
    public function service_finder_get_categories_providers_author ($date) {
        if ($this->category && ($categories = get_the_category())) {
            foreach ($categories as $category) {
                if (!isset($this->categories[($id = $category->term_id)]) || $this->categories[$id] < $date) {
                    $this->categories[$id] = $date;
                }
            }
        }
        if ($this->providerscategory && ($providescategories = service_finder_getCategoryList("", true))) {
            
            foreach ($providescategories as $providercategory) {
                if (!isset($this->providerscategories[($id = $providercategory->term_id)]) || $this->providerscategories[$id] < $date) {
                    $this->providerscategories[$id] = $date;
                }
            }
        }
        if ($this->tag && ($tags = get_the_tags())) {
            foreach ($tags as $tag) {
              if (!isset($this->tags[$id]) || $this->tags[$id] < $date) {
                    $this->tags[$id] = $date;
                }
            }
        }
        
        $args = array('role' => 'Provider'); 
		
   		if ($this->author && ($authors = get_users( array( 'fields' => array( 'ID' ) ) ))) {
   			foreach ($authors as $author) {
   				 if (!isset($this->authors[$id = $author->ID]) || $this->authors[$id] < $date) {
					 $this->authors[$id] = $date;
				 }
			}	 
        } 
    }
    // Prints sitemap xml or html output
    public function service_finder_print_output () {
        if ($this->xml) {
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<?xml-stylesheet type=\"text/css\" href=\"", $this->pluginUrl, "css/xml.css\"?>\n<urlset xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" >\n";
            $this->service_finder_sort_print_sections();
            echo '</urlset>';

        } else {
            $title = esc_html(get_option('xml_sitemap_title'));
            echo '<!doctype html><html lang="', get_locale(), '"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>', $title ? $title : esc_html(get_bloginfo('name') . ' ' . __('Html Sitemap', 'service-finder')), '</title><link rel="stylesheet" href="', $this->pluginUrl, 'css/html.css"></head><body><div id="wrapper"><h1>', $title ? $title : sprintf('<a href="%s">%s</a> %s', $this->homeUrl, esc_html(get_bloginfo('name')), esc_html__('Html Sitemap', 'service-finder')), '</h1>';
            $this->service_finder_sort_print_sections();
            echo '</div></body></html>';
        }
    }

    // Prints sections after sort order
    public function service_finder_sort_print_sections () {
        $orderArray = get_option('xml_sitemap_order');

        if (!$orderArray || !isset($orderArray['home'])) { // Fix for old versions
            
            $orderArray = $this->service_finder_migrate_from();
        }
        if (!$this->home) {
            $this->home = $this->service_finder_get_xml($this->homeUrl, date($this->pattern));
        }
        array_walk($orderArray, array($this, 'service_finder_print_section'));
    }

    // Prints a sections xml/html
    public function service_finder_print_section ($arr, $type) {
        if ($this->$type) {
            $xml = $this->$type;
            unset($this->$type);
            if (in_array($type, array('providerscategories','categories', 'tags', 'authors'))) { // Providers Categories, Categories, tags or authors
                $urls = array();
                foreach ($xml as $id => $date) {
                    if ($date) {
                         $url = esc_url($type === 'tags' ? get_tag_link($id) : $type === 'providerscategories' ? service_finder_getCategoryLink($id) : ($type === 'categories' ? get_category_link($id) : service_finder_get_author_url($id)));
                       if (!$this->service_finder_isBlockedUrl($url)) {
                            $urls[] = $this->service_finder_get_xml($url, $date);
                        }
                    }
                }
                $xml = $this->service_finder_sortToString($urls);
            }
            if ($xml) {
                if ($this->html) {
                    echo '<div class="fileds"><div class="header"><p class="header-txt">', esc_html($arr['title']), '</p><p class="header-date">', $this->lastUpdated, '</p></div><ul class="target">', $xml, '</ul></div>';
                } else {
                    //echo'<div class="fileds"><div class="header"><p class="header-txt">', esc_html($arr['title']), '</p></div><ul class="xmltarget">', $xml, '</ul></div>';
					print($xml);
                }
            }
        }
    }

    // Sorts or shuffles array and returns as string
    public function service_finder_sortToString ($urls) {
        switch ($this->orderby) {
            case 'name': natcasesort($urls); break;
            case 'rand': shuffle($urls); break;
        }
        return implode('', $urls);
    }

    // Sort function for last modified date
    public function sortDate ($a, $b) {
        return $b['date'] - $a['date'];
    }
}
