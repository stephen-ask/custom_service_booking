<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

/*************************************************************
*
*	SedateTheme (c) sedatelab.com - Breadcrumb
*	Description: 
*
**************************************************************/
$service_finder_options = get_option('service_finder_options');
?>
<?php
$texonomy = 'providers-category';
$li = '';
if(is_tax( $texonomy )){
if(class_exists('service_finder_texonomy_plugin')) {
	$catid = get_queried_object()->term_id;
	$term = get_term( $catid, $texonomy );
	if($term->parent > 0){
	$termid = $term->parent;
		$li = '<li><a href="'.esc_url(get_term_link( $term->parent, $texonomy )).'">'.service_finder_getCategoryName($term->parent).'</a></li>';
		$li .= '<li>'.service_finder_getCategoryName($catid).'</li>';
	}else{
		$li = '<li>'.service_finder_getCategoryName($catid).'</li>';
	}
}	
}elseif(is_author()){
if(class_exists('service_finder_texonomy_plugin')) {
$authorcatid = get_user_meta($author,'primary_category',true);
if($authorcatid != ""){
$li .= service_finder_get_category_breadcrumb($authorcatid);
}
}	
	$li .= '<li>'.service_finder_getProviderName($author).'</li>';
}elseif(is_search()){
	$li .= '<li>'.esc_html__('Search', 'service-finder').'</li>';
}elseif(is_404()){
	$li .= '<li>404</li>';
}else{
	$perentid = wp_get_post_parent_id( get_the_ID() );
	if($perentid > 0){
	$li .= '<li><a href="'.get_the_permalink($perentid).'">'.get_the_title($perentid).'</a></li>';
	}
	$li .= '<li>'.get_the_title().'</li>';
}
?>
<?php
$breadcrumbs = service_finder_breadcrumb_pl();
if($breadcrumbs){
if(is_author() && service_finder_themestyle() == 'style-3')
{
?>
	<div class="sf-banner-breadcrumbs-nav">
		<ul class="list-inline">
          <li><a href="<?php echo esc_url(home_url('/')); ?>">
            <?php esc_html_e('Home', 'service-finder'); ?>
            </a></li>
          <?php 
          $allowedhtml = array(
                'a' => array(
                    'href' => array(),
                    'title' => array()
                ),
                'li' => array(),
            );
          echo wp_kses($li,$allowedhtml); 
          ?>
        </ul>
    </div>
<?php
}else
{
?>
<div class="breadcrumb-row">
  <div class="container">
    <ul class="list-inline">
      <li><a href="<?php echo esc_url(home_url('/')); ?>">
        <?php esc_html_e('Home', 'service-finder'); ?>
        </a></li>
      <?php 
	  $allowedhtml = array(
			'a' => array(
				'href' => array(),
				'title' => array()
			),
			'li' => array(),
		);
	  echo wp_kses($li,$allowedhtml); 
	  ?>
    </ul>
  </div>
</div>
<?php
}
}
?>
