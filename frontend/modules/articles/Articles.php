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

class SERVICE_FINDER_Articles{


	/*Add New Article*/
	public function service_finder_addArticle($arg = ''){
			global $wpdb, $service_finder_Tables;
			
			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
			$categoryid = (!empty($arg['categoryid'])) ? $arg['categoryid'] : '';
			$title = (!empty($arg['article_title'])) ? $arg['article_title'] : '';
			$content = (!empty($arg['article_description'])) ? $arg['article_description'] : '';
			$attachment_id = (!empty($arg['articlefeaturedattachmentid'])) ? $arg['articlefeaturedattachmentid'] : '';
			
			$article_data = array(
                'post_title' 	=> $title,
                'post_status' 	=> 'publish',
                'post_content'  => $content,
                'post_author' 	=> $user_id,
                'post_type' 	=> 'sf_articles',
                'post_date' 	=> current_time('Y-m-d H:i:s')
            );
            
			$post_id = wp_insert_post($article_data);
			
			$cat_ids = array( $categoryid );
			$cat_ids = array_map( 'intval', $cat_ids );
			$cat_ids = array_unique( $cat_ids );

			wp_set_object_terms( $post_id, $cat_ids, 'sf_article_category',true );
			
			update_post_meta($post_id, '_article_category_id', $categoryid);
			
			if (!empty($attachment_id)) {
                set_post_thumbnail($post_id, $attachment_id);
            }
			
			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Add article successfully.', 'service-finder'),
					);
			echo json_encode($success);
	}
	
	/*update Article*/
	public function service_finder_updateArticle($arg = ''){
			global $wpdb, $service_finder_Tables;
			
			$articleid = (!empty($arg['articleid'])) ? $arg['articleid'] : '';
			$categoryid = (!empty($arg['categoryid'])) ? $arg['categoryid'] : '';
			$title = (!empty($arg['article_title'])) ? $arg['article_title'] : '';
			$content = (!empty($arg['edit_article_description'])) ? $arg['edit_article_description'] : '';
			$attachment_id = (!empty($arg['articlefeaturedattachmentid'])) ? $arg['articlefeaturedattachmentid'] : '';
			
			$article_data = array(
				  'ID'           => $articleid,
				  'post_title' 	=> $title,
				  'post_content'  => $content,
			);
			wp_update_post( $article_data );
			
			update_post_meta($articleid, '_article_category_id', $categoryid);  
			
			$old_attachment_id = get_post_thumbnail_id( $articleid );
			if( !empty( $old_attachment_id ) && intval( $old_attachment_id ) != intval( $attachment_id )  ){
				wp_delete_attachment( $old_attachment_id, true );
			}
			
			if (!empty($attachment_id)) {
				delete_post_thumbnail($articleid);
				set_post_thumbnail($articleid, $attachment_id);
			}

			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Update article successfully.', 'service-finder'),
					);
			echo json_encode($success);
	}
	
	/*Get Saved Articles into datatable*/
	public function service_finder_getArticles($arg){
		global $wpdb, $service_finder_Tables;
		$requestData= $_REQUEST;
		$currUser = wp_get_current_user(); 
		$columns = array( 
			0 =>'ID', 
			1 =>'post_title', 
		);
		
		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
		
		// getting total number records without any search
		$sql = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix. "posts WHERE `post_author` = %d AND `post_status` = 'publish' AND `post_type` = 'sf_articles'",$user_id);
		$query=$wpdb->get_results($sql);
		$totalData = count($query);
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		$sql = "SELECT * FROM ".$wpdb->prefix. "posts WHERE `post_status` = 'publish' AND `post_type` = 'sf_articles' AND `post_author` = ".$user_id;
		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql.=" AND ( `post_title` LIKE '".$requestData['search']['value']."%' ";    
			$sql.=" OR `post_content` LIKE '".$requestData['search']['value']."%' )";    
		}
		$query=$wpdb->get_results($sql);
		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		$query=$wpdb->get_results($sql);
		$data = array();
		
		foreach($query as $result){
			$nestedData=array(); 
		
			$nestedData[] = '<div class="checkbox sf-radio-checkbox">
			  <input type="checkbox" id="article-'.$result->ID.'" class="deleteArticleRow" value="'.esc_attr($result->ID).'">
			  <label for="article-'.$result->ID.'"></label>
			</div>';
			
			$nestedData[] = $result->post_title;
			$nestedData[] = '<button title="Edit Article" data-id="'.esc_attr($result->ID).'" class="btn btn-primary btn-xs editArticle" type="button">'.esc_html__('Edit', 'service-finder').'</button>';
			
			$data[] = $nestedData;
		}
		
		
		
		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data   // total data array
					);
		
		echo json_encode($json_data);  // send data as json format
	}	
	
	/*Delete Articles*/
	public function service_finder_deleteArticles($arg){
		global $wpdb, $service_finder_Tables;
		$currUser = wp_get_current_user(); 
			$data_ids = $_REQUEST['data_ids'];
			$data_id_array = explode(",", $data_ids); 
			if(!empty($data_id_array)) {
				foreach($data_id_array as $id) {
					wp_delete_post( $id, true );
				}
			}
			wp_send_json_success();
	}
		
	/*Load article for edit*/
	public function service_finder_loadArticle($arg){
		global $wpdb, $service_finder_Tables;		
		
		$articleid = (!empty($arg['articleid'])) ? $arg['articleid'] : '';
		
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'posts WHERE `ID` = %d',$articleid));

		if(!empty($row)){
		
			$attachment_id = get_post_thumbnail_id( $row->ID );
			
			$hiddenclass = '';
			$html = '';
			
			if (!empty($attachment_id)) {
				$src  = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
				$src  = $src[0];
				$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
				$hiddenclass = 'hidden';
				
				$html = sprintf('<li id="item_%s">
					<img src="%s" />
					<div class="rwmb-image-bar">
						<a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
						<input type="hidden" name="articlefeaturedattachmentid" value="%s">
					</div>
				</li>',
				esc_attr($attachment_id),
				esc_url($src),
				esc_attr($i18n_delete), esc_attr($attachment_id),
				esc_attr($attachment_id)
				);
			}
			
			$category_id = get_post_meta($articleid, '_article_category_id', true);
			
			$result = array(
				'article_title' => $row->post_title,
				'article_descrption' => $row->post_content,
				'attachment_id' => $attachment_id,
				'category_id' => $category_id,
				'imagehtml'	 => $html,
				'hiddenclass' => $hiddenclass,
			);

			echo json_encode($result);
		}
			
	}
	
}