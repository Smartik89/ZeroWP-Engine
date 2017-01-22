<?php
/**
* Save a metabox
*
* Provides the required functions for saving a metabox.
*
* @since 1.0
*
*/
namespace Zwe\Metabox;

use Zwe\Form\AbstractSave;

class Save extends AbstractSave{

	public $postId;

	public function filterData(){
		$this->fieldsData = $this->formData;
		return parent::filterData();
	}

	public function saveData( $data ){
		if( !empty($this->postId) ){
			if( is_array( $data ) ){

				if( !empty($this->formFields) && is_array( $this->formFields ) ){
					foreach ($this->formFields as $field_id => $field) {
						if( isset($data[ $field_id ]) ){
							update_post_meta( $this->postId, $field_id, $data[ $field_id ] );
						}
					}
				}

			}
		}
	}

	public function getData(){
		return ( isset( $_POST ) ) ? $_POST : false;
	}

	public function verifyNonce(){
		if( !empty( $this->formData["_form_nonce_{$this->moduleId}"] ) ){

			return (bool) wp_verify_nonce(
				$this->formData["_form_nonce_{$this->moduleId}"],
				"zwe_{$this->module}_{$this->moduleId}_nonce"
			);

		}
		else{
			return false;
		}
	}

}