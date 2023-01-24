<?php

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SFTimeSlots {
    /**
     * @var array
     */
    protected $values = array();
	
	const WORKING_START_TIME = '00:00:00';
    const WORKING_END_TIME   = '23:00:00';

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct( array $options = array() ) {
        global $service_finder_options;
		// Handle widget options.
        $options = array_merge( array(
            'use_empty' => true,
            'empty_value' => null
        ), $options );

        // Insert empty value if required.
        if ( $options[ 'use_empty' ] ) {
            $this->values[ null ] = $options[ 'empty_value' ];
        }

        $time_format = (!empty($service_finder_options['time-format'])) ? $service_finder_options['time-format'] : '';
        if($time_format){
		$tf         = 'H:i';//get_option( 'time_format' );
		}else{
		$tf         = 'g:i a';//get_option( 'time_format' );		
		}
        $ts_length  = 15;//get_option( 'ab_settings_time_slot_length' );
        $time_start = new SFDateTime( $this::WORKING_START_TIME, new DateTimeZone( 'UTC' ) );
        $time_end   = new SFDateTime( $this::WORKING_END_TIME, new DateTimeZone( 'UTC' ) );

        // Run the loop.
        while ( $time_start->format( 'U' ) <= $time_end->format( 'U' ) ) {
            $this->values[ $time_start->format( 'H:i:s' ) ] = $time_start->format( $tf );
            $time_start->modify( '+' . $ts_length . ' min' );
        }
        $this->values[ $time_end->format( 'H:i:s' ) ] = $time_end->format( $tf );
    }

    /**
     * Render the widget.
     *
     * @param       $name
     * @param null  $value
     * @param array $attributes
     *
     * @return string
     */
    public function render( $name, $value = null, array $attributes = array() ) {
        $options = array();
        $attributes_str = '';
        foreach ( $this->values as $option_value => $option_text ) {

            $selected = strval( $value ) == strval( $option_value );
            $options[ ] = sprintf(
                '<option value="%s"%s>%s</option>',
                $option_value,
                ($selected ? ' selected="selected"' : ''),
                $option_text
            );
			
        }
        foreach ( $attributes as $attr_name => $attr_value ) {
            $attributes_str .= sprintf( ' %s="%s"', $attr_name, $attr_value );
        }
		
        return sprintf( '<select name="%s"%s>%s</select>', $name, $attributes_str, implode( '', $options ) );
    }
	
	/**
     * Render the widget.
     *
     * @param       $name
     * @param null  $value
     * @param array $attributes
     *
     * @return string
     */
    public function customrender( $name, $value = null, array $attributes = array(), $maxbookingname = '', $maxbookingvalue = '', array $maxbookingattributes = array() ) {
        $options = array();
        $attributes_str = '';
		$maxbookingattributes_str = '';
        foreach ( $this->values as $option_value => $option_text ) {

            $selected = strval( $value ) == strval( $option_value );
            $options[ ] = sprintf(
                '<option value="%s"%s>%s</option>',
                $option_value,
                ($selected ? ' selected="selected"' : ''),
                $option_text
            );
			
        }
        foreach ( $attributes as $attr_name => $attr_value ) {
            $attributes_str .= sprintf( ' %s="%s"', $attr_name, $attr_value );
        }
		
		foreach ( $maxbookingattributes as $attr_name => $attr_value ) {
            $maxbookingattributes_str .= sprintf( ' %s="%s"', $attr_name, $attr_value );
        }
		
        return sprintf( '<select name="%s"%s>%s</select><input type="text" name="%s" value="%s" %s>', $name, $attributes_str, implode( '', $options ), $maxbookingname, $maxbookingvalue, $maxbookingattributes_str );
    }

    /**
     * @param $start
     * @param string $selected
     * @return array
     */
    public function renderOptions( $start, $selected = '' ) {
        $options = array();
        foreach ( $this->values as $option_value => $option_text ) {
            if ( $start && strval( $option_value ) < strval( $start ) ) continue;
            $options[ ] = sprintf(
                '<option value="%s"%s>%s</option>',
                $option_value,
                (strval( $selected ) == strval( $option_value ) ? 'selected="selected"' : ''),
                $option_text
            );
        }

        return $options;
    }
}