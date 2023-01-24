<?php

/*****************************************************************************

*

*	copyright(c) - aonetheme.com - Service Finder Team

*	More Info: http://aonetheme.com/

*	Coder: Service Finder Team

*	Email: contact@aonetheme.com

*

******************************************************************************/



global $post, $wp_query, $service_finder_options;



$customerstring = (!empty($service_finder_options['customer-replace-string'])) ? esc_attr($service_finder_options['customer-replace-string']) : esc_html__( 'customer', 'service-finder' );

$providerstring = (!empty($service_finder_options['customer-replace-string'])) ? esc_attr($service_finder_options['provider-replace-string']) : esc_html__( 'provider', 'service-finder' );



$author_post_id = get_user_meta($author,'comment_post',true);

query_posts(array( 

	'post_type' => 'sf_comment_rating',

	'p' => $author_post_id

) );  

the_post();



$wp_query->is_single = true;

//get comments

if ( comments_open($author_post_id) || get_comments_number() ) {

	$allowedhtml = array(

		'a' => array(

			'href' => array(),

			'title' => array()

		),

	);

  ?>

 

  <div class="clear" id="comment-list">

	<div class="comments-area" id="comments">

    <?php 

    $providersreview = (!empty($service_finder_options['providers-review'])) ? $service_finder_options['providers-review'] : false;



	if($providersreview){

		if(!is_user_logged_in() || (service_finder_getUserRole($current_user->ID) != 'Customer' && service_finder_getUserRole($current_user->ID) != 'Provider')){ 

			$allowedhtml = array(

				'a' => array(

					'href' => array(),

					'data-action' => array(),

					'data-redirect' => array(),

					'data-toggle' => array(),

					'data-target' => array(),

				),

			);

			echo '<div class="alert alert-warning" role="alert">';

			echo esc_html__('Please ', 'service-finder'); 

			echo '<a href="javascript:;" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal">'.esc_html__('login', 'service-finder').'</a>'; 

			echo sprintf( esc_html__('via %s/%s account to submit review', 'service-finder'), strtolower($customerstring),strtolower($providerstring) ); 

			echo '</div>';

		}

	}else{

		if(!is_user_logged_in() || service_finder_getUserRole($current_user->ID) != 'Customer'){ 

			echo '<div class="alert alert-warning" role="alert">';

			echo esc_html__('Please ', 'service-finder'); 

			echo '<a href="javascript:;" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal">'.esc_html__('login', 'service-finder').'</a>'; 

			echo sprintf( esc_html__(' via %s account to submit review', 'service-finder'), strtolower($customerstring) ); 

			echo '</div>';

		}

	}

	?>

      	<?php

          $totalreview = get_comments_number( get_the_id() );

		  if($totalreview > 0)

		  {

		  echo '<div class="sf-provider-rating-box">';

          service_finder_review_box($author,$totalreview);

		  echo '</div>';

		  }

          ?>

          <!-- comment list END -->

		<?php comments_template(); ?>

		<!-- comment list END -->

	</div>

  </div>

<?php }

$wp_query->is_single = false;







?>

