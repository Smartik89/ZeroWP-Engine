<?php
/**
* Manager
*
* Get access to a module by ID and manage its content(fields, sections, etc).
*
* @since 1.0
*
*/
namespace Zwe\Manager;

abstract class AbstractManager{

	protected $moduleType; //Module type(ie: OptionsPage, Metabox, etc).
	protected $moduleId; //The module ID to get access

	public $currentSection = null;
	public $currentPanel = null;

	//------------------------------------//--------------------------------------//

	/**
	 * Construct manager
	 *
	 * @param string $module_type (ie: OptionsPage, Metabox, etc.)
	 * @return void
	 */
	public function __construct( $module_id ){
		$this->moduleId = $module_id;
	}

	/*
	-------------------------------------------------------------------------------
	Fields
	-------------------------------------------------------------------------------
	*/
	public function addField( $id, $type, $value, $settings = false, $priority = 30 ){
		$filter = 'zwe_'. $this->moduleType .'_'. $this->moduleId .'_manage_fields';

		$args = array(
			'id' => $id,
			'type' => $type,
			'value' => $value,
			'section' => $this->currentSection,
			'settings' => $settings,
		);

		new Inject( $filter, $id, $args, $priority );

		return $this;
	}

	public function removeField( $id, $priority = 30 ){
		$filter = 'zwe_'. $this->moduleType .'_'. $this->moduleId .'_manage_fields';

		new Eject( $filter, $id, $priority );

		return $this;
	}

	//Add the field and return settings to developer
	public function __addField( $id, $type, $value, $settings = false, $priority = 30 ){

		$settings = wp_parse_args(
			array( '__dump_settings' => true ),
			( array ) $settings
		);

		return $this->addField( $id, $type, $value, $settings, $priority );
	}

	/*
	-------------------------------------------------------------------------------
	Sections
	-------------------------------------------------------------------------------
	*/
	public function addSection( $id, $settings = false, $priority = 30 ){
		$filter = 'zwe_'. $this->moduleType .'_'. $this->moduleId .'_manage_sections';

		$args = array(
			'id' => $id,
			'settings' => $settings,
		);

		new Inject( $filter, $id, $args, $priority );

		$this->currentSection = $id;

		return $this;
	}

	public function removeSection( $id, $priority = 30 ){
		$filter = 'zwe_'. $this->moduleType .'_'. $this->moduleId .'_manage_sections';

		new Eject( $filter, $id, $priority );
		$this->currentSection = null;

		return $this;
	}

	public function openSection( $id ){
		$this->currentSection = $id;

		return $this;
	}

	public function closeSection( $id ){
		$this->currentSection = null;

		return $this;
	}

	/*
	-------------------------------------------------------------------------------
	Panels
	-------------------------------------------------------------------------------
	*/
	// TODO: Create panels and add the necesary methods
	public function addPanel( $id, $settings = false, $priority = 30 ){}

	public function removePanel( $id ){}

	public function openPanel( $id ){}

	public function closePanel( $id ){}

}