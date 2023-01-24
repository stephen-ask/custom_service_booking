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

// Make sure "text" field is loaded
require_once SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/myaccount/library/manage-urls.php';

if ( ! class_exists( 'ClassOEmbed' ) )
{
	class ClassOEmbed extends ClassURL
	{
		/**
		 * Enqueue scripts and styles
		 *
		 * @return void
		 */
		static function admin_enqueue_scripts()
		{
			
		}

		/**
		 * Add actions
		 *
		 * @return void
		 */
		static function add_actions()
		{
			add_action( 'wp_ajax_rwmb_getembededcode', array( __CLASS__, 'wp_ajax_getembededcode' ) );
		}

		/**
		 * Ajax callback for returning oEmbed HTML
		 *
		 * @return void
		 */
		static function wp_ajax_getembededcode()
		{
			$url = isset( $_POST['url'] ) ? $_POST['url'] : '';
			wp_send_json_success( self::getembededcode( $url ) );
		}

		/**
		 * Get embed html from url
		 *
		 * @param string $url
		 *
		 * @return string
		 */
		static function getembededcode( $url )
		{
			$embed = @wp_oembed_get( $url );
			
			return $embed ? $embed : esc_html__( 'Please enter correct video url.', 'service-finder' );
			
			/*if(service_finder_get_video_type($url) == 'facebook'){
			if(preg_match("~(?:t\.\d+/)?(\d+)~i", $url, $matches)) {
		   	$videoid = $matches[1];
					
			return '<iframe src="https://www.facebook.com/video/embed?video_id='.$videoid.'" width="1280" height="720" frameborder="0"></iframe>';
			}
			}else{

			return $embed ? $embed : esc_html__( 'Please enter correct video url.', 'service-finder' );
			}*/
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
			return sprintf(
				'<input type="url" class="rwmb-oembed" name="%s" id="%s" value="%s" size="%s">
				<a href="javascript:;" class="show-embed button">%s</a>
				<span class="spinner"></span>
				<div class="embed-code">%s</div>',
				$field['field_name'],
				$field['id'],
				$meta,
				$field['size'],
				esc_html__( 'Preview', 'service-finder' ),
				$meta ? self::getembededcode( $meta ) : ''
			);
		}

		/**
		 * Output the field value
		 * Display embed media
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
			if ( $field['clone'] )
			{
				$output = '<ul>';
				foreach ( $value as $subvalue )
				{
					$output .= '<li>' . self::getembededcode( $subvalue ) . '</li>';
				}
				$output .= '</ul>';
			}
			else
			{
				$output = self::getembededcode( $value );
			}
			return $output;
		}
	}
}
