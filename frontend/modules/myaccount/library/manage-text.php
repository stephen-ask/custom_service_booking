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

if ( ! class_exists( 'ClassText' ) )
{
	class ClassText extends ClassFeild
	{
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
				'<input type="text" class="rwmb-text" name="%s" id="%s" value="%s" placeholder="%s" size="%s" %s>%s',
				$field['field_name'],
				$field['id'],
				$meta,
				$field['placeholder'],
				$field['size'],
				$field['datalist'] ? "list='{$field['datalist']['id']}'" : '',
				self::listed_html_data( $field )
			);
		}

		/**
		 * Normalize parameters for field
		 *
		 * @param array $field
		 *
		 * @return array
		 */
		static function arrangefields( $field )
		{
			$field = wp_parse_args( $field, array(
				'size'        => 30,
				'datalist'    => false,
				'placeholder' => '',
			) );

			return $field;
		}

		/**
		 * Create datalist, if any
		 *
		 * @param array $field
		 *
		 * @return array
		 */
		static function listed_html_data( $field )
		{
			if ( ! $field['datalist'] )
				return '';

			$datalist = $field['datalist'];
			$html     = sprintf(
				'<datalist id="%s">',
				$datalist['id']
			);

			foreach ( $datalist['options'] as $option )
			{
				$html .= sprintf( '<option value="%s"></option>', $option );
			}

			$html .= '</datalist>';

			return $html;
		}
	}
}
