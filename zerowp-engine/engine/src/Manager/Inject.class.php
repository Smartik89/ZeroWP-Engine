<?php
/**
 * Inject
 *
 * Inject content in manager.
 *
 * @since 1.0
 */
namespace Zwe\Manager;

class Inject{

	public $args;
	public $id;

	public function __construct( $filter, $id, $args, $priority ){
		$this->id = $id;
		$this->args = $args;

		add_filter( $filter, array($this, 'manage'), absint($priority) );
	}

	public function manage( $elements ){
		if( ! array_key_exists($this->id, (array) $elements) ){
			$elements[ $this->id ] = $this->args;
		}
		return $elements;
	}

}