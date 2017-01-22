<?php
/**
* Register a new metabox
*
* @since 1.0
*
*/
namespace Zwe\Metabox;

use Zwe\Model\AbstractModule;
use Zwe\Manager\Inject;
use Zwe\Form\Form;

class Register extends AbstractModule{

	public $module;
	public $postType;

	public function __construct( $module_id, $post_type, $custom_settings = false ){
		$this->id             = $module_id;
		$this->customSettings = $custom_settings;
		$this->module         = "Metabox_{$post_type}";
		$this->postType       = $post_type;

		// Register a new module if another one with this ID does not exists
		if( ! $this->moduleIsRegistered() ){
			new Inject( zwe_module_filter_string( $this->module ), $this->id, $this->getSettings(), 30 );

			add_action( 'add_meta_boxes', array( $this, 'registerMetabox' ) );
			add_action( 'admin_enqueue_scripts', array($this, 'adminEnqueue'), 30 );
			add_action( 'save_post_' . $this->postType, array( $this, 'saveMetabox' ) );

			add_action( 'admin_print_scripts', array($this, 'printErrors'), 90 );
		}
	}

	public function defaultSettings(){
		return array(
			'context' => 'advanced', //'normal', 'side', and 'advanced'
			'priority' => 'default', // 'high', 'low'
		);
	}

	public function registerMetabox(){
		$settings = $this->getSettings();

		add_meta_box(
			$this->id,
			$settings['title'],
			array($this, 'renderMetabox'),
			$this->postType,
			$settings['context'],
			$settings['priority'],
			null //Callback args
		);
	}

	public function renderMetabox( $post ){
		$form = new Form( $this->id, array(
			'module'            => $this->module,
			'pattern'           => '{ID}',
			'form_tags'         => false,
			'http_referer'      => false,
			'submit_button'     => false,
			'field:nonce'       => "_form_nonce_{$this->id}",
			'field:form_id'     => false,
			'field:form_action' => false,
			'before_fields'     => '<table class="form-table"><tbody>',
			'after_fields'      => '</tbody></table>',
			'row_callback'      => 'zwe_form_table_row_callback',
		) );

		$fields = apply_filters( 'zwe_'. $this->module .'_'. $this->id .'_manage_fields', array() );
		$sections = apply_filters( 'zwe_'. $this->module .'_'. $this->id .'_manage_sections', array() );

		// Get previously saved data from DB
		$post_meta = get_post_meta( $post->ID );

		// If the submittion has errors, then there should be unsaved data. Get access to it.
		$keeped_data = $this->getKeepedUserData();

		// Add form fields and and set the values if the post has been saved, already
		foreach ( $fields as $element_id => $elem) {
			$meta_key = "_{$this->id}_{$element_id}";

			if( isset($post_meta[ $meta_key ][0]) || !empty($keeped_data) ){

				// Unsaved data, keeped
				if( isset( $keeped_data[ $this->id ][ $element_id ] ) ){
					$saved_value = $keeped_data[ $this->id ][ $element_id ];
				}

				// Saved data from DB
				elseif( isset($post_meta[ $meta_key ][0]) ){
					$saved_value = $post_meta[ $meta_key ][0];
				}

				// If we have something set the field value.
				if( isset( $saved_value ) ){
					// In some cases data may be serialized and not plain array(ie: in metaboxes ), unserialize if needed.
					if( is_serialized( $saved_value ) ){
						$elem['value'] = unserialize( $saved_value );
					}
					else{
						$elem['value'] = $saved_value;
					}
				}

			}
			$form->addFormField( $element_id, $elem );
		}

		// Add form sections, if any
		$form->addFormSections( $sections );

		// And finally render the form
		$form->renderForm();

		$this->deleteKeepedUserData();

		echo '<pre>';
		print_r( get_transient( 'zwe_meta_errors' ) );
		print_r( get_transient( 'zwe_meta_user_data' ) );
		print_r( get_post_meta( $post->ID ) );
		echo '</pre>';
	}

	public function saveMetabox( $post_id ){


		// Create a new save object
		$save = new Save;

		// Prepare current object
		$save->moduleId = $this->id;
		$save->module   = $this->module;
		$save->postId   = $post_id;

		// Make sure to load the fields for backend use
		$save->view     = 'backend';

		// Make it ready for saving
		$save->ready();

		update_option( 'xwert', $save->formData );
		// Save data. See Save::saveData() for details.
		$save_message = $save->validNonce()->hasData()->validData()->save()->complete();

		// Log errors if there are some
		if( $this->haveErrorsOnSubmit( $save_message ) ){
			$this->logErrors( $save_message['errors'] );

			// If errors are present, keep the user data to fill the fields that has not been saved yet.
			$data = $save->getData();

			if( !empty($data) && !empty($data[ $this->id ]) ){
				$this->keepUserData( $data[ $this->id ] );
			}
		}


		//For debuging. TODO: To be removed.
		// update_option('zwe_log_form_post_save_post', $_POST);

	}

	//------------------------------------//--------------------------------------//

	/**
	 * Enqueue scripts for this page
	 *
	 * Enqueue fields scripts
	 *
	 * @return void
	 */
	public function adminEnqueue( $hook_suffix ){
		if( in_array($hook_suffix, array('post.php', 'post-new.php') ) ){

			$screen = get_current_screen();

			if( is_object( $screen ) && $this->postType == $screen->post_type ){

				wp_enqueue_style( 'zwe_form' );

				$fields =  apply_filters( 'zwe_'. $this->module .'_'. $this->id .'_manage_fields', array() );
				Form::toEnqueue( $fields, 'backend' );

			}
		}
	}

	public function keepUserData( $new_data ){
		// Old data
		if( ! ( $data = get_transient( 'zwe_meta_user_data' ) ) ){
			$data = array();
		}

		// Merge with new data
		$data[ $this->id ] = (array) $new_data;

		// Save new data
		if( !empty($data) ){
			set_transient( 'zwe_meta_user_data', $data, 30 );
		}
	}

	public function getKeepedUserData(){
		return get_transient( 'zwe_meta_user_data' );
	}

	public function deleteKeepedUserData(){
		$keeped_data = get_transient( 'zwe_meta_user_data' );

		if( isset( $keeped_data[ $this->id ] ) ){
			unset( $keeped_data[ $this->id ] );
		}

		if( empty( $keeped_data ) ){
			delete_transient( 'zwe_meta_user_data' );
		}
		else{
			set_transient( 'zwe_meta_user_data', $keeped_data, 30 );
		}

	}

	//------------------------------------//--------------------------------------//

	/**
	 * Have errors on submit
	 *
	 * Determine if have errors on submittion and return a bool value
	 *
	 * @return bool `true` - means errors are present
	 */
	public function haveErrorsOnSubmit( $save_message ){
		$s = $save_message;
		return ( !empty($s) && !empty($s['status']) && 'fail' == $s['status'] && !empty($s['errors']) );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Log errors
	 *
	 * If the submittion has errors, log them. Later will be available for use all at once.
	 *
	 * @param array $new_errors New erorrs to be added
	 * @return array|void
	 */
	public function logErrors( $new_errors ){
		// Old errors
		if( ! ( $errors = get_transient( 'zwe_meta_errors' ) ) ){
			$errors = array();
		}

		// Merge with new errors
		$errors = $errors + (array) $new_errors;

		// Save new errors
		if( !empty($errors) ){
			set_transient( 'zwe_meta_errors', $errors, 30 );
		}
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Print errors
	 *
	 * Print errors in admin header to be accessible later in JS.
	 *
	 * @return string
	 */
	public function printErrors(){
		if( ( $errors = get_transient( 'zwe_meta_errors' ) ) ){
			$output = '<script>';
			$output .= 'var zwe_meta_errors = '. wp_json_encode( $errors ) .';';
			$output .= '</script>';

			echo $output;

			delete_transient( 'zwe_meta_errors' );
		}
	}

}