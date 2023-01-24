<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

/*Recent post with thumbnails*/
class service_finder_recent_post_with_thumbnails extends WP_Widget {

function __construct() {
	parent::__construct(
			'recent_post_with_thumbnails',
			esc_html__('Recent Posts with Thumbnail', 'service-finder' ),
			array( 'description' => esc_html__('Display Recent Posts with Thumbnail', 'service-finder' ), ) // Args
		);
}

  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>

<p>
  <label for="<?php echo esc_html($this->get_field_id('title')); ?>">Title:
  <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo  esc_attr($title); ?>" />
  </label>
</p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
	$allowedhtml1 = array(
			'div' => array(
				'class' => array(),
				'id' => array()
			),
		);
	$allowedhtml2 = array(
			'h4' => array(
				'class' => array(),
			),
		);	
    echo wp_kses($before_widget,$allowedhtml1);
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
	if ($title != ' '){
      echo wp_kses($before_title,$allowedhtml2) . esc_html($title) . wp_kses($after_title,$allowedhtml2);
    } 
 
 	  if($args['id'] == 'sf-sidebar-footer-1' || $args['id'] == 'sf-sidebar-footer-2' || $args['id'] == 'sf-sidebar-footer-3' || $args['id'] == 'sf-sidebar-footer-4'){
	  	$arg = array( 'numberposts' => '2','post_type'=> 'post','post_status' => 'publish' );
	  }else{
	  	$arg = array( 'numberposts' => '4','post_type'=> 'post','post_status' => 'publish' );
	  }
	  
	  $recent_posts = wp_get_recent_posts( $arg );
	  if(!empty($recent_posts)){
	  echo '<ul>';
	  foreach( $recent_posts as $recent ){
	  $imgsrc = wp_get_attachment_image_src( get_post_thumbnail_id($recent["ID"]), 'service_finder-small-thumb' );
	  ?>
<?php
                                                    if ( has_post_thumbnail() ) { 
													$imgsrc = wp_get_attachment_image_src( get_post_thumbnail_id($recent["ID"]), 'service_finder-small-thumb' );
													}
													?>
<li class="<?php echo sanitize_html_class(($imgsrc[0] == "") ? 'no-img-post' : ''); ?>">
  <div class="post-thum-bx">
    <?php if($imgsrc[0] != ""): ?>
    <img width="73" height="73" src="<?php echo esc_url($imgsrc[0]); ?>">
    <?php endif; ?>
  </div>
  <div class="post-text-bx">
    <h6 class="post-title"><a href="<?php echo esc_url(get_permalink($recent["ID"])); ?>"><?php echo esc_html($recent["post_title"]); ?></a></h6>
    <?php 
													if($args['id'] == 'sf-sidebar-footer-1' || $args['id'] == 'sf-sidebar-footer-2' || $args['id'] == 'sf-sidebar-footer-3' || $args['id'] == 'sf-sidebar-footer-4'){ ?>
    <p>
      <?php 
													if($recent["post_excerpt"] != ""){
														if ( function_exists( 'service_finder_getExcerpts' ) ){
														echo service_finder_getExcerpts($recent["post_excerpt"],0,40);
														}else{
														echo substr($recent["post_excerpt"],0,40);
														}
													}else{
														if ( function_exists( 'service_finder_getExcerpts' ) ){
														echo service_finder_getExcerpts($recent["post_content"],0,40);
														}else{
														echo substr($recent["post_content"],0,40);
														}
													}
													 ?>
    </p>
    <?php } ?>
    <div class="post-meta"> 
    <span class="post-date"><i class="fa fa-calendar-o"></i>
    <a href="<?php echo esc_url(get_permalink($recent["ID"])); ?>">
    <?php echo date('d',strtotime(esc_html($recent["post_date"]))); ?>
    <?php echo service_finder_translate_monthname(date('n',strtotime(esc_html($recent["post_date"])))); ?>
    <?php echo date('Y',strtotime(esc_html($recent["post_date"]))); ?>
    </a>
    </span>
    <span class="post-date">
    <i class="fa fa-comments"></i><?php echo '<a href="'.esc_url(get_comments_link($recent["ID"])).'">'.esc_html($recent["comment_count"]).'</a>'; ?>
    </span> </div>
  </div>
</li>
<?php
      } 
	  echo '</ul>';
	  }
 	
	echo wp_kses($after_widget,$allowedhtml1);
  }


}

/*Custom Search Widget*/
class service_finder_custom_search extends WP_Widget {

function __construct() {
	parent::__construct(
			'custom_search',
			esc_html__('Custom Search', 'service-finder' ),
			array( 'description' => esc_html__('A search form for your site.', 'service-finder' ), ) // Args
		);
}

  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = esc_html($instance['title']);
?>
<p>
  <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:
  <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </label>
</p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    extract($args, EXTR_SKIP);
	$allowedhtml1 = array(
			'div' => array(
				'class' => array(),
				'id' => array()
			),
		);
	$allowedhtml2 = array(
			'h4' => array(
				'class' => array(),
			),
		);	
    echo wp_kses($before_widget,$allowedhtml1);
	
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if ($title != ' '){
      echo wp_kses($before_title,$allowedhtml2) . esc_html($title) . wp_kses($after_title,$allowedhtml2);
	 } 
 
	  ?>
<div class="search-bx">
  <form action="<?php echo home_url('/') ?>" class="searchform" id="searchform" method="get" role="search">
    <div class="input-group">
      <input type="text" placeholder="Write your text" class="form-control" name="s" id="s">
      <span class="input-group-btn">
      <button class="btn btn-border" type="submit"><i class="fa fa-search"></i></button>
      </span> </div>
  </form>
</div>
<?Php
 	
    echo wp_kses($after_widget,$allowedhtml1);
  }


}

/*Booking Search Form*/
class service_finder_provider_search_form extends WP_Widget {

function __construct() {
	parent::__construct(
			'provider_search_form',
			esc_html__('Provider Search Form', 'service-finder' ),
			array( 'description' => esc_html__('A provider search form for customers.', 'service-finder' ), ) // Args
		);
}

  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = esc_html($instance['title']);
?>
<p>
  <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:
  <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </label>
</p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    extract($args, EXTR_SKIP);
	$allowedhtml1 = array(
			'div' => array(
				'class' => array(),
				'id' => array()
			),
		);
	$allowedhtml2 = array(
			'h4' => array(
				'class' => array(),
			),
		);	
    echo wp_kses($before_widget,$allowedhtml1);
	
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if ($title != ' '){
      echo wp_kses($before_title,$allowedhtml2) . esc_html($title) . wp_kses($after_title,$allowedhtml2);
	 } 
 
	require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . 'search-form.php';
	
	print_r($html);
 	
    echo wp_kses($after_widget,$allowedhtml1);
  }


}

/*Request a quote Providers*/
class service_finder_request_a_quote extends WP_Widget {

function __construct() {
	parent::__construct(
			'service_finder_request_a_quote',
			esc_html__('Request a Quote', 'service-finder' ),
			array( 'description' => esc_html__('Request a quote', 'service-finder' ), ) // Args
		);
}

function form($instance)
{
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>
<p>
  <label for="<?php echo esc_html($this->get_field_id('title')); ?>">
  <?php esc_html_e('Title', 'service-finder'); ?>
  :
  <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo  esc_attr($title); ?>" />
  </label>
</p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
  	global $service_finder_Params;
  	if(service_finder_request_quote_for_loggedin_user()){
    extract($args, EXTR_SKIP);
	$allowedhtml1 = array(
			'div' => array(
				'class' => array(),
				'id' => array()
			),
		);
	$allowedhtml2 = array(
			'h4' => array(
				'class' => array(),
			),
		);	
    echo wp_kses($before_widget,$allowedhtml1);
	$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');
    $title = empty($instance['title']) ? esc_html__('Contact', 'service-finder').' '.$providerreplacestring : apply_filters('widget_title', $instance['title']);
 
	if ($title != ' '){
      echo wp_kses($before_title,$allowedhtml2) . esc_html($title) . wp_kses($after_title,$allowedhtml2);
    } 
 	
	?>
<div class="padding-20  margin-b-30  bg-white sf-rouned-box">
  <div class="form-quot-bx" id="form-quot-bx">
    <?php require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/get-quote/templates/get-quote.php'; ?>
  </div>
</div>
<?php      
	echo wp_kses($after_widget,$allowedhtml1);
  }
  }


}

/*Login Form*/
class service_finder_login_form extends WP_Widget {

function __construct() {
	parent::__construct(
			'service_finder_login_form',
			esc_html__('Login Form', 'service-finder' ),
			array( 'description' => esc_html__('Login form', 'service-finder' ), ) // Args
		);
}

function form($instance)
{
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>
<p>
  <label for="<?php echo esc_html($this->get_field_id('title')); ?>">
  <?php esc_html_e('Title', 'service-finder'); ?>
  :
  <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo  esc_attr($title); ?>" />
  </label>
</p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
  	global $service_finder_Params;
  	if(!is_user_logged_in()){
    extract($args, EXTR_SKIP);
	$allowedhtml1 = array(
			'div' => array(
				'class' => array(),
				'id' => array()
			),
		);
	$allowedhtml2 = array(
			'h4' => array(
				'class' => array(),
			),
		);	
    echo wp_kses($before_widget,$allowedhtml1);
	$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');
    $title = empty($instance['title']) ? esc_html__('User Login', 'service-finder') : apply_filters('widget_title', $instance['title']);
 
	if ($title != ' '){
      echo wp_kses($before_title,$allowedhtml2) . esc_html($title) . wp_kses($after_title,$allowedhtml2);
    } 
 	
	?>
<div class="padding-20  margin-b-30  bg-white sf-rouned-box">
  <div class="form-user-login">
    <form class="loginform" method="post">
      <div class="form-group">
        <label>
        <?php esc_html_e('User Name', 'service-finder'); ?>
        </label>
        <input name="login_user_name" type="text" class="form-control">
      </div>
      <div class="form-group">
        <label>
        <?php esc_html_e('Password', 'service-finder'); ?>
        </label>
        <input name="login_password" type="password" class="form-control">
      </div>
      <div class="form-group">
        <div class="checkbox sf-radio-checkbox">
          <input type="checkbox" id="remember-me">
          <label for="remember-me">
          <?php esc_html_e('Remember me', 'service-finder'); ?>
          </label>
        </div>
      </div>
      <div class="form-group">
        <input type="submit" class="btn btn-primary btn-block" name="user-login" value="<?php esc_html_e('Log in', 'service-finder'); ?>" />
      </div>
    </form>
  </div>
</div>
<?php      
	echo wp_kses($after_widget,$allowedhtml1);
	}
  }


}

/*Related Providers*/
class service_finder_related_providers extends WP_Widget {

function __construct() {
	$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');
	parent::__construct(
			'service_finder_related_providers',
			esc_html__('Related', 'service-finder' ).' '.$providerreplacestring,
			array( 'description' => esc_html__('Related', 'service-finder' ).' '.$providerreplacestring, ) // Args
		);
}

function form($instance)
{
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>
<p>
  <label for="<?php echo esc_html($this->get_field_id('title')); ?>">
  <?php esc_html_e('Title', 'service-finder'); ?>
  :
  <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo  esc_attr($title); ?>" />
  </label>
</p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
  	global $service_finder_Params;
  
    extract($args, EXTR_SKIP);
	$allowedhtml1 = array(
			'div' => array(
				'class' => array(),
				'id' => array()
			),
		);
	$allowedhtml2 = array(
			'h4' => array(
				'class' => array(),
			),
		);	
    echo wp_kses($before_widget,$allowedhtml1);
    $providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');
	$title = empty($instance['title']) ? esc_html__('Related', 'service-finder').' '.$providerreplacestring : apply_filters('widget_title', $instance['title']);
 
	if ($title != ' '){
      echo wp_kses($before_title,$allowedhtml2) . esc_html($title) . wp_kses($after_title,$allowedhtml2);
    } 
 	
	?>
<div class="padding-20  margin-b-30  bg-white sf-rouned-box">
  <div class="recent-services-bx">
    <ul>
      <?php 
			    global $author;
			  	$providerid = $author;
				$providers = service_finder_getRelatedProviders($providerid,get_user_meta($providerid,'primary_category',true),3);
				if(!empty($providers)){
					foreach($providers as $provider){
					$bookingurl = service_finder_get_author_url($provider->wp_user_id);
					$src = '';
					if(!empty($provider->avatar_id) && $provider->avatar_id > 0){
						$src  = wp_get_attachment_image_src( $provider->avatar_id, 'service_finder-related-provider' );
						$src  = $src[0];
					}
					
					if($src != ''){
						$imgtag = '<img src="'.esc_url($src).'" width="150" height="150">';
					}else{
						$imgtag = '<img src="'.esc_url($service_finder_Params['pluginImgUrl'].'/no_img.jpg').'" width="150" height="150">';
					}
					
					
						echo '<li>';	
						echo '<div class="post-thum-bx">';
						print_r($imgtag);
						echo '</div>';
						echo '<div class="post-text-bx">';
						echo '<h6 class="post-title"><a href="'.esc_url($bookingurl).'">'.$provider->full_name.'</a></h6>';
						echo '<p>'.$provider->address.'<br>'.$provider->city.'</p>';
						echo '</div>';
						echo '</li>';
					}
				}else{
					echo '<li>';
					esc_html_e('No Related Provider Available', 'service-finder');
					echo '</li>';
				}
				?>
    </ul>
  </div>
</div>
<?php      
	echo wp_kses($after_widget,$allowedhtml1);
  }


}

/*Providers Category*/
class service_finder_providers_category extends WP_Widget {

function __construct() {
	$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');
	parent::__construct(
			'service_finder_providers_category',
			$providerreplacestring.' '.esc_html__('Categories', 'service-finder' ),
			array( 'description' => esc_html__('Display', 'service-finder' ).' '.$providerreplacestring.' '.esc_html__('Category', 'service-finder' ), ) // Args
		);
}

function form($instance)
{
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>
<p>
  <label for="<?php echo esc_html($this->get_field_id('title')); ?>">
  <?php esc_html_e('Title', 'service-finder'); ?>
  :
  <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo  esc_attr($title); ?>" />
  </label>
</p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
  	global $service_finder_Params;
  
    extract($args, EXTR_SKIP);
	$allowedhtml1 = array(
			'div' => array(
				'class' => array(),
				'id' => array()
			),
		);
	$allowedhtml2 = array(
			'h4' => array(
				'class' => array(),
			),
		);	
    echo wp_kses($before_widget,$allowedhtml1);
    $providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');
	$title = empty($instance['title']) ? $providerreplacestring.' '.esc_html__('Category', 'service-finder' ) : apply_filters('widget_title', $instance['title']);
 
	if ($title != ' '){
      echo wp_kses($before_title,$allowedhtml2) . esc_html($title) . wp_kses($after_title,$allowedhtml2);
    } 
 	
	?>
<div class="padding-20 margin-b-30 sf-rouned-box">
  <div class="providers-category-bx">
    <ul>
    <?php 
	$html = '';
	if(class_exists('service_finder_texonomy_plugin')){
                    $limit = 1000;
                    $categories = service_finder_getCategoryList($limit);
					$texonomy = 'providers-category';
					
                    if(!empty($categories)){
                        foreach($categories as $category){
						$term_id = (!empty($category->term_id)) ? $category->term_id : '';
						$term_name = (!empty($category->name)) ? $category->name : '';
						$catimage =  service_finder_getCategoryImage($term_id,'service_finder-category-small');
						$categorylink = service_finder_getCategoryLink($term_id);
						$html .= '<li><a href="'.esc_url($categorylink).'">'.$term_name;
						
						$term_children = get_term_children($term_id,$texonomy);
						$namearray = array();
						if(!empty($term_children)){
						    foreach ($term_children as $child) {
								$term = get_term_by( 'id', $child, $texonomy );
					
								$namearray[$term->name]= $child;
				
							}
							
							if(!empty($namearray)){
							ksort($namearray);
							$html .= '<ul>';
							foreach($namearray as $key => $value) {
								$term_child_id = $value;
								$term_child = get_term_by('id',$term_child_id,$texonomy);
								$term_child_name = (!empty($term_child->name)) ? $term_child->name : '';
								$categorylink = service_finder_getCategoryLink($term_child_id);
								$catimage =  service_finder_getCategoryImage($term_child_id,'service_finder-category-small');
								$html .= '<li><a href="'.esc_url($categorylink).'">'.$term_child_name.'</a></li>';
								
							}
							$html .= '</ul>';
							}
						}
						$html .= '</a></li>';
                        
                        }
                    }	
		}	
	print_r($html);		    
	?>
    </ul>
  </div>
</div>
<?php      
	echo wp_kses($after_widget,$allowedhtml1);
  }


}

/*Providers Cities*/
class service_finder_providers_cities extends WP_Widget {

function __construct() {
	$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');
	parent::__construct(
			'service_finder_providers_cities',
			$providerreplacestring.' '.esc_html__('Cities', 'service-finder' ),
			array( 'description' => esc_html__('Display', 'service-finder' ).' '.$providerreplacestring.' '.esc_html__('Cities', 'service-finder' ), ) // Args
		);
}

function form($instance)
{
    $instance = wp_parse_args( (array) $instance, array( 'title' => '','numberofcities' => 6 ) );
    $title = $instance['title'];
	$numberofcities = $instance['numberofcities'];
?>
<p>
  <label for="<?php echo esc_html($this->get_field_id('title')); ?>">
  <?php esc_html_e('Title', 'service-finder'); ?>
  :
  <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo  esc_attr($title); ?>" />
  </label>
</p>
<p><label for="<?php echo esc_html($this->get_field_id('numberofcities')); ?>"><?php esc_html_e('Number of cities to show:', 'service-finder'); ?></label>
		<input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('numberofcities')); ?>" name="<?php echo esc_attr($this->get_field_name('numberofcities')); ?>" type="number" step="1" min="1" value="<?php echo  esc_attr($numberofcities); ?>" size="3"></p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
	$instance['numberofcities'] = $new_instance['numberofcities'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
  	global $service_finder_Params;
  
    extract($args, EXTR_SKIP);
	$allowedhtml1 = array(
			'div' => array(
				'class' => array(),
				'id' => array()
			),
		);
	$allowedhtml2 = array(
			'h4' => array(
				'class' => array(),
			),
		);	
    echo wp_kses($before_widget,$allowedhtml1);
    $providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');
	$title = empty($instance['title']) ? $providerreplacestring.' '.esc_html__('Cities', 'service-finder' ) : apply_filters('widget_title', $instance['title']);
 
	if ($title != ' '){
      echo wp_kses($before_title,$allowedhtml2) . esc_html($title) . wp_kses($after_title,$allowedhtml2);
    } 
 	
	$limit = $instance['numberofcities'];

	$args = array(
		'hide_empty' => false,
		'number' => $limit,
		'taxonomy'  => 'sf-cities',
		);
	$cities = get_terms( $args );
	
	?>
        <ul class="providers-city-list">
        <?php
        if(!empty($cities))
        {
            foreach($cities as $city)
            {
                ?>
                <li><a href="<?php echo get_term_link($city)?>"><?php echo esc_html($city->name); ?></a></li>
                <?php
            }
        }
        ?>
        </ul>
<?php      
	echo wp_kses($after_widget,$allowedhtml1);
  }


}
