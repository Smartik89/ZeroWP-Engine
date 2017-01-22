<?php
/**
* Save a form
*
* Provides the required functions for saving a form via ajax.
*
* @since 1.0
*
*/
namespace Zwe\Form;

abstract class AbstractSave{

	//------------------------------------//--------------------------------------//

	/**
	 * Module type
	 *
	 * @return string
	 */
	public $module;

	//------------------------------------//--------------------------------------//

	/**
	 * Module view
	 *
	 * The module is loaded in backend or frontend. Usually used to load the right fields.
	 *
	 * @return string
	 */
	public $view;

	//------------------------------------//--------------------------------------//

	/**
	 * Module ID
	 *
	 * Capture the module ID from form data(note: this is not module type!!)
	 *
	 * @return string
	 */
	public $moduleId;

	//------------------------------------//--------------------------------------//

	/**
	 * Module settings
	 *
	 * Get and store module settings for internal use
	 *
	 * @return string
	 */
	public $moduleSettings;

	//------------------------------------//--------------------------------------//

	/**
	 * Form data
	 *
	 * Store data submitted by user to be processed and saved
	 *
	 * @return array
	 */
	public $formData;

	//------------------------------------//--------------------------------------//

	/**
	 * Form fields
	 *
	 * Store the form fields from module filter
	 *
	 * @return array
	 */
	public $formFields;

	//------------------------------------//--------------------------------------//

	/**
	 * User data
	 *
	 * This contains only the data submitted by user, no hidden fields here.
	 * It's also filtered for unknown fields.
	 *
	 * @return string
	 */
	public $userData;

	//------------------------------------//--------------------------------------//

	/**
	 * Form message
	 *
	 * Store the form submittion messages
	 *
	 * @return array
	 */
	public $formMessage;

	//------------------------------------//--------------------------------------//

	/**
	 * Ready
	 *
	 * Make it ready for processing data and save
	 *
	 * @return void
	 */
	public function ready(){
		$this->formData       = $this->getData();

		update_option('optwer', $this->formData);

		if( isset($this->formData[ $this->moduleId ]) ){
			$this->fieldsData     = $this->formData[ $this->moduleId ];
		}

		if( empty($this->moduleId) ){
			$this->moduleId = esc_html( $this->formData[ '_form_id' ] );
		}

		$this->formFields     = $this->filterFormFields();
		$this->moduleSettings = $this->getModuleSettings();

		$this->userData       = $this->filterData();
	}


	//------------------------------------//--------------------------------------//

	/**
	 * Filter form fields
	 *
	 * Filter form fields based on view. Check if field is enabled in backend or frontend otherwise remove it.
	 *
	 * @return array
	 */
	public function filterFormFields(){
		$fields = apply_filters( "zwe_{$this->module}_{$this->moduleId}_manage_fields", array() );

		if( !empty($fields) ){
			foreach ($fields as $field_id => $field) {

				$fields[$field_id]['settings'] = $this->parseFieldSettings( $field['type'], $field_id, $field['settings'] );

				if( ! empty($fields[$field_id]['settings']['disable:'. $this->view ]) ){
					unset( $fields[ $field_id ] );
				}
			}
		}

		return $fields;
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
		$field_class  = zwe_get_field_classname( $type, $this->view );
		$class        = new $field_class( $field_id );
		$new_settings = wp_parse_args( $field_settings, $class->settings() );

		return $new_settings;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Get the settings for current module
	 *
	 * @return array
	 */
	public function getModuleSettings(){
		$modules = zwe_module_childs( $this->module );

		if( array_key_exists( $this->moduleId, $modules ) ){
			return $modules[ $this->moduleId ];
		}
		else{
			return array();
		}
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Get Field Class Name
	 *
	 * @return string The class name
	 */
	public function getFieldClass( $field_type ){
		return zwe_get_field_classname( $field_type, $this->view );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Filter data
	 *
	 * Filter data based on defined fields. If data has a field value that is not
	 * defined in fields list(ie. the user tried to change the name attribute from
	 * source code ), unset it from $data.
	 *
	 * @param array $data Submitted data
	 * @param array $all_fields All fields defined by user .
	 * @return array Filtered data without the hacked fields values.
	 */
	public function filterData(){
		$data = false;

		if(
			isset( $this->fieldsData )
			&& is_array( $this->fieldsData )
			&& is_array( $this->formFields )
		){
			$data = $this->fieldsData;

			foreach ($data as $field_id => $field_value) {
				if( ! array_key_exists( $field_id, $this->formFields ) ){
					unset( $data[ $field_id ] );
				}
			}

		}

		return $data;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Get Form Data
	 *
	 * Get form data subbmitted by user
	 *
	 * @return array|bool(false) `array` - the submitted form data, `false` - on error.
	 */
	public function getData(){
		if( isset( $_POST['form'] ) ){
			parse_str( stripslashes( $_POST['form'] ), $form_data);
			return $form_data;
		}
		else{
			return false;
		}
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Validate data
	 *
	 * Validate submitted data based on field settings.
	 *
	 * @return array Empty or an array of errors: 'field_id' => 'Error message'
	 */
	public function validateData(){
		$errors = false;

		if( ! is_array( $this->userData ) )
			return $errors;

		foreach ($this->userData as $field_id => $field_value) {
			if( $class = $this->getFieldClass( $this->formFields[ $field_id ][ 'type' ] ) ){
				$validate_result = $class::validate(
					$this->formFields[ $field_id ][ 'type' ],
					$field_value,
					$this->formFields[ $field_id ][ 'settings' ]
				);
				if( !empty($validate_result) ){
					$errors[ $this->moduleId .'['. $field_id .']' ] = $validate_result;
				}
			}
		}

		return $errors;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Prepare data
	 *
	 * Sanitize submitted data based on field settings.
	 *
	 * @param array $data Submitted data
	 * @return array Sanitized data
	 */
	public function sanitizeData( $data ){
		$new_data = array();

		if( ! is_array( $data ) )
			return $data;

		foreach ($data as $field_id => $field_value) {
			if( $class = $this->getFieldClass( $this->formFields[ $field_id ][ 'type' ] ) ){
				$new_data[ $field_id ] = $class::sanitize( $field_value, $this->formFields[ $field_id ][ 'settings' ] );
			}
		}

		return $new_data;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Submit message
	 *
	 * Return the submission status and message(string or array or messages)
	 *
	 * @param string $status Submission status: `fail` or `success`
	 * @param string $message A message based on $status
	 * @param array|bool(false) $errors An array of messages
	 * @return JSON
	 */
	public function _msg( $status, $message = '', $errors = false ){
		return array(
			'status' => $status,
			'message' => $message,
			'errors' => $errors,
		);
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Verify Action
	 *
	 * Verify POST action
	 *
	 * @return bool `true` if action is OK.
	 */
	public function isAction(){
		return ( isset( $_POST['action'] ) && "zwe_{$this->module}_submit" == $_POST['action'] );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Nonce Verify
	 *
	 * Check if the nonce is valid
	 *
	 * @return bool `true` - is valid, `false` -  is invalid
	 */
	public function verifyNonce(){
		if( !empty( $this->formData['_form_nonce'] ) ){
			return (bool) wp_verify_nonce( $this->formData['_form_nonce'], "zwe_{$this->module}_{$this->moduleId}_nonce" );
		}
		else{
			return false;
		}
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Validate nonce
	 *
	 * Check if the nonce is valid and add a error message if needed.
	 *
	 * @return object $this
	 */
	public function validNonce(){
		if( $this->formMessage )
			return $this;

		// Check if user can save
		if( ! $this->verifyNonce() ){
			$this->formMessage = $this->_msg( 'fail', $this->moduleSettings['msg:invalid_nonce']);
		}

		return $this;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Check user capability
	 *
	 * Check if the current user has the capability required to save options
	 *
	 * @return bool `true` - has, `false` - has not.
	 */
	public function userHasCapability(){
		$capability = $this->moduleSettings['capability'];
		return current_user_can( $capability );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Validate current user
	 *
	 * Check if the user can save and set an error message is needed
	 *
	 * @return object $this
	 */
	public function validUser(){
		if( $this->formMessage )
			return $this;

		// Check if user can save
		if( ! $this->userHasCapability() ){
			$this->formMessage = $this->_msg( 'fail', $this->moduleSettings['msg:not_allowed']);
		}

		return $this;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Has Data
	 *
	 * Check if the user has some data to be saved.
	 *
	 * @return object $this
	 */
	public function hasData(){
		if( $this->formMessage )
			return $this;

		//Check if there is something to save(if we have valid filtered data)
		if( empty( $this->userData ) ){
			$this->formMessage = $this->_msg( 'fail', $this->moduleSettings['msg:nothing']);
		}

		return $this;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Validate data
	 *
	 * Check if the submitted data is valid and set the error if needed.
	 *
	 * @return object $this
	 */
	public function validData(){
		if( $this->formMessage )
			return $this;

		// If there are validation errors, fail and return messages
		if( ( $validation_errors = $this->validateData() ) !== false ){
			$this->formMessage = $this->_msg( 'fail', $this->moduleSettings['msg:fail'], $validation_errors );
		}

		return $this;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Save
	 *
	 * Send data to DataBase and save it.
	 *
	 * @return void
	 */
	abstract public function saveData( $data );

	//------------------------------------//--------------------------------------//

	/**
	 * Save
	 *
	 * Sanitize data and save it
	 *
	 * @return object $this
	 */
	public function save(){
		if( $this->formMessage )
			return $this;

		$data = $this->sanitizeData( $this->userData );
		do_action( "zwe_{$this->module}_before_save", $this->moduleId, $data, $this->formFields );

		$new_data = $this->saveData( $data );
		$this->formMessage = $this->_msg( 'success', $this->moduleSettings['msg:success']); //Success message

		do_action( "zwe_{$this->module}_after_save", $this->moduleId, $new_data, $this->formFields );

		return $this;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Complete
	 *
	 * Must be called after save to get the submittion messages(errors or success).
	 *
	 * @return array Submittion message
	 */
	public function complete(){
		return $this->formMessage;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Complete in JSON
	 *
	 * @return JSON
	 */
	public function completeJson(){
		return json_encode( $this->complete() );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Die in JSON
	 *
	 * @return JSON
	 */
	public function dieJson(){
		die( json_encode( $this->complete() ) );
	}

}