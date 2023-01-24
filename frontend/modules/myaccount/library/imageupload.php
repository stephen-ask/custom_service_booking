<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'SERVICE_FINDER_ImageSpace' ) )
{
	class SERVICE_FINDER_ImageSpace extends SERVICE_FINDER_FileSpace
	{
		/**
		 * Enqueue scripts and styles
		 *
		 * @return void
		 */
		static function admin_enqueue_scripts()
		{
			// Enqueue same scripts and styles as for file field
			parent::admin_enqueue_scripts();

			wp_enqueue_style( 'sf-image', SERVICE_FINDER_BOOKING_ASSESTS_URL . '/manage-uploads/image-upload.css', array(), RWMB_VER );
			wp_enqueue_script( 'sf-image', SERVICE_FINDER_BOOKING_ASSESTS_URL . '/manage-uploads/image-upload.js', array( 'jquery-ui-sortable' ), RWMB_VER, true );
		}

		/**
		 * Add actions
		 *
		 * @return void
		 */
		static function add_actions()
		{
			// Do same actions as file field
			parent::add_actions();

			// Reorder images via Ajax
			add_action( 'wp_ajax_rwmb_rearrangeimages', array( __CLASS__, 'wp_ajax_rearrangeimages' ) );
		}

		/**
		 * Ajax callback for reordering images
		 *
		 * @return void
		 */
		static function wp_ajax_rearrangeimages()
		{
			$field_id = isset( $_POST['field_id'] ) ? $_POST['field_id'] : 0;
			$order    = isset( $_POST['order'] ) ? $_POST['order'] : '';
			$post_id  = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

			check_ajax_referer( "rwmb-reorder-images_{$field_id}" );

			parse_str( $order, $items );

			delete_post_meta( $post_id, $field_id );
			foreach ( $items['item'] as $item )
			{
				add_post_meta( $post_id, $field_id, $item, false );
			}
			wp_send_json_success();
		}

		/**
		 * Get field HTML
		 *
		 * @param mixed $meta
		 * @param array $field
		 *
		 * @return string
		 */
		static function html( $meta, $field )
		{
			$i18n_title = apply_filters( 'rwmb_image_upload_string', _x( 'Upload Images', 'image upload', 'service-finder' ), $field );
			$i18n_more  = apply_filters( 'rwmb_image_add_string', _x( '+ Add new image', 'image upload', 'service-finder' ), $field );

			// Uploaded images
			$html = self::get_uploaded_images( $meta, $field );

			// Show form upload
			$html .= sprintf(
				'<h4>%s</h4>
				<div class="new-files">
					<div class="file-input"><input type="file" name="%s[]" /></div>
					<a class="rwmb-add-file" href="javascript:;"><strong>%s</strong></a>
				</div>',
				$i18n_title,
				$field['id'],
				$i18n_more
			);

			return $html;
		}

		/**
		 * Get HTML markup for uploaded images
		 *
		 * @param array $images
		 * @param array $field
		 *
		 * @return string
		 */
		static function get_uploaded_images( $images, $field )
		{
			$reorder_nonce = wp_create_nonce( "rwmb-reorder-images_{$field['id']}" );
			$delete_nonce  = wp_create_nonce( "rwmb-delete-file_{$field['id']}" );
			$classes       = array( 'rwmb-images', 'rwmb-uploaded' );
			if ( count( $images ) <= 0 )
				$classes[] = 'hidden';
			$ul   = '<ul class="%s" data-field_id="%s" data-delete_nonce="%s" data-reorder_nonce="%s" data-force_delete="%s" data-max_file_uploads="%s">';
			$html = sprintf(
				$ul,
				implode( ' ', $classes ),
				$field['id'],
				$delete_nonce,
				$reorder_nonce,
				$field['force_delete'] ? 1 : 0,
				$field['max_file_uploads']
			);
			foreach ( $images as $image )
			{
				if($field['id'] == 'plupload'){
					$html .= self::img_custom_html( $image );
				}elseif($field_id == 'sfidentityuploader'){
					wp_send_json_success( self::img_identity_html( $id ) );
				}elseif($field_id == 'quoteuploader'){
					wp_send_json_success( self::img_quote_html( $id ) );
				}elseif($field_id == 'sfmemberavatarupload'){
					wp_send_json_success( self::img_member_html( $id ) );
				}elseif($field_id == 'sfmemberavataruploadedit'){
					wp_send_json_success( self::img_memberedit_html( $id ) );
				}elseif($field_id == 'coverimageuploader'){
					wp_send_json_success( self::img_coverimage_html( $id ) );
				}elseif($field_id == 'certificate' || $field_id == 'certificateedit'){
					wp_send_json_success( self::img_certificate_html( $id ) );
				}elseif($field_id == 'stripeidentity'){
					wp_send_json_success( self::img_stripeidentity_html( $id ) );
				}elseif($field_id == 'articlefeatured' || $field_id == 'articlefeatureedit'){
					wp_send_json_success( self::img_articlefeatured_html( $id ) );	
				}elseif($field['id'] == 'sffileuploader'){
					wp_send_json_success( self::img_file_html( $id ) );
				}elseif($field_id == 'jobgallery'){
					wp_send_json_success( self::img_jobgallery_html( $id ) );
				}else{
					wp_send_json_success( self::img_html( $id ) );
				}
				
			}

			$html .= '</ul>';

			return $html;
		}

		/**
		 * Get HTML markup for ONE uploaded image
		 *
		 * @param int $image Image ID
		 *
		 * @return string
		 */
		static function img_custom_html( $image )
		{
			$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
			$i18n_edit   = apply_filters( 'rwmb_image_edit_string', _x( 'Edit', 'image upload', 'service-finder' ) );
			$arr  = self::get_icon_for_attachment($image);
			$li          = '
				<li id="item_%s">
					<img src="%s" />
					<div class="rwmb-image-bar">
						<a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
						<input type="hidden" name="%s[]" value="%s">
					</div>
				</li>
			';

			
			
			$link = get_edit_post_link( $image );

			return sprintf(
				$li,
				esc_attr($image),
				esc_url($arr['src']),
				esc_attr($i18n_delete), esc_attr($image), $arr['filename'], esc_attr($image)
			);
		}
		
		static function img_jobgallery_html( $image )
		{
			$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
			$i18n_edit   = apply_filters( 'rwmb_image_edit_string', _x( 'Edit', 'image upload', 'service-finder' ) );
			$arr  = self::get_icon_for_attachment($image);
			$li          = '
				<li id="item_%s">
					<img src="%s" />
					<div class="rwmb-image-bar">
						<a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
						<input type="hidden" name="jobgalleryattachmentid[]" value="%s">
					</div>
				</li>
			';

			$link = get_edit_post_link( $image );

			return sprintf(
				$li,
				esc_attr($image),
				esc_url($arr['src']),
				esc_attr($i18n_delete), esc_attr($image), esc_attr($image)
			);
		}
		
		static function img_identity_html( $image )
		{
			$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
			$i18n_edit   = apply_filters( 'rwmb_image_edit_string', _x( 'Edit', 'image upload', 'service-finder' ) );
			$arr  = self::get_icon_for_attachment($image);
			$li          = '
				<li id="item_%s">
					<img src="%s" />
					<div class="rwmb-image-bar">
						<a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
						<input type="hidden" name="identityattachmentid[]" value="%s">
					</div>
				</li>
			';

			$link = get_edit_post_link( $image );

			return sprintf(
				$li,
				esc_attr($image),
				esc_url($arr['src']),
				esc_attr($i18n_delete), esc_attr($image), esc_attr($image)
			);
		}
		
		static function img_quote_html( $image )
		{
			$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
			$i18n_edit   = apply_filters( 'rwmb_image_edit_string', _x( 'Edit', 'image upload', 'service-finder' ) );
			$arr  = self::get_icon_for_attachment($image);
			$li          = '
				<li id="item_%s">
					<img src="%s" />
					<div class="rwmb-image-bar">
						<a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
						<input type="hidden" name="quoteattachmentid[]" value="%s">
					</div>
				</li>
			';

			$link = get_edit_post_link( $image );

			return sprintf(
				$li,
				esc_attr($image),
				esc_url($arr['src']),
				esc_attr($i18n_delete), esc_attr($image), esc_attr($image)
			);
		}
		
		/**
		 * Get HTML markup for ONE uploaded image
		 *
		 * @param int $image Image ID
		 *
		 * @return string
		 */
		static function img_html( $image )
		{
			$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
			$i18n_edit   = apply_filters( 'rwmb_image_edit_string', _x( 'Edit', 'image upload', 'service-finder' ) );
			$li          = '
				<li id="item_%s">
					<img src="%s" />
					<div class="rwmb-image-bar">
						<a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
						<input type="hidden" name="plavatar" value="%s">
					</div>
				</li>
			';

			$src  = wp_get_attachment_image_src( $image, 'thumbnail' );
			$src  = $src[0];
			$link = get_edit_post_link( $image );

			return sprintf(
				$li,
				esc_attr($image),
				esc_url($src),
				esc_attr($i18n_delete), esc_attr($image), esc_attr($image)
			);
		}
		
		static function img_member_html( $image )
		{
			$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
			$i18n_edit   = apply_filters( 'rwmb_image_edit_string', _x( 'Edit', 'image upload', 'service-finder' ) );
			$li          = '
				<li id="item_%s">
					<img src="%s" />
					<div class="rwmb-image-bar">
						<a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
						<input type="hidden" name="sfmemberavatar" value="%s">
					</div>
				</li>
			';

			$src  = wp_get_attachment_image_src( $image, 'thumbnail' );
			$src  = $src[0];
			$link = get_edit_post_link( $image );

			return sprintf(
				$li,
				esc_attr($image),
				esc_url($src),
				esc_attr($i18n_delete), esc_attr($image), esc_attr($image)
			);
		}
		
		static function img_coverimage_html( $image )
		{
			$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
			$i18n_edit   = apply_filters( 'rwmb_image_edit_string', _x( 'Edit', 'image upload', 'service-finder' ) );
			$li          = '
				<li id="item_%s">
					<img src="%s" />
					<div class="rwmb-image-bar">
						<a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
						<input type="hidden" name="coverimageattachmentid[]" value="%s">
					</div>
				</li>
			';

			$src  = wp_get_attachment_image_src( $image, 'thumbnail' );
			$src  = $src[0];
			$link = get_edit_post_link( $image );

			return sprintf(
				$li,
				esc_attr($image),
				esc_url($src),
				esc_attr($i18n_delete), esc_attr($image), esc_attr($image)
			);
		}
		
		static function img_certificate_html( $image )
		{
			$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
			$i18n_edit   = apply_filters( 'rwmb_image_edit_string', _x( 'Edit', 'image upload', 'service-finder' ) );
			$arr  = self::get_icon_for_attachment($image);
			$li          = '
				<li id="item_%s">
					<img src="%s" />
					<div class="rwmb-image-bar">
						<a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
						<input type="hidden" name="certificateattachmentid" value="%s">
					</div>
				</li>
			';

			$src  = wp_get_attachment_image_src( $image, 'thumbnail' );
			$src  = $src[0];
			$link = get_edit_post_link( $image );

			return sprintf(
				$li,
				esc_attr($image),
				esc_url($arr['src']),
				esc_attr($i18n_delete), esc_attr($image), esc_attr($image)
			);
		}
		
		static function img_stripeidentity_html( $image )
		{
			$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
			$i18n_edit   = apply_filters( 'rwmb_image_edit_string', _x( 'Edit', 'image upload', 'service-finder' ) );
			$li          = '
				<li id="item_%s">
					<img src="%s" />
					<div class="rwmb-image-bar">
						<a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
						<input type="hidden" name="stripeidentityattachmentid" value="%s">
					</div>
				</li>
			';

			$src  = wp_get_attachment_image_src( $image, 'thumbnail' );
			$src  = $src[0];
			$link = get_edit_post_link( $image );

			return sprintf(
				$li,
				esc_attr($image),
				esc_url($src),
				esc_attr($i18n_delete), esc_attr($image), esc_attr($image)
			);
		}
		
		static function img_articlefeatured_html( $image )
		{
			$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
			$i18n_edit   = apply_filters( 'rwmb_image_edit_string', _x( 'Edit', 'image upload', 'service-finder' ) );
			$li          = '
				<li id="item_%s">
					<img src="%s" />
					<div class="rwmb-image-bar">
						<a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
						<input type="hidden" name="articlefeaturedattachmentid" value="%s">
					</div>
				</li>
			';

			$src  = wp_get_attachment_image_src( $image, 'thumbnail' );
			$src  = $src[0];
			$link = get_edit_post_link( $image );

			return sprintf(
				$li,
				esc_attr($image),
				esc_url($src),
				esc_attr($i18n_delete), esc_attr($image), esc_attr($image)
			);
		}
		
		static function img_file_html( $image )
		{
			$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
			$i18n_edit   = apply_filters( 'rwmb_image_edit_string', _x( 'Edit', 'image upload', 'service-finder' ) );
			$li          = '
				<li id="item_%s">
					<img src="%s" />
					<div class="rwmb-image-bar">
						<a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
						<input type="hidden" name="fileattachmentid[]" value="%s">
					</div>
				</li>
			';

			$src  = wp_get_attachment_image_src( $image, 'thumbnail' );
			$src  = $src[0];
			$link = get_edit_post_link( $image );
			
			if($src == ''){
			$arr  = self::get_icon_for_attachment($image);
			$src  = $arr['src'];
			}

			return sprintf(
				$li,
				esc_attr($image),
				esc_url($src),
				esc_attr($i18n_delete), esc_attr($image), esc_attr($image)
			);
		}
		
		static function img_memberedit_html( $image )
		{
			$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
			$i18n_edit   = apply_filters( 'rwmb_image_edit_string', _x( 'Edit', 'image upload', 'service-finder' ) );
			$li          = '
				<li id="item_%s">
					<img src="%s" />
					<div class="rwmb-image-bar">
						<a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
						<input type="hidden" name="sfmemberavataredit" value="%s">
					</div>
				</li>
			';

			$src  = wp_get_attachment_image_src( $image, 'thumbnail' );
			$src  = $src[0];
			$link = get_edit_post_link( $image );

			return sprintf(
				$li,
				esc_attr($image),
				esc_url($src),
				esc_attr($i18n_delete), esc_attr($image), esc_attr($image)
			);
		}
		
		/*Get attachment icon*/
		static function get_icon_for_attachment($post_id) {
		  $base = SERVICE_FINDER_BOOKING_IMAGE_URL . "/file_icons/";
		  $type = get_post_mime_type($post_id);
		  switch ($type) {
			case 'image/jpeg':
			case 'image/png':
			case 'image/jpg':
			case 'image/gif':
			  $src = wp_get_attachment_image_src( $post_id, 'thumbnail' ); 
			  $arr = array(
			  					'src' => $src[0],
								'filename' => 'attachmentid',
								);
			  return $arr;
			  break;
			case 'application/pdf':
			  $arr = array(
			  					'src' => $base . "pdf.png",
								'filename' => 'fileattachmentid',
								);
			  return $arr;
			  break;
			case 'application/msword':
			  $arr = array(
			  					'src' => $base . "doc.png",
								'filename' => 'fileattachmentid',
								);
			  return $arr;
			  break;
			case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
			  $arr = array(
			  					'src' => $base . "doc.png",
								'filename' => 'fileattachmentid',
								);
			  return $arr;
			  break;
			case 'application/vnd.ms-excel':
			  $arr = array(
			  					'src' => $base . "xls.png",
								'filename' => 'fileattachmentid',
								);
			  return $arr;
			  break; 
			case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
			  $arr = array(
			  					'src' => $base . "xls.png",
								'filename' => 'fileattachmentid',
								);
			  return $arr;
			  break; 
			case 'application/vnd.ms-powerpoint':
			  $arr = array(
			  					'src' => $base . "ppt.png",
								'filename' => 'fileattachmentid',
								);
			  return $arr;
			  break;
			case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
			  $arr = array(
			  					'src' => $base . "ppt.png",
								'filename' => 'fileattachmentid',
								);
			  return $arr;
			  break;        
			default:
				//return $type;
			  $arr = array(
			  					'src' => $base . "file.png",
								'filename' => 'fileattachmentid',
								);
			  return $arr;
			  break;
		  }
		}

		/**
		 * Output the field value
		 * Display unordered list of images with option for size and link to full size
		 *
		 * @param  array    $field   Field parameters
		 * @param  array    $args    Additional arguments. Not used for these fields.
		 * @param  int|null $post_id Post ID. null for current post. Optional.
		 *
		 * @return mixed Field value
		 */
		static function the_value( $field, $args = array(), $post_id = null )
		{
			$value = self::get_value( $field, $args, $post_id );
			if ( ! $value )
				return '';

			$output = '<ul>';
			foreach ( $value as $file_id => $file_info )
			{
				$img = sprintf(
					'<img src="%s" alt="%s" title="%s">',
					esc_url( $file_info['url'] ),
					esc_attr( $file_info['alt'] ),
					esc_attr( $file_info['title'] )
				);

				// Link thumbnail to full size image?
				if ( isset( $args['link'] ) && $args['link'] )
				{
					$img = sprintf(
						'<a href="%s" title="%s">%s</a>',
						esc_url( $file_info['full_url'] ),
						esc_attr( $file_info['title'] ),
						$img
					);
				}

				$output .= "<li>$img</li>";
			}
			$output .= '</ul>';

			return $output;
		}

		/**
		 * Get uploaded file information
		 *
		 * @param int   $file_id Attachment image ID (post ID). Required.
		 * @param array $args    Array of arguments (for size).
		 *
		 * @return array|bool False if file not found. Array of image info on success
		 */
		static function file_info( $file_id, $args = array() )
		{
			$args = wp_parse_args( $args, array(
				'size' => 'thumbnail',
			) );

			$img_src = wp_get_attachment_image_src( $file_id, $args['size'] );
			if ( ! $img_src )
			{
				return false;
			}

			$attachment = get_post( $file_id );
			$path       = get_attached_file( $file_id );
			return array(
				'ID'          => $file_id,
				'name'        => basename( $path ),
				'path'        => $path,
				'url'         => $img_src[0],
				'width'       => $img_src[1],
				'height'      => $img_src[2],
				'full_url'    => wp_get_attachment_url( $file_id ),
				'title'       => $attachment->post_title,
				'caption'     => $attachment->post_excerpt,
				'description' => $attachment->post_content,
				'alt'         => get_post_meta( $file_id, '_wp_attachment_image_alt', true ),
			);
		}
	}
}
