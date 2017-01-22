<?php
namespace Zwe\Field\Select;

use Zwe\Field\Base;
use Zwe\Field\Validate;

class BackendSelect extends Base{

	public function defaultSettings(){
		return array(
			'size'            => false,
			'text_before'     => false,
			'text_after'      => false,
			'options'         => false,
			'empty_option'    => true,
		);
	}

	public function render(){
		$output = '';

		$output .= ( $before = $this->getSetting('text_before') ) ? $before .' ' : '';

		$output .= $this->htmlSelect( $this->getSetting('options'), $this->getValue(), array(
			'name'  => $this->getName(),
			'empty_option' => $this->getSetting('empty_option'),
			'data-can-be-parent' => true,
		));

		$output .= ( $after = $this->getSetting('text_after') ) ? ' '. $after : '';

		return $output;
	}

	public static function validate( $type, $value, $settings ){
		$validate = new Validate( $type, $value, $settings );
		return $validate->on()->_required()->_selectedOption()->getError();
	}

	public static function sanitize( $value, $settings ){
		return $value;
	}

}