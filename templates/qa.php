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
    'post_type' 	=> 'sf_questions',
    'post_status' 	=> 'publish',
    'posts_per_page' => 5,
    'order' => 'DESC',
    'meta_query' => array(
        array(
            'key' 		=> '_question_for_provider_id',
            'value' 	=> $author,
            'compare' 	=> '=',
        ),
    ),
);
$the_query = new WP_Query( $args );

if ( $the_query->have_posts() ) {

if(service_finder_themestyle() == 'style-4'){
if(service_finder_get_data($service_finder_options,'booking-page-style') == 'style-2'){
echo '<ul class="sf-qes-answer-list sf-qes-answerList-full d-flex flex-wrap">';
}else{
echo '<ul class="sf-qes-answer-list">';
}

while( $the_query->have_posts() ) : $the_query->the_post();
global $post;
$question_for = get_post_meta($post->ID, '_question_for_provider_id', true);
?>
<li>
<h5 class="sf-qestion-line"><a href="<?php echo get_permalink(); ?>" target="_blank"><?php echo get_the_title(); ?></a> <i class="fa fa-plus"></i></h5>
<div class="sf-answer-line"><?php echo get_the_excerpt(); ?></div>
</li>
<?php

endwhile;
echo '</ul>';
}else{
echo '<ul class="sf-ques-ans-list clearfix">';
while( $the_query->have_posts() ) : $the_query->the_post();
global $post;
$question_for = get_post_meta($post->ID, '_question_for_provider_id', true);
?>
<li>
    <div class="sf-ques-area">
        <div class="sf-ques-ans-author"><img src="<?php echo service_finder_get_avatar_by_userid($post->post_author); ?>" alt=""></div>
        <div class="sf-ques-has"><a href="<?php echo get_permalink(); ?>" target="_blank"><?php echo get_the_title(); ?></a></div>
        <div class="sf-ques-has-desc"><?php the_content(); ?></div>
        <div class="sf-ques-ans-meta">
            <span class="sf-ques-meta-col sf-qa-answers" data-toggle="modal" data-target="#answers-modal"><i class="fa fa-commenting-o"></i> <?php printf(esc_html__('%d Answers', 'service-finder'),service_finder_get_total_answers($post->ID)); ?></span>
            <span class="sf-ques-meta-col sf-qa-vote"><i class="fa fa-calendar-o"></i> <?php echo get_the_date( 'M j, Y', $post->ID ); ?></span>
            <span class="sf-ques-meta-col sf-qa-hour"><i class="fa fa-clock-o"></i> <?php printf( __( '%s ago', 'service-finder' ), human_time_diff( get_post_time( 'U' ), time() ) ); ?></span>
        </div>
        <?php 
        if(is_user_logged_in()){
            if($current_user->ID == $author){
                service_finder_answer_button_html($post->ID);
            } 
        }
        ?>
    </div>
    <?php 
    if(is_user_logged_in()){
        if($current_user->ID == $author){
            service_finder_answer_html($post->ID);
        } 
    }
    ?>
    <?php
    $args = array( 'post_type' => 'sf_answers', 'posts_per_page' => 1, 'post_status' =>'publish', 'post_parent' => $post->ID, 'order' => 'DESC' ); 
    $answers = get_posts( $args );
    if ( $answers ) {
        foreach ( $answers as $answer ) {
            ?>
            <div class="sf-ansering-area">
                <div class="sf-answer-icon"><img src="<?php echo SERVICE_FINDER_BOOKING_IMAGE_URL.'/qapic.jpg'; ?>" alt=""></div>
                <?php
                print_r($answer->post_content);
                echo '....';
                echo '<a href="'.get_permalink($post->ID).'#'.$answer->ID.'" class="readmore-link">('.esc_html__( 'more', 'service-finder' ).')</a>';
                ?>
            </div>
            <?php
        }
    }
    $the_query->reset_postdata();
    ?>
</li>
<?php

endwhile;
echo '</ul>';
}


wp_reset_postdata();
$authorlink = service_finder_get_author_url($author);
$url = add_query_arg( array('moreqa' => "true"), $authorlink );
echo '<div class="padding-t-20 text-center"><a href="'.esc_url($url).'" target="_blank" class="btn btn-primary">'.esc_html__('More from this provider', 'service-finder').'</a></div>';
}else{
echo '<div class="sf-nodata-dark">'.esc_html__('No data available.', 'service-finder').'</div>';
}
