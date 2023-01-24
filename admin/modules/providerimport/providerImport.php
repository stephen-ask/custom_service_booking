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
class SERVICE_FINDER_providerImport extends SERVICE_FINDER_sedateManager{

	/*Actions for wp ajax call*/
	protected function service_finder_registerWpActions() {
     	$_this = $this;
     	$_this = $this;
	   add_action(
                    'wp_ajax_import_providers',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_import_providers' ) );
                    }
						
                );
       add_action(
                    'wp_ajax_keep_session_alive',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_keep_session_alive' ) );
                    }
						
                ); 
	   add_action(
                    'wp_ajax_import_categories',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_import_categories' ) );
                    }
						
                );        
        
	}
	
	/*Initial Function*/
	public function service_finder_index()
    {
        /*Rander providers template*/
		$this->service_finder_render( 'index','providerimport' );
		
		/*Action for wp ajax call*/
		$this->service_finder_registerWpActions();
	}
    
	public function service_finder_import_categories(){
		
		$row_data = array();
		if(isset( $_FILES['categorycsv'] ) && !empty($_FILES['categorycsv']))
		{
			$update_existing_category = (isset($_POST['update_existing_category'])) ? esc_html($_POST['update_existing_category']) : '';
			
			if($_FILES['categorycsv']['size'] > 0)
			{
				 $file_data = fopen($_FILES['categorycsv']['tmp_name'], 'r');
				 $column = fgetcsv($file_data);
				 while($row = fgetcsv($file_data))
				 {
				  if($row[0] != '')
				  $row_data[] = array(
				   'catname'  => $row[0],
				   'parentcat'  => $row[1],
				   'description'  => $row[2],
				   'image'  => $row[3]
				  );
				 }
				 $this->update_category_data($row_data,$update_existing_category);
			}
		}
		
		$success = array(
				'status' => 'success',
				'files' => $row_data,
				);
		wp_send_json_success($success);
	}
	
	public function update_category_data($rowdata = array(),$update_existing_category = 'no'){
		global $wpdb;
		$taxonomy = 'providers-category';
		if(!empty($rowdata)){
			foreach($rowdata as $data){
				
				$catname = (!empty($data['catname'])) ? esc_attr($data['catname']) : '';
				$parentcat = (!empty($data['parentcat'])) ? esc_attr($data['parentcat']) : '';
				$description = (!empty($data['description'])) ? esc_attr($data['description']) : '';
				$image = (!empty($data['image'])) ? esc_attr($data['image']) : '';
				
				if($update_existing_category == 'yes')
				{
				if($parentcat == ''){
					$catinfo = get_term_by('name', $catname, $taxonomy);
					if(!empty($catinfo))
					{
						$catid = $catinfo->term_id;
						$args = array(
							'description' => $description,
							'parent'      => 0,
						);
						$term = wp_update_term( $catid, $taxonomy, $args );
					}else{
						$args = array(
							'description' => $description,
							'parent'      => 0,
						);
						$term = wp_insert_term( $catname, $taxonomy, $args );
					}
				}else{
					$parentcategory = get_term_by('name', $parentcat, $taxonomy);
					if(!empty($parentcategory))
					{
						$parentcatid = $parentcategory->term_id;
						$catinfo = get_term_by('name', $catname, $taxonomy);
						if(!empty($catinfo))
						{
							$catid = $catinfo->term_id;
							$args = array(
								'description' => $description,
								'parent'      => $parentcatid,
							);
							$term = wp_update_term( $catid, $taxonomy, $args );
						}else{
							$args = array(
								'description' => $description,
								'parent'      => $parentcatid,
							);
							$term = wp_insert_term( $catname, $taxonomy, $args );
						}
						
					}
				}
				if(!is_wp_error( $term )){
					$termid = (!empty($term['term_id'])) ? $term['term_id'] : 0;
					if($termid > 0){
						if($image != '')
						{
							service_finder_import_category_image($termid,$image);
						}
					}
				}
				}else{
				if($parentcat == ''){
					$args = array(
						'description' => $description,
						'parent'      => 0,
					);
					$term = wp_insert_term( $catname, $taxonomy, $args );
				}else{
					$parentcategory = get_term_by('name', $parentcat, $taxonomy);
					if(!empty($parentcategory))
					{
					$parentcatid = $parentcategory->term_id;
					$args = array(
						'description' => $description,
						'parent'      => $parentcatid,
					);
					$term = wp_insert_term( $catname, $taxonomy, $args );
					}
				}
				if(!is_wp_error( $term )){
					$termid = (!empty($term['term_id'])) ? $term['term_id'] : 0;
					if($termid > 0){
						if($image != '')
						{
							service_finder_import_category_image($termid,$image);
						}
					}
				}
				}
			}
		}

	}
	
    public function service_finder_import_providers(){
    
		global $service_finder_Tables, $wpdb;
		$service_finder_options = get_option('service_finder_options');
		$wp_users_fields = array( "id", "user_login", "user_pass", "user_email", "first_name", "last_name",  "user_nicename", "display_name", "role", "user_url" );
		$wp_users_post_fields = array( "post_title", "post_status", "post_type");
		$wp_user_meta_fields = array( "comment_post", "booking_option");
		$wp_providers_fields = array( "wp_user_id", "admin_moderation", "account_blocked", "company_name", "full_name", "email",  "phone", "category_id", "address", "apt", "city", "state", "zipcode", "country",  "lat", "long" );
		$wp_job_limits_post_fields = array( "provider_id", "free_limits", "available_limits", "membership_date", "start_date", "expire_date");
		$wp_req_fields = array("Username", "Email");
		$service_finder_restricted_fields = array_merge( $wp_users_fields, $wp_users_post_fields, $wp_user_meta_fields, $wp_providers_fields, $wp_job_limits_post_fields, $wp_req_fields );
		$limit = 100;
	
		if(isset($_FILES['file']) && !empty($_FILES['file'])){
			
			$service_finder_options = get_option('service_finder_options');
			$update_existing_users = (isset($_POST['update_existing_users'])) ? esc_html($_POST['update_existing_users']) : '';
			$no_records = (isset($_POST['no_records'])) ? esc_html($_POST['no_records']) : 0;
			$uploadfiles = (isset($_FILES['file'])) ? $_FILES['file']: '';
			if ( is_array($uploadfiles) ) {
			 	if ($uploadfiles['error'] == 0) {
			 		$filetmp = $uploadfiles['tmp_name'];
			 		$filename = $uploadfiles['name'];
			 		
			 	   // get file info
				   $filetype = wp_check_filetype( basename( $filename ), array('csv' => 'text/csv') );
				   $filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
				   $filename = $filetitle . '.' . $filetype['ext'];
				  // $upload_dir = wp_upload_dir();
				   if ($filetype['ext'] != "csv") {
					$response['csv'] = 'File must be a CSV';
					echo json_encode( $response );
					 die();
				   }
				   $uploaded_file = wp_handle_upload( $uploadfiles, array( 'test_form' => false ) );
					if( $uploaded_file && ! isset( $uploaded_file['error'] ) ) {
						$response['response'] = "SUCCESS";
						$file = $response['url'] = $uploaded_file['url'];

						$csv_complete_contents = array_map('str_getcsv', file($file));
						$data = $csv_complete_filtered = array_filter(array_map('array_filter', $csv_complete_contents));
						
						$csv_num_rows = count($csv_complete_contents);
						$total_limit = $no_records+$limit;
						if($csv_num_rows > $total_limit){
							$fields_array=array();
							foreach($data as $key => $data){
								if($key==0){
									foreach($data as $key1 => $fieldName){
										$fields_array[$fieldName] = $key1;
									}
									
									$i = 0;
									$id_position = false;
									foreach( $data as $element ){
										$headers[] = $element;

										if( in_array( strtolower( $element ) , $service_finder_restricted_fields ) )
											$positions[ strtolower( $element ) ] = $i;

										if( !in_array( strtolower( $element ), $service_finder_restricted_fields ))
											$headers_filtered[] = $element;

										$i++;
									}
									$columns = count( $data );
									foreach ( $service_finder_restricted_fields as $service_finder_restricted_field ) {
										$positions[ $service_finder_restricted_field ] = false;
									}
									$fields_array=array();
									$count =0;
									foreach( $headers as $element ){
									   $fields_array[$element] = $count;
									   $count++; 
									}
									
								}else{
									if($no_records<$key){
											if($key<= $total_limit ){
												$user_id = 0;
												$sanitized_user_name = sanitize_user($data[$fields_array['Username']]);
												$email = $data[$fields_array['Email']];
												$firstname = $data[$fields_array['First Name']];
												$lastname = $data[$fields_array['Last Name']];
												$fullname = $data[$fields_array['First Name']].' '.$data[$fields_array['Last Name']];
												$password = $data[$fields_array['Password']];
												$website = $data[$fields_array['Website']];
												$role = "Provider";
												$problematic_row = false;
												$id_position = $positions["id"];
				   
												if(!empty($password)){
													$hash_password = wp_hash_password($password);
												}else{
													$password = '123456';
													$hash_password = wp_hash_password($password);
												}
				   
				   
												if ( !empty( $id_position ) )
													$id = $data[ $id_position ];
												else
													$id = "";

												$created = true;
				   
												if( !empty( $id ) ){ // if user have used id
													if( $this->service_finder_user_id_exists( $id ) ){
														if( $update_existing_users == 'no' ){
															continue;
														}
														// we check if username is the same than in row
														$user = get_user_by( 'ID', $id );
														if( $user->user_login == $sanitized_user_name ){
															$user_id = $id;
															if( $password !== "" )
																wp_set_password( $password, $user_id );

															if( !empty( $email ) ) {
																$updateEmailArgs = array(
																	'ID'         => $user_id,
																	'user_login'  =>  $sanitized_user_name,
																	'user_email'  =>  $email,
																	'user_nicename'  =>  service_finder_create_user_name($fullname),
																	'display_name'  =>  $fullname,
																	'user_url'  =>  esc_url($website),
																	'role'   	  =>  $role
																);
																wp_update_user( $updateEmailArgs );
															}

															$created = false;
														}
														
													}
													else{
														$userdata = array(
															'ID'		  =>  $id,
															'user_login'  =>  $sanitized_user_name,
															'user_email'  =>  $email,
															'user_pass'   =>  $hash_password,
															'user_nicename'  =>  service_finder_create_user_name($fullname),
															'display_name'  =>  $fullname,
															'user_url'  =>  esc_url($website),
															'role'   	  =>  $role
														);
														$user_id = wp_insert_user( $userdata );
														$created = true;
													}
												}
												elseif( username_exists( $sanitized_user_name ) ){ // if user exists, we take his ID by login, we will update his mail if it has changed
				   
													if( $update_existing_users == 'no' ){
														continue;
													}

													$user_object = get_user_by( "login", $sanitized_user_name );
													$user_id = $user_object->ID;

													if( $password !== "" )
														wp_set_password( $password, $user_id );

													if( !empty( $email ) ) {
														$updateEmailArgs = array(
															'ID'         => $user_id,
															'user_login'  =>  $sanitized_user_name,
															'user_email'  =>  $email,
															'user_nicename'  =>  service_finder_create_user_name($fullname),
															'display_name'  =>  $fullname,
															'user_url'  =>  esc_url($website),
															'role'   	  =>  $role
														);
														wp_update_user( $updateEmailArgs );
													}
													$created = false;
												}
												else{
													$userdata = array(
															'user_login'  =>  $sanitized_user_name,
															'user_email'  =>  $email,
															'user_pass'   =>  $password,
															'user_nicename'  =>  service_finder_create_user_name($fullname),
															'display_name'  =>  $fullname,
															'user_url'  =>  esc_url($website),
															'role'   	  =>  $role
														);
													$user_id = wp_insert_user( $userdata );
													$updateEmailArgs = array(
															'ID'         => $user_id,
															'user_url'  =>  esc_url($website),
														);
														
														wp_update_user( $updateEmailArgs );
												}
												
												if( !is_wp_error( $user_id ) ){ // in case the user is generating errors after this checks
													// User Meta data
													
													$this->service_finder_insert_data($data, $user_id, $fields_array);	
												}	   
			
												
												do_action('post_service_finder_import_single_user', $headers, $data, $user_id );
						
				
												
											 }else{
										 
											 $response['csv_num_rows'] = $csv_num_rows;
											 $response['records'] = $total_limit;
											 echo json_encode( $response );
											 die();
										 }
									}
								}
									
									
							}
						}
						else{
							foreach($data as $key => $data){
								if($key==0){
									foreach($data as $key1 => $fieldName){
										$fields_array[$fieldName] = $key1;
									}
					 
									$i = 0;
									$id_position = false;
									foreach( $data as $element ){
										$headers[] = $element;

										if( in_array( strtolower( $element ) , $service_finder_restricted_fields ) )
											$positions[ strtolower( $element ) ] = $i;

										if( !in_array( strtolower( $element ), $service_finder_restricted_fields ))
											$headers_filtered[] = $element;

										$i++;
									}
									$columns = count( $data );
									foreach ( $service_finder_restricted_fields as $service_finder_restricted_field ) {
										$positions[ $service_finder_restricted_field ] = false;
									}
									$fields_array=array();
									$count =0;
									foreach( $headers as $element ){
									   $fields_array[$element] = $count;
									   $count++; 
									}
					 
								}
								else{
									if($no_records<$key){
											if($key<= $total_limit ){
											
											  $user_id = 0;
												$sanitized_user_name = sanitize_user($data[$fields_array['Username']]);
												$email = $data[$fields_array['Email']];
												$fullname = $data[$fields_array['First Name']].' '.$data[$fields_array['Last Name']];
												$password = $data[$fields_array['Password']];
												$website = $data[$fields_array['Website']];
												$role = "Provider";
												$problematic_row = false;
												$id_position = $positions["id"];
				   
												if(!empty($password)){
													$hash_password = wp_hash_password($password);
												}else{
													$password = '123456';
													$hash_password = wp_hash_password($password);
												}
				   
				   
												if ( !empty( $id_position ) )
													$id = $data[ $id_position ];
												else
													$id = "";

												$created = true;
				   
												if( !empty( $id ) ){ // if user have used id
													if( $this->service_finder_user_id_exists( $id ) ){
														if( $update_existing_users == 'no' ){
															continue;
														}
														// we check if username is the same than in row
														$user = get_user_by( 'ID', $id );
														if( $user->user_login == $sanitized_user_name ){
															$user_id = $id;
															if( $password !== "" )
																wp_set_password( $password, $user_id );

															if( !empty( $email ) ) {
																$updateEmailArgs = array(
																	'ID'         => $user_id,
																	'user_login'  =>  $sanitized_user_name,
																	'user_email'  =>  $email,
																	'user_nicename'  =>  service_finder_create_user_name($fullname),
																	'display_name'  =>  $fullname,
																	'user_url'  =>  esc_url($website),
																	'role'   	  =>  $role
																);
																wp_update_user( $updateEmailArgs );
															}

															$created = false;
														}
														
													}
													else{
														$userdata = array(
															'ID'		  =>  $id,
															'user_login'  =>  $sanitized_user_name,
															'user_email'  =>  $email,
															'user_pass'   =>  $hash_password,
															'user_nicename'  =>  service_finder_create_user_name($fullname),
															'display_name'  =>  $fullname,
															'user_url'  =>  esc_url($website),
															'role'   	  =>  $role
														);
														$user_id = wp_insert_user( $userdata );
														$created = true;
													}
												}
				   
												elseif( username_exists( $sanitized_user_name ) ){ // if user exists, we take his ID by login, we will update his mail if it has changed
				   
													if( $update_existing_users == 'no' ){
														continue;
													}

													$user_object = get_user_by( "login", $sanitized_user_name );
													$user_id = $user_object->ID;

													if( $password !== "" )
														wp_set_password( $password, $user_id );

													if( !empty( $email ) ) {
														$updateEmailArgs = array(
															'ID'         => $user_id,
															'user_login'  =>  $sanitized_user_name,
															'user_email'  =>  $email,
															'user_nicename'  =>  service_finder_create_user_name($fullname),
															'display_name'  =>  $fullname,
															'user_url'  =>  esc_url($website),
															'role'   	  =>  $role
														);
														
														wp_update_user( $updateEmailArgs );
													}
													$created = false;
												}
												else{
													
													$userdata = array(
															'user_login'  =>  $sanitized_user_name,
															'user_email'  =>  $email,
															'user_pass'   =>  $password,
															'user_nicename'  =>  service_finder_create_user_name($fullname),
															'display_name'  =>  $fullname,
															'user_url'  =>  esc_url($website),
															'role'   	  =>  $role
														);
													$user_id = wp_insert_user( $userdata );
													$updateEmailArgs = array(
															'ID'         => $user_id,
															'user_url'  =>  esc_url($website),
														);
														
														wp_update_user( $updateEmailArgs );
												}

												if( !is_wp_error( $user_id ) ){ // in case the user is generating errors after this checks
													
													$this->service_finder_insert_data($data, $user_id, $fields_array);	
												}	   
			
												do_action('post_service_finder_import_single_user', $headers, $data, $user_id );
						
				
												 }else{
													 $response['csv_num_rows'] = $csv_num_rows;
													 $response['records'] = $total_limit;
													 echo json_encode( $response );
													 die();
											}
									}else{
										$response['response'] = "success";
										$response['total'] = "final";
										echo json_encode( $response );
										die();
									}
								}
					 		}
							$response['response'] = "success";
							$response['total'] = "final";
							echo json_encode( $response );
							die();
						}
					} else {
						$response['response'] = "ERROR";
						$response['error'] = $uploaded_file['error'];
					}
	
					echo json_encode( $response );
					die();
			 	}
			 }
		}
 	}
    
    
    public function service_finder_keep_session_alive(){
    
    global $service_finder_Tables, $wpdb;
		$service_finder_options = get_option('service_finder_options');
		$wp_users_fields = array( "id", "user_login", "user_pass", "user_email", "first_name", "last_name",  "user_nicename", "display_name", "role", "user_url" );
		$wp_users_post_fields = array( "post_title", "post_status", "post_type");
		$wp_user_meta_fields = array( "comment_post", "booking_option");
		$wp_providers_fields = array( "wp_user_id", "admin_moderation", "account_blocked", "company_name", "full_name", "email",  "phone", "category_id", "address", "apt", "city", "state", "zipcode", "country",  "lat", "long" );
		$wp_job_limits_post_fields = array( "provider_id", "free_limits", "available_limits", "membership_date", "start_date", "expire_date");
		$wp_req_fields = array("Username", "Email");
		$service_finder_restricted_fields = array_merge( $wp_users_fields, $wp_users_post_fields, $wp_user_meta_fields, $wp_providers_fields, $wp_job_limits_post_fields, $wp_req_fields );
		$limit = 100;
	
		if(isset($_POST['url']) && !empty($_POST['url'])){
			$file = (isset($_POST['url'])) ? $_POST['url']: '';
			$csv_complete_contents = array_map('str_getcsv', file($file));
			$data = $csv_complete_filtered = array_filter(array_map('array_filter', $csv_complete_contents));
			$csv_num_rows = count($csv_complete_filtered) -1;
			
			
    	}else{
    		if(isset($_FILES['file']) && !empty($_FILES['file'])){
				$service_finder_options = get_option('service_finder_options');
				$update_existing_users = (isset($_POST['update_existing_users'])) ? esc_html($_POST['update_existing_users']) : '';
				$no_records = (isset($_POST['no_records'])) ? esc_html($_POST['no_records']) : 0;
				$uploadfiles = (isset($_FILES['file'])) ? $_FILES['file']: '';
				if ( is_array($uploadfiles) ) {
					if ($uploadfiles['error'] == 0) {
						$filetmp = $uploadfiles['tmp_name'];
						$filename = $uploadfiles['name'];
					    $filetype = wp_check_filetype( basename( $filename ), array('csv' => 'text/csv') );
					    $filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
					    $filename = $filetitle . '.' . $filetype['ext'];
					    if ($filetype['ext'] != "csv") {
						  $response['csv'] = 'File must be a CSV';
						  die();
					    }
					    $uploaded_file = wp_handle_upload( $uploadfiles, array( 'test_form' => false ) );
					    if( $uploaded_file && ! isset( $uploaded_file['error'] ) ) {
							$file = $response['url'] = $uploaded_file['url'];
							$csv_complete_contents = array_map('str_getcsv', file($file));
							$data = $csv_complete_filtered = array_filter(array_map('array_filter', $csv_complete_contents));
							$csv_num_rows = count($csv_complete_filtered)-1;
						}	
					   $response['url'] = $uploaded_file['url'];
					}
				}	
			}else{
				$response['response'] = "ERROR";
			
			}
    	} 
		 $no_records = (isset($_POST['no_records'])) ? $_POST['no_records']: '0';
		 
		 $total_limit = $no_records+$limit;
		
		 if($csv_num_rows > $total_limit){
			 $fields_array=array();
			 foreach($data as $key => $data){
				 if($key==0){
					 foreach($data as $key1 => $fieldName){
						 $fields_array[$fieldName] = $key1;
					 }
					 
					 $i = 0;
					 $id_position = false;
					 foreach( $data as $element ){
						 $headers[] = $element;

						 if( in_array( strtolower( $element ) , $service_finder_restricted_fields ) )
							 $positions[ strtolower( $element ) ] = $i;

						 if( !in_array( strtolower( $element ), $service_finder_restricted_fields ))
							 $headers_filtered[] = $element;

						 $i++;
					 }
					 $columns = count( $data );
					 foreach ( $service_finder_restricted_fields as $service_finder_restricted_field ) {
						 $positions[ $service_finder_restricted_field ] = false;
					 }
					 $fields_array=array();
					 $count =0;
					 foreach( $headers as $element ){
						$fields_array[$element] = $count;
						$count++; 
					 }
					 
				 }else{
					 if($no_records<$key){
							 if($key<= $total_limit ){
								$user_id = 0;
								$sanitized_user_name = sanitize_user($data[$fields_array['Username']]);
								$email = $data[$fields_array['Email']];
								$fullname = $data[$fields_array['First Name']].' '.$data[$fields_array['Last Name']];
								$password = $data[$fields_array['Password']];
								$website = $data[$fields_array['Website']];
								$role = "Provider";
								$problematic_row = false;
								$id_position = $positions["id"];
   
								if(!empty($password)){
									$hash_password = wp_hash_password($password);
								}else{
									$password = '123456';
									$hash_password = wp_hash_password($password);
								}
   
   
								if ( !empty( $id_position ) )
									$id = $data[ $id_position ];
								else
									$id = "";

								$created = true;
   
								if( !empty( $id ) ){ // if user have used id
									if( $this->service_finder_user_id_exists( $id ) ){
										if( $update_existing_users == 'no' ){
											continue;
										}
										// we check if username is the same than in row
										$user = get_user_by( 'ID', $id );
										if( $user->user_login == $sanitized_user_name ){
											$user_id = $id;
											if( $password !== "" )
												wp_set_password( $password, $user_id );

											if( !empty( $email ) ) {
												$updateEmailArgs = array(
													'ID'         => $user_id,
													'user_login'  =>  $sanitized_user_name,
													'user_email'  =>  $email,
													'user_nicename'  =>  service_finder_create_user_name($fullname),
													'display_name'  =>  $fullname,
													'user_url'  =>  esc_url($website),
													'role'   	  =>  $role
												);
												wp_update_user( $updateEmailArgs );
											}

											$created = false;
										}
										
									}
									else{
										$userdata = array(
											'ID'		  =>  $id,
											'user_login'  =>  $sanitized_user_name,
											'user_email'  =>  $email,
											'user_pass'   =>  $hash_password,
											'user_nicename'  =>  service_finder_create_user_name($fullname),
											'display_name'  =>  $fullname,
											'user_url'  =>  esc_url($website),
											'role'   	  =>  $role
										);
										$user_id = wp_insert_user( $userdata );
										$created = true;
									}
								}
   
								elseif( username_exists( $sanitized_user_name ) ){ // if user exists, we take his ID by login, we will update his mail if it has changed
   
									if( $update_existing_users == 'no' ){
										continue;
									}

									$user_object = get_user_by( "login", $sanitized_user_name );
									$user_id = $user_object->ID;

									if( $password !== "" )
										wp_set_password( $password, $user_id );

									if( !empty( $email ) ) {
										$updateEmailArgs = array(
											'ID'         => $user_id,
											'user_login'  =>  $sanitized_user_name,
											'user_email'  =>  $email,
											'user_nicename'  =>  service_finder_create_user_name($fullname),
											'display_name'  =>  $fullname,
											'user_url'  =>  esc_url($website),
											'role'   	  =>  $role
										);
										wp_update_user( $updateEmailArgs );
									}
									$created = false;
								}
								else{
									$userdata = array(
											'user_login'  =>  $sanitized_user_name,
											'user_email'  =>  $email,
											'user_pass'   =>  $password,
											'user_nicename'  =>  service_finder_create_user_name($fullname),
											'display_name'  =>  $fullname,
											'user_url'  =>  esc_url($website),
											'role'   	  =>  $role
										);
									$user_id = wp_insert_user( $userdata );
									$updateEmailArgs = array(
															'ID'         => $user_id,
															'user_url'  =>  esc_url($website),
														);
														
														wp_update_user( $updateEmailArgs );
								}
								
								if( !is_wp_error( $user_id ) ){ // in case the user is generating errors after this checks
									$this->service_finder_insert_data($data, $user_id, $fields_array);	
								}	   

							
								do_action('post_service_finder_import_single_user', $headers, $data, $user_id );
		
							 }else{
							 	 $response['url'] = $file;
								 $response['csv_num_rows'] = $csv_num_rows;
								 $response['records'] = $total_limit;
								 echo json_encode( $response );
								 die();
							 }
					 }
				 }
					 
					 
			 }
		 }
		 else{
		 	 foreach($data as $key => $data){
				 if($key==0){
					 foreach($data as $key1 => $fieldName){
						 $fields_array[$fieldName] = $key1;
					 }
					 
					 $i = 0;
					 $id_position = false;
					 foreach( $data as $element ){
						 $headers[] = $element;

						 if( in_array( strtolower( $element ) , $service_finder_restricted_fields ) )
							 $positions[ strtolower( $element ) ] = $i;

						 if( !in_array( strtolower( $element ), $service_finder_restricted_fields ))
							 $headers_filtered[] = $element;

						 $i++;
					 }
					 $columns = count( $data );
					 foreach ( $service_finder_restricted_fields as $service_finder_restricted_field ) {
						 $positions[ $service_finder_restricted_field ] = false;
					 }
					 $fields_array=array();
					 $count =0;
					 foreach( $headers as $element ){
						$fields_array[$element] = $count;
						$count++; 
					 }
					 
				 }
				 else{
			  
					   if($key<= $total_limit ){
						   $user_id = 0;
						   $sanitized_user_name = sanitize_user($data[$fields_array['Username']]);
						   $email = $data[$fields_array['Email']];
						   $fullname = $data[$fields_array['First Name']].' '.$data[$fields_array['Last Name']];
						   $password = $data[$fields_array['Password']];
						   $website = $data[$fields_array['Website']];
						   $role = "Provider";
						   $problematic_row = false;
						   $id_position = $positions["id"];

						   if(!empty($password)){
							   $hash_password = wp_hash_password($password);
						   }else{
							   $password = '123456';
							   $hash_password = wp_hash_password($password);
						   }


						   if ( !empty( $id_position ) )
							   $id = $data[ $id_position ];
						   else
							   $id = "";

						   $created = true;

						   if( !empty( $id ) ){ // if user have used id
							   if( $this->service_finder_user_id_exists( $id ) ){
								   if( $update_existing_users == 'no' ){
									   continue;
								   }
								   // we check if username is the same than in row
								   $user = get_user_by( 'ID', $id );
								   if( $user->user_login == $sanitized_user_name ){
									   $user_id = $id;
									   if( $password !== "" )
										   wp_set_password( $password, $user_id );

									   if( !empty( $email ) ) {
										   $updateEmailArgs = array(
											   'ID'         => $user_id,
											   'user_login'  =>  $sanitized_user_name,
											   'user_email'  =>  $email,
											   'user_nicename'  =>  service_finder_create_user_name($fullname),
											   'display_name'  =>  $fullname,
											   'user_url'  =>  esc_url($website),
											   'role'   	  =>  $role
										   );
										   wp_update_user( $updateEmailArgs );
									   }

									   $created = false;
								   }
								  
							   }
							   else{
								   $userdata = array(
									   'ID'		  =>  $id,
									   'user_login'  =>  $sanitized_user_name,
									   'user_email'  =>  $email,
									   'user_pass'   =>  $hash_password,
									   'user_nicename'  =>  service_finder_create_user_name($fullname),
									   'display_name'  =>  $fullname,
									   'user_url'  =>  esc_url($website),
									   'role'   	  =>  $role
								   );
								   $user_id = wp_insert_user( $userdata );
								   $created = true;
							   }
						   }

						   elseif( username_exists( $sanitized_user_name ) ){ // if user exists, we take his ID by login, we will update his mail if it has changed

							   if( $update_existing_users == 'no' ){
								   continue;
							   }

							   $user_object = get_user_by( "login", $sanitized_user_name );
							   $user_id = $user_object->ID;

							   if( $password !== "" )
								   wp_set_password( $password, $user_id );

							   if( !empty( $email ) ) {
								   $updateEmailArgs = array(
									   'ID'         => $user_id,
									   'user_login'  =>  $sanitized_user_name,
									   'user_email'  =>  $email,
									   'user_nicename'  =>  service_finder_create_user_name($fullname),
									   'display_name'  =>  $fullname,
									   'user_url'  =>  esc_url($website),
									   'role'   	  =>  $role
								   );
								   wp_update_user( $updateEmailArgs );
							   }
							   $created = false;
						   }
						   else{
							  
							   $userdata = array(
										'user_login'  =>  $sanitized_user_name,
										'user_email'  =>  $email,
										'user_pass'   =>  $password,
										'user_nicename'  =>  service_finder_create_user_name($fullname),
										'display_name'  =>  $fullname,
										'user_url'  =>  esc_url($website),
										'role'   	  =>  $role
									);
								$user_id = wp_insert_user( $userdata );
							   $updateEmailArgs = array(
															'ID'         => $user_id,
															'user_url'  =>  esc_url($website),
														);
														
														wp_update_user( $updateEmailArgs );
						   }
						   if( !is_wp_error( $user_id ) ){ // in case the user is generating errors after this checks
							  $this->service_finder_insert_data($data, $user_id, $fields_array);	
						   }	   

			
						   do_action('post_service_finder_import_single_user', $headers, $data, $user_id );
					  
						}else{
						   $response['csv_num_rows'] = $csv_num_rows;
						   $response['records'] = $total_limit;
						   echo json_encode( $response );
						   die();
					   }
					 
				 }
			 }
		
			 $response['response'] = "success";
			 $response['total'] = "final";
		 	 echo json_encode( $response );
			 die();
		 }
    }
    
    
	public function service_finder_insert_data($data, $user_id, $fields_array){
		
		global $service_finder_Tables, $wpdb;
		$service_finder_options = get_option('service_finder_options');
		$sanitized_user_name = sanitize_user($data[$fields_array['Username']]);
		$email = $data[$fields_array['Email']];
		$firstname = $data[$fields_array['First Name']];
		$lastname = $data[$fields_array['Last Name']];
		$imageurl = $data[$fields_array['Profile Image Url']];
		$fullname = $data[$fields_array['First Name']].' '.$data[$fields_array['Last Name']];
		$signup_address = $data[$fields_array['Address']];
		$gender = $data[$fields_array['Gender']];
		$signup_city = $data[$fields_array['City']];
		$signup_state = $data[$fields_array['State']];
		$signup_country = $data[$fields_array['Country']];
		$signup_zip = $data[$fields_array['Zip code']];
		
		if($data[$fields_array['Admin Moderation']]=="approved"){
		   $account_moderation = "approved";
		}else if($data[$fields_array['Admin Moderation']]=="pending"){
		   $account_moderation = "pending";
		}else{
		   $account_moderation ="pending";
		}
		if($data[$fields_array['Account Blocked']]=="yes"){
		   $signup_account_blocked = "yes";
		}else if($data[$fields_array['Account Blocked']]=="no"){
		   $signup_account_blocked = "no";
		}else{
		   $signup_account_blocked ="no";
		}
		$signup_company_name = isset($data[$fields_array['Company Name']]) ? $data[$fields_array['Company Name']] : '';

	
		// update usermeta 
		update_user_meta($user_id, 'first_name', esc_attr($firstname));

		update_user_meta($user_id, 'last_name', esc_attr($lastname));

		update_user_meta($user_id, 'nickname', esc_attr($sanitized_user_name));
		
		update_user_meta($user_id,'gender',esc_attr($gender));
		
		$getProviders = $wpdb->get_row( "SELECT wp_user_id FROM $service_finder_Tables->providers WHERE wp_user_id = ".$user_id );
		
		$full_address = $signup_address.' '.$signup_city.' '.$signup_country;
		$address = str_replace(" ","+",$full_address);
		
		if(!empty($getProviders)){
			$currentaddress = $getProviders->address;
			$currentcity = $getProviders->city;
			$currentstate = $getProviders->state;
			$currentcountry = $getProviders->country;
			$currentlat = $getProviders->lat;
			$currentlong = $getProviders->long;
			
			if($currentlat == '' || $currentlong = ''){
			$address = str_replace(" ","+",$full_address);
			$res = service_finder_getLatLong($address);
			$lat = $res['lat'];
			$lng = $res['lng'];
			}elseif($currentaddress != $signup_address || $currentcity != $signup_city || $currentstate != $signup_state || $currentcountry != $signup_country ){
			$address = str_replace(" ","+",$full_address);
			$res = service_finder_getLatLong($address);
			$lat = $res['lat'];
			$lng = $res['lng'];
			}else{
			$lat = $currentlat;
			$lng = $currentlong;
			}
		}else{
			$res = service_finder_getLatLong($address);
			$lat = $res['lat'];
			$lng = $res['lng'];
		}
	
		//Provider Array
		$providerData = array(

			   'wp_user_id' => $user_id,

			   'admin_moderation' => $account_moderation,

			   'account_blocked' => $signup_account_blocked,

			   'company_name' => $signup_company_name,
			   
			   'gender' => $gender,

			   'full_name' => $fullname,

			   'email' => esc_attr($email),

			   'phone' => esc_attr($data[$fields_array['Phone']]),
			   
			   'website' => esc_attr($data[$fields_array['Website']]),

			   'category_id' => esc_attr($data[$fields_array['Categories ID']]),

			   'address' => esc_attr($signup_address),

			   'apt' => esc_attr($data[$fields_array['apt']]),

			   'city' => esc_attr($signup_city),

			   'state' => esc_attr($signup_state),

			   'zipcode' => esc_attr($signup_zip),

			   'country' => esc_attr($signup_country),

			   'lat' => $lat,

			   'long' => $lng,

		   );
	  
		$users_registered[] = $user_id;
		$user = new WP_User( $user_id );
		$user->set_role('Provider');

		// role set usermeta
		$role = $data[$fields_array['Package']]; 
		if(empty($role)){
			$role = "package_0";
		}
		$roleNum = intval(substr($role, 8));
		switch ($role) {
			case "package_0":
				if(isset($service_finder_options['package0-expday'])) {
					$expire_limit = $service_finder_options['package0-expday'];
				}
				break;
			case "package_1":
				if(isset($service_finder_options['package1-price'])) {
					$free = false;
					$packageName = $service_finder_options['package1-name'];
					$expire_limit = $service_finder_options['package1-expday'];
					$price = trim($service_finder_options['package1-price']);								
				}
				break;
			case "package_2":
				if(isset($service_finder_options['package2-price'])) {
					$expire_limit = $service_finder_options['package2-expday'];
					$free = false;
					$packageName = $service_finder_options['package2-name'];
					$price = trim($service_finder_options['package2-price']);								
				}
				break;
			case "package_3":
				if(isset($service_finder_options['package3-price'])) {
					$expire_limit = $service_finder_options['package3-expday'];
					$free = false;
					$packageName = $service_finder_options['package3-name'];
					$price = trim($service_finder_options['package3-price']);								
				}
				break;
			default:
				 $expire_limit = $service_finder_options['package0-expday'];
				break;
		}

		$roleNum = intval(substr($role, 8));
		$roleName = $service_finder_options['package'.$roleNum.'-name'];
		if($expire_limit > 0){
			 update_user_meta( $user_id, 'expire_limit', $expire_limit);
		}
		update_user_meta( $user_id, 'provider_role', $role );
		update_user_meta( $user_id, 'created_by', 'admin' );

		if($roleNum == 0 || empty($role)){
			update_user_meta($user_id, 'trial_package', 'yes');
		}

		//add usermeta data 
		update_user_meta($user_id,'primary_category',$data[$fields_array['Primary Category ID']]);
	  		
		//Insert and update job limit tables
		$jobLimitData = array();
		if(isset($role)){
			  if ($role == "package_0" || $role == "package_1" || $role == "package_2" || $role == "package_3"){
					 $packageNum = intval(substr($role, 8));
					 $allowedjobapply = (!empty($service_finder_options['package'.$packageNum.'-job-apply'])) ? $service_finder_options['package'.$packageNum.'-job-apply'] : '';
					 
					 $period = (!empty($service_finder_options['job-apply-limit-period'])) ? $service_finder_options['job-apply-limit-period'] : '';
					 $numberofweekmonth = (!empty($service_finder_options['job-apply-number-of-week-month'])) ? $service_finder_options['job-apply-number-of-week-month'] : 1;
					 $numberofperiod = (!empty($service_finder_options['job-apply-number-of-week-month'])) ? $service_finder_options['job-apply-number-of-week-month'] : '';
					 $startdate = date('Y-m-d h:i:s');

					 if($period == 'weekly'){
						 $freq = 7 * $numberofweekmonth;
						 $expiredate = date('Y-m-d h:i:s', strtotime("+".$freq." days"));
					 }elseif($period == 'monthly'){
						 $freq = 30 * $numberofweekmonth;
						 $expiredate = date('Y-m-d h:i:s', strtotime("+".$freq." days"));
					 }
					 
					 $jobLimitData = array(
						'provider_id' => $user_id,
						'free_limits' => $allowedjobapply,
						'available_limits' => $allowedjobapply,
						'membership_date' => $startdate,
						'start_date' => $startdate,
						'expire_date' => $expiredate,
					 );
			  }
			  update_user_meta( $user_id, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
		}
		 
		// Insert and update team members tables
		$memberData = array(

			'member_name' => esc_attr($fullname),

			'email' => esc_attr($email),

			'admin_wp_id' => esc_attr($user_id),

			'is_admin' => 'yes',

		 );
		 
		if(!empty($getProviders)){
		
			 // Update Provider
			 $whereProvider = array( 'wp_user_id' => $user_id);
			 $wpdb->update($service_finder_Tables->providers,wp_unslash($providerData),$whereProvider);
			 
			 // Update Job limit
			 if(!empty($jobLimitData)){
				  $whereJobLimit = array('provider_id' => $user_id);
				  $wpdb->update($service_finder_Tables->job_limits,wp_unslash($jobLimitData),$whereJobLimit);
			 }
			 //Update Members
			 $whereUserMembers = array('admin_wp_id' => $user_id);
			 $wpdb->update($service_finder_Tables->team_members,wp_unslash($memberData),$whereUserMembers);
			 
			  if($imageurl != '')
			  {
			  service_finder_upload_import_image($user_id,$imageurl);
			  }
		
		}
		else{
			  // insert Provider
			  $wpdb->insert($service_finder_Tables->providers,wp_unslash($providerData));
			  
			  if(!empty($jobLimitData)){
				  // Insert Job limit
				  $wpdb->insert($service_finder_Tables->job_limits,wp_unslash($jobLimitData));
			  }
		 
			  //Insert Members
			  $wpdb->insert($service_finder_Tables->team_members,wp_unslash($memberData));
			  
			  // Create a post for user
			  $comment_post = array(
				  'post_title' => $fullname,
				  'post_status' => 'publish',
				  'post_type' => 'sf_comment_rating',
				  'comment_status' => 'open',
			  );

			  $postid = wp_insert_post( $comment_post );
			  update_user_meta($user_id, 'comment_post', $postid);
			  
			  if($imageurl != '')
			  {
			  service_finder_upload_import_image($user_id,$imageurl);
			  }
			  
			  if(service_finder_availability_method($user_id) == 'timeslots')
			  {
			    service_finder_create_default_timeslots($user_id);
			  }elseif(service_finder_availability_method($user_id) == 'starttime')
			  {
				service_finder_create_default_starttime($user_id);
			  }
			  
			  $userslots = get_user_meta($user_id, 'timeslots', true);
			  if($userslots == ""){
			  	service_finder_set_default_business_hours($user_id);
			  }
				
			  service_finder_set_default_booking_settings($user_id);
		 }
}

    
    
	function service_finder_detect_delimiter($file){
	$handle = @fopen($file, "r");
	$sumComma = 0;
	$sumSemiColon = 0;
	$sumBar = 0; 

    if($handle){
    	while (($data = fgets($handle, 4096)) !== FALSE):
	        $sumComma += substr_count($data, ",");
	    	$sumSemiColon += substr_count($data, ";");
	    	$sumBar += substr_count($data, "|");
	    endwhile;
    }
    fclose($handle);
    
    if(($sumComma > $sumSemiColon) && ($sumComma > $sumBar))
    	return ",";
    else if(($sumSemiColon > $sumComma) && ($sumSemiColon > $sumBar))
    	return ";";
    else 
    	return "|";
}



    
	function service_finder_upload_csv_file($uploadfiles){
  
  if ( is_array($uploadfiles) ) {

		foreach ( $uploadfiles['name'] as $key => $value ) {

		  // look only for uploded files
		  if ($uploadfiles['error'][$key] == 0) {
			$filetmp = $uploadfiles['tmp_name'][$key];

			//clean filename and extract extension
			$filename = $uploadfiles['name'][$key];

			// get file info
			// @fixme: wp checks the file extension....
			$filetype = wp_check_filetype( basename( $filename ), array('csv' => 'text/csv') );
			$filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
			$filename = $filetitle . '.' . $filetype['ext'];
			$upload_dir = wp_upload_dir();
			
			if ($filetype['ext'] != "csv") {
			  wp_die('File must be a CSV');
			  return;
			}

			/**
			 * Check if the filename already exist in the directory and rename the
			 * file if necessary
			 */
			$i = 0;
			while ( file_exists( $upload_dir['path'] .'/' . $filename ) ) {
			  $filename = $filetitle . '_' . $i . '.' . $filetype['ext'];
			  $i++;
			}
			$filedest = $upload_dir['path'] . '/' . $filename;

			/**
			 * Check write permissions
			 */
			if ( !is_writeable( $upload_dir['path'] ) ) {
			  wp_die( __( 'Unable to write to directory. Is this directory writable by the server?', 'service-finder' ));
			  return;
			}

			/**
			 * Save temporary file to uploads dir
			 */
			if ( !@move_uploaded_file($filetmp, $filedest) ){
			  wp_die( __( 'Error, the file', 'service-finder' ) . " $filetmp " . __( 'could not moved to', 'service-finder' ) . " : $filedest");
			  continue;
			}

			$attachment = array(
			  'post_mime_type' => $filetype['type'],
			  'post_title' => $filetitle,
			  'post_content' => '',
			  'post_status' => 'inherit'
			);
			
			$attach_id = wp_insert_attachment( $attachment, $filedest );
			
			require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $filedest );
			wp_update_attachment_metadata( $attach_id,  $attach_data );
			
		  }
		}
	  }
 	echo $filedest;
  
  
  }  
  
  	
	function service_finder_string_conversion( $string ){
		if(!preg_match('%(?:
		[\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
		|\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
		|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
		|\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
		|\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
		|[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
		|\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
		)+%xs', $string)){
			return utf8_encode($string);
		}
		else
			return $string;
	}

	// wp-access-areas functions
	function service_finder_set_cap_for_user( $capability , &$user , $add ) {
	   $has_cap = $user->has_cap( $capability );
	   $is_change = ($add && ! $has_cap) || (!$add && $has_cap);
	   if ( $is_change ) {
		   if ( $add ) {
			   $user->add_cap( $capability , true );
			   do_action( 'sf_grant_access' , $user , $capability );
			   do_action( "sf_grant_{$capability}" , $user );
		   } else if ( ! $add ) {
			   $user->remove_cap( $capability );
			   do_action( 'sf_revoke_access' , $user , $capability );
			   do_action( "sf_revoke_{$capability}" , $user );
		   }
	   }
   }


	function service_finder_user_id_exists( $user_id ){
		if ( get_userdata( $user_id ) === false )
			return false;
		else
			return true;
	}

	function service_finder_old_email( $email ) {
		if ( ! is_email( $email ) ) {
			return;
		}

		$old_email = $email;

		for ( $i = 0; ! $skip_remap && email_exists( $email ); $i++ ) {
			$email = str_replace( '@', "+ama{$i}@", $old_email );
		}

		return $email;
	}
    
	function service_finder_restore_email_address( $user_id, $email ) {
		global $wpdb;

		$wpdb->update(
			$wpdb->users,
			array( 'user_email' => $email ),
			array( 'ID' => $user_id )
		);

		clean_user_cache( $user_id );	
	}

	function service_finder_get_roles($user_id){
		$roles = array();
		$user = new WP_User( $user_id );

		if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
			foreach ( $user->roles as $role )
				$roles[] = $role;
		}

		return $roles;
	}
	
}