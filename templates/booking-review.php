<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

global $post, $service_finder_Tables;

$limit = (get_option('comments_per_page') > 0) ? get_option('comments_per_page') : 5;  
$authorurl = service_finder_get_author_url($author);
if (isset($_GET["comment_page"])) { $page  = $_GET["comment_page"]; } else { $page=1; };  
$start_from = ($page-1) * $limit;  
  
$reviews = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feedback.' where provider_id = %d ORDER BY id DESC LIMIT %d, %d',$author,$start_from, $limit));

$allreviews = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feedback.' where provider_id = %d',$author));
$totalreview = count($allreviews);
?>
  <div class="clear" id="comment-list">
	<div class="comments-area" id="comments">
      	<?php
        if($totalreview > 0)
		{
		?>
        <div class="sf-provider-rating-box">
		  <?php
          service_finder_review_box($author,$totalreview);
          ?>
          </div>
          <?php } ?>
          <h2 class="comments-title"> 
		  <?php 
		  if($totalreview > 1){
		  	printf( esc_html__('%d Reviews', 'service-finder' ), $totalreview );
		  }else{
		  	printf( esc_html__('%d Review', 'service-finder' ), $totalreview );
		  }
		  ?></h2>
		<div id='contentInner'>
        <!-- comment list END -->
		<ol class="comment-list">
        <?php 
		if(!empty($reviews)){
			foreach($reviews as $review){
			$customername = get_user_meta($review->customer_id,'first_name',true).' '.get_user_meta($review->customer_id,'last_name',true);
			$avatar_id = service_finder_getCustomerAvatarID($review->customer_id);
			if(!empty($avatar_id) && $avatar_id > 0){
					$src  = wp_get_attachment_image_src( $avatar_id, 'thumbnail' );
					$src  = $src[0];
			}else{
					$src = '//2.gravatar.com/avatar/2d8b3378fb00ca047026e456903cae16?s=56&d=mm&r=g';
			}		
			?>
			<li class="comment byuser comment-author-admin bypostauthor odd alt thread-odd thread-alt depth-1" id="comment-41">
				<div id="div-comment-41" class="comment-body">
				<div class="comment-author vcard">
			<img alt="" src="<?php echo esc_url($src); ?>" class="avatar avatar-56 photo" width="56" height="56">			
            <cite class="fn"><?php echo esc_html($customername); ?></cite> <span class="says"><?php echo esc_html__('says:','service-finder')?></span>		</div>
            <?php if(!empty($review->date)){ ?>
            <div class="comment-meta commentmetadata"> <a href="javascript:;"><?php echo date('M, d, Y \A\T h:i a',strtotime($review->date)); ?></a></div>
            <?php } ?>
		
            <div class="show-rating-bx">
			<?php 
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM `'.$service_finder_Tables->custom_rating.'` where `feedbackid_id` = %d',$review->id));

			$rating = '';
			if(!empty($row)){
			
			if($row->label1 != ""){
			$k = 1;
			}
			if($row->label2 != ""){
			$k = 2;
			}
			if($row->label3 != ""){
			$k = 3;
			}
			if($row->label4 != ""){
			$k = 4;
			}
			if($row->label5 != ""){
			$k = 5;
			}
			
			$rating .= '<div class="sf-customer-display-rating">';
			for($i=1;$i<=$k;$i++){
			switch($i){
			case 1:
				$label = $row->label1;
				$ratingnumber = $row->rating1;
				break;
			case 2:
				$label = $row->label2;
				$ratingnumber = $row->rating2;
				break;
			case 3:
				$label = $row->label3;
				$ratingnumber = $row->rating3;
				break;
			case 4:
				$label = $row->label4;
				$ratingnumber = $row->rating4;
				break;
			case 5:
				$label = $row->label5;
				$ratingnumber = $row->rating5;
				break;				
			}
			$rating .= '<div class="sf-customer-rating-row clearfix">';
				
				$rating .= '<div class="sf-customer-rating-name pull-left">'.$label.'</div>';
				
				$rating .= '<div class="sf-customer-rating-count  pull-right">';
				$rating .= service_finder_displayRating($ratingnumber);
				$rating .= '</div>';
			$rating .= '</div>';	
			}
			$rating .= '</div>';
		
			echo $rating;
			
			}else{
			echo service_finder_displayRating($review->rating);
			}
			?>
            </div>
            <p><?php echo $review->comment; ?></p>

				</div>
		</li>
			<?php
			}
		}?>
		  
		</ol>
		<!-- comment list END -->
        </div>
        
        <?php $total_pages = ceil($totalreview / $limit); ?>
		<?php if($total_pages > 1){ ?>
        <div align="center">
		<ul class='pagination text-center'>
		<?php if(!empty($total_pages)):for($i=1; $i<=$total_pages; $i++):  
					if($i == 1):?>
					<li class='active' id="<?php echo $i;?>" data-link="<?php echo $authorurl ?>"><a href='<?php echo $authorurl.'/?comment_page='.$i.'#sf-provider-review'; ?>'><?php echo $i;?></a></li> 
					<?php else:?>
					<li id="<?php echo $i;?>" data-link="<?php echo $authorurl ?>"><a href='<?php echo $authorurl.'/?comment_page='.$i.'#sf-provider-review'; ?>'><?php echo $i;?></a></li>
				<?php endif;?>			
		<?php endfor;endif;?>  
		</ul>
		</div>
        <?php } ?>

	</div>
  </div>
<?php 



?>
