<?php
/**
* Register an new options page
*
* @since 1.0
*
*/
namespace Zwe\OptionsPage;

use Zwe\Model\AbstractModule;
use Zwe\Manager\Inject;
use Zwe\Form\Form;

class Register extends AbstractModule{

	public $module = 'OptionsPage';

	public function __construct( $module_id, $custom_settings = false ){
		$this->id             = $module_id;
		$this->customSettings = $custom_settings;

		// Register a new module if another one with this ID does not exists
		if( ! $this->moduleIsRegistered() ){
			new Inject( zwe_module_filter_string( $this->module ), $this->id, $this->getSettings(), 30 );

			// Registration actions
			add_action( 'admin_menu', array($this, 'createMenu'), 30 );
			add_action( 'admin_enqueue_scripts', array($this, 'adminEnqueue'), 30 );

			// AJAX handler to save options
			add_action( 'wp_ajax_zwe_OptionsPage_submit', array( $this, 'saveOptions' ) );
		}
	}


	public function defaultSettings(){
		return array(
			'capability' => 'manage_options',
			'parent'     => 'options-general.php',
			'icon'       => '',
			'position'   => null,
		);
	}

	public function createMenu(){
		$settings = $this->getSettings();

		if( $settings['parent'] === false ){
			add_menu_page(
				$settings['title'],          // Page title
				$settings['title'],          // Menu title
				$settings['capability'],     // Capability
				$this->id,                   // Menu slug
				array($this, 'displayPage'), // Calback function to display the page contents
				$settings['icon'],           // The icon url or "Dashicons" class
				$settings['position']        // Menu position
			);
		}
		else{
			add_submenu_page(
				$settings['parent'],        // Parent page slug
				$settings['title'],         // Page title
				$settings['title'],         // Menu title
				$settings['capability'],    // Capability
				$this->id,                  // Menu slug
				array($this, 'displayPage') // Calback function to display the page contents
			);
		}
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Render page
	 *
	 * This is the final markup
	 *
	 * @param string
	 * @return string
	 */
	public function displayPage(){
		echo '<div class="wrap">';

			$form = new Form( $this->id, array(
				'module'        => 'OptionsPage',
				'before_form'   => '<div id="zwe-options-page-container" class="zwe-form-container">',
				'after_form'    => '</div>',
				'before_fields' => '<table class="form-table"><tbody>',
				'after_fields'  => '</tbody></table>',
				'row_callback'  => 'zwe_form_table_row_callback',
			) );

			$fields = apply_filters( 'zwe_OptionsPage_'. $this->id .'_manage_fields', array() );
			$sections = apply_filters( 'zwe_OptionsPage_'. $this->id .'_manage_sections', array() );

			// Get previously saved data from DB
			$saved_options = get_option( $this->id );
			foreach ( $fields as $element_id => $elem) {
				if( isset($saved_options[ $element_id ]) ){
					$elem['value'] = $saved_options[ $element_id ];
				}
				$form->addFormField( $element_id, $elem );
			}

			$form->addFormSections( $sections );
			$form->renderForm();

			echo '<pre>';
			print_r( $form->formFields );
			print_r( $this->getSettings() );
			echo '</pre>';

		echo '</div>'; // .wrap
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Enqueue scripts for this page
	 *
	 * Enqueue fields scripts
	 *
	 * @return void
	 */
	public function adminEnqueue(){
		if( isset($_GET['page']) && $_GET['page'] == $this->id ){
			$version = ZWE_VERSION;
			$prefix  = 'zwe-'. $this->module .'-';

			// Register scripts and styles
			wp_register_script( $prefix .'scripts', zwe_module_js_url( $this->module, 'scripts.js' ), array( 'jquery' ), $version, true );

			// Enqueue scripts and styles
			wp_enqueue_style( 'zwe_form' );
			wp_enqueue_script( $prefix .'scripts' );

			// Scripts and styles based on fields included in a particular options page
			$fields =  apply_filters( 'zwe_'. $this->module .'_'. $this->id .'_manage_fields', array() );
			Form::toEnqueue( $fields, 'backend' );
		}
	}

	//------------------------------------//--------------------------------------//

	/**
	 * AJAX handler to save options
	 *
	 * Here is the final process. The options will be saved in DB if there are no validation errors
	 *
	 * @return JSON
	 */
	public function saveOptions(){

		// Create a new save object
		$save = new Save;

		// Prepare current object
		$save->module = $this->module;

		// Make sure to load the fields for backend use
		$save->view   = 'backend';

		// Make it ready for saving
		$save->ready();

		// $this->_d( 'validNonce', $save->validNonce() );
		// $this->_d( 'validUser', $save->validUser() );
		// $this->_d( 'hasData', $save->hasData() );
		// $this->_d( 'validData', $save->validData() );
		// $this->_d( 'save', $save->save() );
		// $this->_d( 'completeJson', $save->completeJson() );

		// die();

		update_option( 'xwert', $_POST );

		// Save data. See Save::saveData() for details.
		if( $save->isAction() ){
			$ajax_message = $save->validNonce()->validUser()->hasData()->validData()->save()->completeJson();
		}
		else{
			$ajax_message = null;
		}

		die( $ajax_message ); // Stop ajax request.
	}

	public function _d( $name, $val ){
		if( isset( $this->debug ) ){
			$opt = get_option( 'option_page_debug', array() );
		}
		else{
			$opt = array();
			$this->debug = true;
		}

		$opt[ $name ] = (array) $val;

		update_option( 'option_page_debug', $opt );
	}

}