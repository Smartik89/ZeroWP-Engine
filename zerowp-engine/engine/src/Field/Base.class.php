<?php
/**
 * Base field
 *
 * The base field class desinged to be extended.
 *
 * @since 1.0
 */
namespace Zwe\Field;

abstract class Base{

	/* Field ID
	----------------*/
	protected $id;

	/* Field value
	-------------------*/
	protected $value;

	/* Field settings
	----------------------*/
	protected $settings;

	/* Field name
	----------------------*/
	protected $name;

	//------------------------------------//--------------------------------------//

	/**
	 * Constructor
	 *
	 * Field construct
	 *
	 * @param string $id The field ID
	 * @param string $value The field value
	 * @param array $settings The field settings
	 * @param string $name If the parent form has a name, it must be included in field name.
	 * @return void
	 */
	public function __construct( $id, $value = '', $settings = array(), $name = false ){
		$this->id       = $id;
		$this->value    = $value;
		$this->settings = $settings;
		$this->name     = $name;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Enqueue
	 *
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	public static function enqueue(){
		return false;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Default settings
	 *
	 * Default field settings
	 *
	 * @return array
	 */
	abstract public function defaultSettings();

	//------------------------------------//--------------------------------------//

	/**
	 * Must Use settings
	 *
	 * Field settings that can't be overided
	 *
	 * @return array|bool(false)
	 */
	public function mustUseSettings(){
		return false;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Field settings
	 *
	 * Get parsed field settings
	 *
	 * @return array
	 */
	public function settings(){
		$default_settings = wp_parse_args( $this->defaultSettings(), array(
			'label' => '',
			'description' => '',
			'disable:backend' => false,
			'disable:frontend' => false,
			'_required' => false,
			'__dump_settings' => false,
		) );
		$settings = wp_parse_args( $this->validateSettings( $this->settings ), $default_settings );

		// Add Must Use settings
		$must_use_settings = $this->mustUseSettings();
		if( !empty( $must_use_settings ) && is_array( $must_use_settings ) ){
			$settings = wp_parse_args( $must_use_settings, $settings );
		}

		return $settings;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Validate field settings
	 *
	 * Validate field settings
	 *
	 * @param array $settings Field settings to be validated
	 * @return array Validated settings
	 */
	public function validateSettings( $settings ){
		return (array) $settings;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Render field
	 *
	 * The final field output without description, label and so on...
	 *
	 * @see renderField()
	 * @return string Field markup
	 */
	abstract public function render();

	//------------------------------------//--------------------------------------//

	/**
	 * Render complete field
	 *
	 * The final field output without description, label and so on...
	 *
	 * @see renderField()
	 * @return string Field markup
	 */
	public function renderField(){
		$output = '<div data-form-field-id="'. $this->getName() .'">';
			$output .= $this->render();
		$output .= '</div>';

		// Return the array of the field settings
		if( $this->getSetting( '__dump_settings' ) ){
			$output .= $this->_dumpSettings();
		}

		return $output;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Dump field settings to developer
	 *
	 * @return string The field settings array as string.
	 */
	public function _dumpSettings(){
		$output = '<pre>';
		$output .= var_export( $this->settings(), true );
		$output .= '</pre>';

		return $output;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Validate field value
	 *
	 * Validate field value. Used on form submit before saving data. Must verify the
	 * submitted user data and and return false(success) or an array of errors.
	 *
	 * @param mixed $value The field value to be validated
	 * @param mixed $settings The field settings
	 * @return string|bool(false) The valitation status. 'string' - validation message, 'false' - is validated.
	 */
	public static function validate( $type, $value, $settings ){
		return false;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Sanitize field value
	 *
	 * Sanitize field value. Sanitize data submitted by user based on field settings.
	 *
	 * @param mixed $value The field value to be sanitized.
	 * @param mixed $settings The field settings
	 * @return mixed Sanitized field value.
	 */
	public static function sanitize( $value, $settings ){
		return $value;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Get setting
	 *
	 * Get a field setting from the array of settings by passing the setting key.
	 *
	 * @param string $setting_key Setting key
	 * @return mixed|null
	 */
	public function getSetting( $setting_key ){
		$settings = $this->settings();
		return ( isset($settings[ $setting_key ]) ) ? $settings[ $setting_key ] : null;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Get field name attribute
	 *
	 * Get field name attribute
	 *
	 * @return string Field name
	 */
	public function getName( $custom_field_id = false ){
		$field_id = ( is_string( $custom_field_id ) ) ? $custom_field_id : $this->id;
		return str_ireplace( '{ID}', $field_id, $this->name );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Get field value
	 *
	 * Get field value that has NOT been sanitized yet.
	 *
	 * @param string
	 * @return string
	 */
	public function getValue(){
		return $this->value;
	}

	/*
	-------------------------------------------------------------------------------
	Helpers
	-------------------------------------------------------------------------------
	*/

	public function htmlInput( $attributes = array() ){
		$default = array(
			'type' => 'text',
			'value' => '',
			'class' => '',
			'name' => '',
		);

		$atts = wp_parse_args( $attributes, $default );

		return '<input'. $this->createAttributes( $atts ) .' />';
	}

	public function htmlSelect( $options, $selected, $attributes = array() ){
		$empty_option = '';
		if( !empty($attributes['empty_option']) ){
			if( is_string($attributes['empty_option']) ){
				$empty_option = '<option value="">'. esc_html($attributes['empty_option']) .'</option>';
			}
			else{
				$empty_option = '<option value="">  </option>';
			}
		}
		if( isset($attributes['empty_option']) ){
			unset($attributes['empty_option']);
		}

		$output = '<select'. $this->createAttributes( $attributes ) .'>';

			$output .= $empty_option;

			if( !empty($options) && is_array( $options ) ){
				foreach ($options as $val => $text) {
					$is_selected = $this->selected( $val, $selected );
					$output .= '<option value="'. esc_attr($val) .'"'. $is_selected .'>'. esc_html( $text ) .'</option>';
				}
			}

		$output .= '</select>';

		return $output;
	}

	/*
	-------------------------------------------------------------------------------
	HTML form elements helpers
	-------------------------------------------------------------------------------
	*/

	public function htmlInputClass(){
		$size = $this->getSetting( 'size' );
		$class = 'regular-text';

		if( !empty($size) ){
			if( in_array( $size, array('wide', 'widefat', 'large') ) ){
				$class = 'widefat';
			}
			elseif( in_array( $size, array('small', 'small-text', 'mini') ) ){
				$class = 'small-text';
			}
			elseif( 'none' == $size ){
				$class = '';
			}
		}

		return $class;
	}

	public function createAttributes( $attributes = array() ){
		$html_attr = '';
		if( !empty($attributes) ){
			foreach ($attributes as $att => $value) {
				$html_attr .= ( !empty($value) || $value == 0 )  ? ' '. strip_tags($att) .'="'. esc_attr( $value ) .'"' : '';
			}
		}
		return $html_attr;
	}

	public function _attr_compare( $first, $second, $attribute_name ){
		if( (string) $first === (string) $second ){
			return ' '. $attribute_name .'="'. $attribute_name .'"';
		}
		else{
			return '';
		}
	}

	public function selected( $first, $second ){
		return $this->_attr_compare( $first, $second, 'selected' );
	}

	public function checked( $first, $second ){
		return $this->_attr_compare( $first, $second, 'checked' );
	}

	public function disabled( $first, $second ){
		return $this->_attr_compare( $first, $second, 'disabled' );
	}

}