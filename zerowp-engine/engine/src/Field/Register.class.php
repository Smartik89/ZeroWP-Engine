<?php
/**
* Register a field
*
* Adds a field to the list of known fields types
*
* @since 1.0
*
*/
namespace Zwe\Field;

class Register{

	/* Field type
	------------------*/
	public $type;

	/* Classname used by this field
	------------------------------------*/
	public $class;

	//------------------------------------//--------------------------------------//

	/**
	 * Ccnstruct
	 *
	 * @param string $type_name The field type.
	 * @param string $class_name The class name used by this field
	 * @return void
	 */
	public function __construct( $type_name, $class_name = false ){
		$this->type = strtolower( $type_name );
		$this->class = $class_name;

		add_filter( zwe_module_filter_string( 'Field' ), array( $this, 'add' ) );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Add field
	 *
	 * Add field to the list of registered fields
	 *
	 * @param array $fields_array Gets the fields array placeholder. It contains
	 * the fields that has been registered already.
	 * @return array All registered fields, including that one.
	 */
	public function add( $fields_array ){
		if( !array_key_exists($this->type, $fields_array) && !empty($this->class) ){
			$fields_array[ $this->type ] = $this->class;
		}
		return $fields_array;
	}

}