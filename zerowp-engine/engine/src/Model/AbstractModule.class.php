<?php
/**
* Abstract Module Model
*
* @since 1.0
*
*/
namespace Zwe\Model;

abstract class AbstractModule{

	public $id;
	public $customSettings;

	protected function _generalDefaultSettings(){
		return array(
			'msg:not_allowed'    => __('You are not allowed to save!', 'zerowp'),
			'msg:success'        => __('The form has been submitted successfully.', 'zerowp'),
			'msg:fail'           => __('There are some errors. Please fix them and resubmit the form.', 'zerowp'),
			'msg:nothing'        => __('Nothing to save!', 'zerowp'),
			'msg:invalid_nonce'  => __('Unknown error!', 'zerowp'),
			'title'              => $this->id,
			'capability'         => 'publish_posts',
		);
	}

	public function defaultSettings(){
		return false;
	}

	public function getSettings(){
		$settings = $this->_generalDefaultSettings();
		$module_default_settings = $this->defaultSettings();

		// Module specific default settings
		if( !empty($module_default_settings) ){
			$settings = wp_parse_args( $module_default_settings, $settings );
		}

		// Custom settings when a user creates a new module instance
		if( !empty( $this->customSettings ) ){
			$settings = wp_parse_args( $this->customSettings, $settings );
		}

		return $settings;
	}

	public function moduleIsRegistered(){
		$modules = zwe_module_childs( $this->module );

		return ( array_key_exists( $this->id, $modules ) );
	}

}