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
<div class="sf-qa-wrap sf-ask-question">
    <div class="qa-pic"><img src="<?php echo SERVICE_FINDER_BOOKING_IMAGE_URL.'/qna.png'; ?>" alt=""></div>
    <h4><?php echo esc_html__('Get answers to your queries now', 'service-finder'); ?></h4>
    <button class="btn btn-primary" data-toggle="collapse" data-target="#sf-add-question"><?php echo esc_html__('Ask Question', 'service-finder'); ?></button>
    <div id="sf-add-question" class="sf-add-question-form collapse">
    	<?php if(is_user_logged_in()){ ?>
        <form method="post" class="add-question">
          <div class="form-group">
            <input type="text" class="form-control" name="question_title" placeholder="<?php esc_html_e('Question Title', 'service-finder'); ?>" required="required" data-error="<?php esc_html_e('Question title should not be empty', 'service-finder'); ?>">
          </div>
            <?php 
            $settings = array('media_buttons' => false);
            wp_editor('', 'question_description', $settings); 
            ?>
            <div class="form-group sf-question-cat-select">
			<?php
            echo service_finder_category_dropdown('sf_question_category');
			?>
            </div>
            <input type="hidden" name="author_id" value="<?php echo base64_encode($author); ?>">
            <input type="submit" class="btn btn-primary" name="add-question" value="<?php esc_html_e('Submit Question', 'service-finder'); ?>" />
        </form>
        <?php }else{ ?>
        <div class="alert alert-info"><?php echo esc_html__('Please login to submit your question.', 'service-finder'); ?></div>	
        <?php } ?>
    </div>
</div>