<?php
/**
* Form
*
* Create a general form. This class does not create the page where to live,
* it must be created manually. This class provides the API required to create a form.
*
* @since 1.0
*
*/
namespace Zwe\Form;

class Form{

	public $formId; //Form ID. This is used in fields name="" attribute.
	public $formSettings; //Form settings array.
	public $formAttributes; //Form attributes array.
	public $formFields = array(); //Form fields.
	public $formSections = array(); //Form sections.
	public $formHiddenFields = array(); //Array of hidden fields to be prepended on this form.

	//------------------------------------//--------------------------------------//

	/**
	 * Construct
	 *
	 * @return void
	 */
	public function __construct( $form_id = false, $form_settings = false, $form_attributes = false ){
		$this->formId         = $form_id;
		$this->formAttributes = $form_attributes;
		$this->formSettings   = $form_settings;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Default form settings
	 *
	 * @return array
	 */
	public function defaultSettings(){
		return array(
			'module'        => 'Form', //Module type(eg: OptionsPage)
			'form_view'     => 'backend', // Form view may be `backend` or `frontend`. Load fields for backend use or frontend.
			'form_tags'     => true, // Display <form></form> tags
			'pattern'       => $this->formId . '[{ID}]', // The pattern used for field name attribute. {ID} is replaced with the field ID

			'field:nonce'       => true, // Add a hidden nonce field(bool)
			'field:form_id'     => true, // Add a hidden field with the form ID(bool)
			'field:form_action' => true, // Add a hidden field with the form action, used in AJAX(bool)

			'http_referer'  => true,
			'submit_button' => true, // Show submit button(bool)
			'before_form'   => '<div class="zwe-form-container">',
			'after_form'    => '</div>',
			'before_fields' => '<div class="zwe-form-fields">',
			'after_fields'  => '</div>',
			'row_callback'  => array( $this, 'renderFormRowCallback' ),
		);
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Add Hidden Field
	 *
	 * @return void
	 */
	public function addHiddenField( $id, $value ){
		$this->formHiddenFields[ $id ] = $value;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Add Form Field
	 *
	 * Add a single form field to the list.
	 *
	 * @return void
	 */
	public function addFormField($id, $elem ){
		$form_settings    = $this->_getFormSettings();
		$elem['settings'] = $this->parseFieldSettings( $elem['type'], $id, $elem['settings'] );

		if( empty($elem['settings']['disable:'. $form_settings['form_view'] ]) ){
			$this->formFields[ $id ] = $elem;
		}
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Add a single form section
	 *
	 * @return void
	 */
	public function addFormSection($id, $settings = array() ){
		$this->formSections[ $id ] = $settings;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Add a one or more form sections
	 *
	 * @return void
	 */
	public function addFormSections($sections = array() ){
		if( !empty($sections) && is_array($sections) ){
			$this->formSections = wp_parse_args( $sections, $this->formSections );
		}
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Parse field settings
	 *
	 * Merge default field settings with custom settings provided by user.
	 *
	 * @param $type Field type
	 * @param $field_id
	 * @param $field_settings
	 * @return array New merged settings
	 */
	public function parseFieldSettings( $type, $field_id, $field_settings ){
		$form_settings = $this->_getFormSettings();
		$field_class   = zwe_get_field_classname( $type, $form_settings['form_view'] );
		$class         = new $field_class( $field_id );
		$new_settings  = wp_parse_args( $field_settings, $class->settings() );

		return $new_settings;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Create Field
	 *
	 * @return string The field HTML
	 */
	public function createField( $id, $type, $value, $settings = false ){
		$form_settings = $this->_getFormSettings();

		if( $class_name = zwe_get_field_classname( $type, $form_settings['form_view'] ) ){

			//Render field
			$field_class_instance = new $class_name( $id, $value, $settings, $form_settings['pattern'] );
			$field_html = $field_class_instance->renderField();

			return $this->renderFormRow( $id, $value, $settings, $field_html, $field_class_instance );

		}
		else{
			return false;
		}

	}

	//------------------------------------//--------------------------------------//

	/**
	 * Get Formated Content
	 *
	 * Loop fields and create a final HTML result of each
	 *
	 * @return string The formated HTML
	 */
	public function getFormatedContent(){

		if( empty( $this->formFields ) )
			return;

		$form_settings = $this->_getFormSettings();
		$created_fields = array(); // This will store html formated fields in arrays for each section.

		foreach ($this->formFields as $element_id => $elem) {
			$section_id = ( empty($elem['section']) ) ? 0 : $elem['section'];
			$created_fields[ $section_id ][] = $this->createField( $elem['id'], $elem['type'], $elem['value'], $elem['settings'] );
		}

		$output = '';

		//Orphan fields(without a specific section)
		if( !empty($created_fields[ 0 ]) ){
			$output .= $this->_getSingleSection( $created_fields[ 0 ] );
		}

		//Fields that has a specific section
		if( !empty($this->formSections) ){
			foreach ($this->formSections as $section_id => $section) {
				if( !empty( $created_fields[ $section_id ] ) ){
					$output .= $this->_getSingleSection( $created_fields[ $section_id ], $section_id, $section['settings'] );
				}
			}
		}

		return $output;
	}

	public function _getSingleSection( $formated_fields_array, $section_id = false, $section = false ){
		$form_settings = $this->_getFormSettings();
		$output        = '';
		$section_id    = !empty($section_id) ? esc_attr( $section_id ) : 0;

		if( !empty($section_id) && !empty($section) ){
			$output .= $this->_sectionHeader( $section_id, $section );
		}

		$output .= '<div class="form-section" data-form-section-id="'. $section_id .'">';

		if( !empty($form_settings['before_fields']) ){
			$output .= $form_settings['before_fields'];
		}

		foreach ($formated_fields_array as $field) {
			$output .= $field;
		}

		if( !empty($form_settings['after_fields']) ){
			$output .= $form_settings['after_fields'];
		}

		$output .= '</div>'; //.form-section

		return $output;
	}

	public function _sectionHeader( $section_id, $section ){
		$output = '<div class="zwe-form-section-header" data-section-header-id="'. esc_attr( $section_id ) .'">';
			if( !empty($section['label']) ){
				$output .= '<h3>'. wp_kses_data( $section['label'] ) .'</h3>';
			}
			if( !empty($section['description']) ){
				$output .= '<div class="section-description">'. $section['description'] .'</div>';
			}
		$output .= '</div>';

		return $output;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Return complete form
	 *
	 * Return the final formated form markup
	 *
	 * @return string The HTML
	 */
	public function getForm(){
		$form_settings = $this->_getFormSettings();
		$output        = '';

		if( !empty($form_settings['before_form']) ){
			$output .= $form_settings['before_form'];
		}

		if( !empty($form_settings['form_tags']) ){
			$output .= $this->openForm();
		}

			// Nonce
			if( $form_settings['field:nonce'] ){
				$output .= $this->getNonceField();
			}

			// Form ID
			if( $form_settings['field:form_id'] ){
				$f_id = is_string( $form_settings['field:form_id'] ) ? $form_settings['field:form_id'] : '_form_id';
				$output .= $this->addHiddenField( $f_id, $this->formId );
			}

			// Form Action
			if( $form_settings['field:form_action'] ){
				$module = $form_settings['module'];
				$f_action = is_string( $form_settings['field:form_action'] ) ? $form_settings['field:form_action'] : '_form_action';
				$output .= $this->addHiddenField( $f_action, "zwe_{$module}_submit" );
			}

			// Render hidden fields
			if( !empty($this->formHiddenFields) ){
				foreach ($this->formHiddenFields as $hidden_id => $hidden_value) {
					$output .= '<input type="hidden" name="'. $hidden_id .'" value="'. $hidden_value .'" />';
				}
			}

			$output .= $this->getFormatedContent();

			if( $form_settings['submit_button'] ){
				$output .= $this->getSubmitButton();
			}

		if( !empty($form_settings['form_tags']) ){
			$output .= $this->closeForm();
		}

		if( !empty($form_settings['after_form']) ){
			$output .= $form_settings['after_form'];
		}

		return $output;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Render Form
	 *
	 * Outputs the final formated form markup
	 *
	 * @return string The HTML
	 */
	public function renderForm(){
		echo $this->getForm();
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Render form row
	 *
	 * The final row markup with the field HTML included.
	 *
	 * @param string $settings Field settings
	 * @param string $field_html The field html. Each field type has it's own HTML output.
	 * @return string Row markup with the field.
	 */
	public function renderFormRow( $id, $value, $settings, $field_html, $field_class_instance ){
		$form_settings = $this->_getFormSettings();

		if( !empty($form_settings['row_callback']) ){
			$is_callable = is_callable( $form_settings['row_callback'], true, $callback);
			if( $is_callable ){
				return call_user_func( $callback, $id, $value, $settings, $field_html, $field_class_instance );
			}
		}

	}

	//------------------------------------//--------------------------------------//

	/**
	 * Get Submit button
	 *
	 * Get the default WordPress submission button
	 *
	 * @param string
	 * @return string
	 */
	public function getSubmitButton( $text = '', $type = 'primary large', $name = 'submit', $wrap = true, $other_attributes = '' ){
		return get_submit_button( $text, $type, $name, $wrap, $other_attributes );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Get form attributes
	 *
	 * Get form attributes and return a string to be included in `form` tag
	 *
	 * @return string
	 */
	protected function _getFormAttributesString(){
		$atts = $this->_getFormAttributes();
		return $this->doAttributes( $atts );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Parse form attributes
	 *
	 * Merge custom form attributes with default form attributes
	 *
	 * @return array
	 */
	protected function _getFormAttributes(){
		$attributes = array(
			'method'  => 'post',
			'enctype' => 'multipart/form-data',
			'novalidate' => 'novalidate',
		);

		$atts = $this->formAttributes;

		if( !empty($atts) && is_array($atts) ){
			$attributes = wp_parse_args( $atts, $attributes );
		}

		return $attributes;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Parse form settings
	 *
	 * Merge custom form settings with default form settings
	 *
	 * @return array
	 */
	protected function _getFormSettings(){
		$settings = $this->defaultSettings();
		$custom   = $this->formSettings;

		if( !empty($custom) && is_array($custom) ){
			$settings = wp_parse_args( $custom, $settings );
		}

		return $settings;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Get nonce field
	 *
	 * Get the WordPress nonce field and if specified the referer field
	 *
	 * @see https://codex.wordpress.org/Function_Reference/wp_nonce_field
	 *
	 * @param string
	 * @return string
	 */
	public function getNonceField(){
		$form_settings = $this->_getFormSettings();
		$module        = $form_settings['module'];
		$http_referer  = $form_settings['http_referer'];

		$field_name = is_string( $form_settings['field:nonce'] ) ? $form_settings['field:nonce'] : '_form_nonce';

		return wp_nonce_field( "zwe_{$module}_{$this->formId}_nonce", $field_name, $http_referer, false );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Open form
	 *
	 * Return the open form tag
	 *
	 * @return string
	 */
	public function openForm(){
		return '<form'. $this->_getFormAttributesString() .'>';
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Close form
	 *
	 * Return the clossing form tag
	 *
	 * @return string
	 */
	public function closeForm(){
		return '</form>';
	}

	/*
	-------------------------------------------------------------------------------
	Helpers
	Static functions that can be used outsite of main class instance.
	-------------------------------------------------------------------------------
	*/

	public static function toEnqueue( $fields_array, $form_view ){
		if( ! empty($fields_array) && is_array($fields_array) ) :
			foreach ($fields_array as $field_id => $field_data) {

				//Check is a valid class
				if( false === ( $class_name = zwe_get_field_classname( $field_data['type'], $form_view ) ) )
					continue;

				//Enqueue
				$class_name::enqueue();
			}


		endif;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Form row callback
	 *
	 * Return the html for a form row
	 *
	 * @param string $settings Field settings
	 * @param string $field_html The field html. Each field type has it's own HTML output.
	 * @return string Row markup with the field.
	 */
	public static function renderFormRowCallback( $id, $value, $settings, $field_html, $field_class_instance ){
		$description = ( !empty($settings['description']) ) ? '<p class="description">'. $settings['description'] .'</p>' : '';

		$row = '<div'. self::rowAttributes( $field_class_instance, $settings, array() ) .'>';
			if( !empty($settings['label']) ){
				$row .= '<div class="form-label">'. self::getFieldLabel( $settings ) .'</div>';
			}
			$row .= '<div class="form-field">'. $field_html .''. $description .'</div>';
		$row .= '</div>';

		return $row;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Row Attributes
	 *
	 * Get the attributes for a single row in a string.
	 *
	 * @param string $field_class_instance
	 * @param array $field_settings
	 * @param array $custom_attributes
	 * @return string
	 */
	public static function rowAttributes( $field_class_instance, $field_settings, $custom_attributes = array() ){
		$custom_attributes = wp_parse_args( (array) $custom_attributes, array( 'class' => 'form-row' ) );

		$attributes = ' data-form-row-id="'. esc_attr( $field_class_instance->getName() ) .'"';
		$attributes .= self::doAttributes( $custom_attributes );

		if( !empty( $field_settings['parent'] ) && is_array($field_settings['parent']) ){
			$parent = $field_settings['parent'];
			if( !empty($parent[0]) && !empty($parent[1]) ){
				$attributes .= 'data-form-parent-field-id="'. esc_attr( $field_class_instance->getName( $parent[0] ) ) .'"';
				$attributes .= 'data-form-parent-field-value="'. esc_attr( $parent[1] ) .'"';
			}
		}

		return $attributes;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Get field label
	 *
	 * Get field label(html escaped).
	 *
	 * @param string $settings Field settings
	 * @return string The label string
	 */
	public static function getFieldLabel( $settings ){
		if( !empty( $settings['label'] ) ){
			return esc_html( $settings['label'] );
		}
		else{
			return '';
		}
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Do Attributes
	 *
	 * Get an array of attributes and return the formated string.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function doAttributes( $atts ){
		$parsed_atts = '';

		if( !empty($atts) && is_array($atts) ){
			foreach ($atts as $attr_key => $attr_value) {
				if( is_numeric($attr_key) )
					continue; // The attribute can't be a number

				$parsed_atts .= ' '. $attr_key. '="'. esc_attr( $attr_value ) .'"';
			}
		}

		return $parsed_atts;
	}

}