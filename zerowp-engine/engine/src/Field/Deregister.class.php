<?php
/**
* Deregister a field
*
* Removes a field from the list of known fields types
*
* @since 1.0
*
*/
namespace Zwe\Field;

class Deregister{

	/* Field type
	------------------*/
	public $type;

	//------------------------------------//--------------------------------------//

	/**
	 * Ccnstruct
	 *
	 * @param string $type_name The field type.
	 * @return void
	 */
	public function __construct( $type_name ){
		$this->type = $type_name;

		add_filter( zwe_module_filter_string( 'Field' ), array( $this, 'delete' ) );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Delete field
	 *
	 * Delete a fields that has been registered.
	 *
	 * @param array $fields_array Gets the fields array placeholder. It contains
	 * the fields that has been registered already.
	 * @return array All registered fields, if any or an empty array.
	 */
	public function delete( $fields_array ){
		if( isset($this->type) ){
			unset( $fields_array[ $this->type ] );
		}
		return $fields_array;
	}

}