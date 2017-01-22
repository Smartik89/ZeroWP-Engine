<?php
/**
* Save an options page
*
* Provides the required functions for saving an options page.
*
* @since 1.0
*
*/
namespace Zwe\OptionsPage;

use Zwe\Form\AbstractSave;

class Save extends AbstractSave{

	public function saveData( $data ){
		$old_data = get_option( $this->moduleId, array() );
		$new_data = wp_parse_args( $data, $old_data );

		update_option( $this->moduleId, $new_data );

		return $new_data;
	}

}