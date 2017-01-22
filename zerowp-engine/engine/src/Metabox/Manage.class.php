<?php
/**
* Manage Metabox
*
* Manage a registered metabox.
*
* @since 1.0
*
*/
namespace Zwe\Metabox;

use Zwe\Manager\AbstractManager;

class Manage extends AbstractManager{

	public function __construct( $post_type, $module_id ){
		$this->moduleId = $module_id;
		$this->moduleType = "Metabox_{$post_type}";
	}

}