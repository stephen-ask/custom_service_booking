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

global $current_user, $service_finder_options;

$args = array(
	'post_type' 	=> 'sf_articles',
	'post_status' 	=> 'publish',
	'posts_per_page' => 5,
	'order' => 'DESC',
	'author' => $author,
);
$the_query = new WP_Query( $args );

if ( $the_query->have_posts() ) {

if(service_finder_themestyle() == 'style-4'){
$fullarticlewrap = (service_finder_get_data($service_finder_options,'booking-page-style') == 'style-2') ? 'sf-provi-articles-full' : '';
echo '<ul class="sf-provi-articles-list '.sanitize_html_class($fullarticlewrap).' d-flex flex-wrap">';
	while( $the_query->have_posts() ) : $the_query->the_post();
	global $post;
	$imgsrc = wp_get_attachment_image_src( get_post_thumbnail_id(), 'service_finder-blog-thumbimage', false );
	$imgpath = (!empty($imgsrc[0])) ? $imgsrc[0] : SERVICE_FINDER_BOOKING_IMAGE_URL.'/art-place.jpg';
	?>
	<li>
      <div class="sf-provi-art-list d-flex flex-wrap">
        <div class="sf-provi-art-left d-flex flex-wrap">
          <div class="sf-provi-art-pic"><img src="<?php echo esc_url($imgpath); ?>" alt=""></div>
          <div class="sf-provi-art-date"><i class="fa fa-calendar-o"></i> <?php echo get_the_date( 'M j, Y', $post->ID ); ?></div>
          <div class="sf-provi-art-comment"><i class="fa fa-comment-o"></i> <?php echo esc_html__('Comments', 'service-finder'); ?> (<?php echo get_comments_number( get_the_id() ); ?>)</div>
        </div>
        <div class="sf-provi-art-right d-flex flex-wrap">
          <h4  class="sf-provi-art-title"><a href="<?php echo get_permalink(); ?>" target="_blank"><?php echo get_the_title(); ?></a></h4>
          <div class="sf-provi-art-text"><?php echo service_finder_getExcerpts(nl2br(stripcslashes(get_the_excerpt())),0,120); ?></div>
          <a class="sf-provi-art-btn" href="<?php echo get_permalink(); ?>" target="_blank"><?php echo esc_html__('Read More', 'service-finder'); ?></a> </div>
      </div>
    </li>
    <?php
	endwhile;
	echo '</ul>';
}else{
	echo '<ul class="sf-ques-ans-list clearfix">';
	while( $the_query->have_posts() ) : $the_query->the_post();
	global $post;
	?>
    <li>
		<div class="sf-ques-area">
			<div class="sf-ques-ans-author"><img src="<?php echo service_finder_get_avatar_by_userid($post->post_author); ?>" alt=""></div>
			<div class="sf-ques-has"><a href="<?php echo get_permalink(); ?>" target="_blank"><?php echo get_the_title(); ?></a></div>
			<div class="sf-ques-has-desc"><?php the_excerpt(); ?></div>
			<div class="sf-ques-ans-meta">
				<span class="sf-ques-meta-col sf-qa-vote"><i class="fa fa-calendar-o"></i> <?php echo get_the_date( 'M j, Y', $post->ID ); ?></span>
				<span class="sf-ques-meta-col sf-qa-hour"><i class="fa fa-clock-o"></i> <?php printf( __( '%s ago', 'service-finder' ), human_time_diff( get_post_time( 'U' ), time() ) ); ?></span>
			</div>
		</div>
	</li>
	<?php
	endwhile;
	echo '</ul>';
}

wp_reset_postdata();
$authorlink = service_finder_get_author_url($author);
$url = add_query_arg( array('morearticles' => "true"), $authorlink );
echo '<div class="padding-t-20 text-center"><a href="'.esc_url($url).'" target="_blank" class="btn btn-primary">'.esc_html__('More from this provider', 'service-finder').'</a></div>';
}else{
echo '<div class="sf-nodata-dark">'.esc_html__('No data available.', 'service-finder').'</div>';
}

?>
