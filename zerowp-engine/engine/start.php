<?php
define('ZWE_VERSION', '1.0');

/**
 * Get engine Root URL
 *
 * Get engine Root URL
 *
 * @return string
 */
if( ! function_exists('zwe_root_url') ){
	function zwe_root_url(){
		return plugin_dir_url(__FILE__);
	}
}

//------------------------------------//--------------------------------------//

/**
 * Get engine Root PATH
 *
 * Get engine Root PATH
 *
 * @return string
 */
if( ! function_exists('zwe_root_path') ){
	function zwe_root_path(){
		return plugin_dir_path(__FILE__);
	}
}

//------------------------------------//--------------------------------------//

/**
 * Include the loader
 *
 * Load all engine classes
 *
 */
include zwe_root_path() . "autoloader.php";

//------------------------------------//--------------------------------------//

/**
 * Engine main constants
 *
 * Engine main constants
 *
 */
define('ZWE_PATH', zwe_root_path() );
define('ZWE_URI', zwe_root_url() );
define('ZWE_URL', ZWE_URI );//alternative for 'ZWE_URI'

/*
-------------------------------------------------------------------------------
URL Access
-------------------------------------------------------------------------------
*/
function zwe_module_url( $module_name ){
	return zwe_root_url() . 'src/'. $module_name .'/';
}
function zwe_module_css_url( $module_name, $file = '' ){
	return zwe_root_url() . 'src/'. $module_name .'/assets/css/' . $file;
}
function zwe_module_js_url( $module_name, $file = '' ){
	return zwe_root_url() . 'src/'. $module_name .'/assets/js/' . $file;
}
function zwe_global_css_url( $file = '' ){
	return zwe_root_url() . 'src/global-assets/css/' . $file;
}
function zwe_global_js_url( $file = '' ){
	return zwe_root_url() . 'src/global-assets/js/' . $file;
}

/*
-------------------------------------------------------------------------------
Get filters
-------------------------------------------------------------------------------
*/
function zwe_module_filter_string( $module ){
	return "zwe_{$module}_childs";
}

function zwe_module_childs( $module ){
	return apply_filters( zwe_module_filter_string( $module ), array() );
}

function zwe_module_child( $module, $id ){
	$s = zwe_module_childs( $module );

	return isset( $s[ $id ] ) ? $s[ $id ] : false;
}

function zwe_get_field_classname( $field_type, $view = 'backend' ){
	$all_registered_fields = zwe_module_childs( 'Field' );

	//Check if the field type is registered globally
	if( ! array_key_exists($field_type, $all_registered_fields) )
		return false;

	//Get available classes for this field
	$field_classes = $all_registered_fields[ $field_type ];

	//Get the classname for this field
	$class_name = $field_classes[ $view ];

	//Check if the class exists
	if( ! class_exists($class_name) )
		return false;

	return $class_name;
}


/*
-------------------------------------------------------------------------------
Global scripts and styles registration
-------------------------------------------------------------------------------
*/
function zwe_global_assets_register(){

	// Global form styles
	wp_register_style(
		'zwe_form',
		zwe_global_css_url( 'form.css' ),
		false,
		ZWE_VERSION
	);

	//Custom Media Uploader Plugin
	wp_register_script(
		'zwe-media-upload',
		zwe_global_js_url( 'wp-media-upload.js' ),
		array( 'jquery' ),
		ZWE_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'zwe_global_assets_register' );

//------------------------------------//--------------------------------------//

/**
 * Register builtin fields
 *
 * The following code will register all the builtin fields automatically.
 *
 */
$available_fields = glob(zwe_root_path() .'src/Field/*/');
foreach ($available_fields as $field) {
	$fieldname = basename($field);
	new Zwe\Field\Register( $fieldname, array(
		'backend'  => 'Zwe\\Field\\'. $fieldname .'\\Backend'. $fieldname .'',
		'frontend' => 'Zwe\\Field\\'. $fieldname .'\\Frontend'. $fieldname .'',
	));
}

//------------------------------------//--------------------------------------//

/**
 * Table form row callback
 *
 * @param string $settings Field settings
 * @param string $field_html The field html. Each field type has it's own HTML output.
 * @return string Row markup with the field.
 */
function zwe_form_table_row_callback($id, $value, $settings, $field_html, $field_class_instance ){
	$description = ( !empty($settings['description']) ) ? '<p class="description">'. $settings['description'] .'</p>' : '';
	$label = ( isset($settings['label']) && false !== $settings['label'] ) ? esc_html( $settings['label'] ) : false;
	$colspan = ( false === $label ) ? ' colspan="2" style="padding-left: 0;"' : '';

	$row = '<tr'. Zwe\Form\Form::rowAttributes( $field_class_instance, $settings, array() ) .'>';
		if( false !== $label ){
			$row .= '<th scope="row">'. $label .'</th>';
		}
		$row .= '<td'. $colspan .'>'. $field_html . $description .'</td>';
	$row .= '</tr>';

	return $row;
}

// function test_req_number( $obj ){
// 	$obj->_required();
// 	return $obj;
// }
// add_filter( 'zwe_field_validate:number', 'test_req_number' );