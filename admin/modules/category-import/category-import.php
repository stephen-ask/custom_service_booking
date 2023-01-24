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

/**
 * Class SERVICE_FINDER_sedateFeatured
 */
class SERVICE_FINDER_CATEGORY_IMPORT extends SERVICE_FINDER_sedateManager{

	/*Actions for wp ajax call*/
	protected function service_finder_registerWpActions() {
     	$_this = $this;
     	$_this = $this;
	   add_action(
                    'wp_ajax_import_to_category',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_import_to_category' ) );
                    }
						
                );
	}
	
	/*Initial Function*/
	public function service_finder_index()
    {
        /*Rander providers template*/
		$this->service_finder_render( 'index','category-import' );
		
		/*Action for wp ajax call*/
		$this->service_finder_registerWpActions();
	 }
    
    public function service_finder_import_to_category(){
	
		$cattype = (!empty($_POST['cattype'])) ? esc_html($_POST['cattype']) : '';
	
		if(!get_option('job_manager_enable_categories') && $cattype == 'job'){
		$error = array(
				'status' => 'error',
				'err_message' => esc_html__('Listing categories are disabled. Please enable it before import.', 'service-finder'),
				);
		echo json_encode($error);
		exit;
		}
    
		$args = array(
			'orderby'           => 'id',
			'order'             => 'ASC',
			'hide_empty'        => false, 
		); 
		$categories = get_terms( 'providers-category',$args );
		
		if($cattype == 'job'){
		$taxonomy = 'job_listing_category';
		$type = 'Job';
		}elseif($cattype == 'question'){
		$taxonomy = 'sf_question_category';
		$type = 'Questions';
		}elseif($cattype == 'article'){
		$taxonomy = 'sf_article_category';
		$type = 'Articles';
		}
		
		if(!empty($categories)){
			foreach($categories as $category){
				$parent_job_categotyid = 0;
				if($category->parent > 0){
					$term = get_term( $category->parent, "providers-category" );	
					if(!empty($term)){
						$catname = $term->name;
						$jobcategory = get_term_by('name', $catname, $taxonomy);
						$parent_job_categotyid = $jobcategory->term_id;
					}
				}
			
				$args = array(
					'description' => $category->description,
					'parent'      => $parent_job_categotyid,
					'slug'        => $category->slug, 
				); 
				
				$term = wp_insert_term( $category->name, $taxonomy, $args );
				
				if(!is_wp_error( $term )){
					$termid = (!empty($term['term_id'])) ? $term['term_id'] : 0;
					if($termid > 0 && ($cattype == 'question' || $cattype == 'article')){
					$term_meta_icon = get_option( "providers-category_icon_".$category->term_id );
					update_option( "cat_icon_".$termid, $term_meta_icon );
					}
				}
				
				
			}
		}
		
		$success = array(
				'status' => 'success',
				'suc_message' => sprintf(esc_html__('%s category imported successfully', 'service-finder'),$type),
				);
		echo json_encode($success);
		
		exit;
	}
	
}