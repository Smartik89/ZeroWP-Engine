<?php
namespace Zwe\Field\Number;

use Zwe\Field\Base;
use Zwe\Field\Validate;

class BackendNumber extends Base{

	public $input_type = 'number';

	public function defaultSettings(){
		return array(
			'size'        => 'regular',
			'text_before' => false,
			'text_after'  => false,
			'_min'        => false,
			'_max'        => false,
			'_step'       => false,
			'_isnumber'   => false,
		);
	}

	public function render(){
		$output = '';

		$output .= ( $before = $this->getSetting('text_before') ) ? $before .' ' : '';

		$output .= $this->htmlInput(array(
			'type'  => $this->input_type,
			'value' => $this->getValue(),
			'name'  => $this->getName(),
			'class' => $this->htmlInputClass(),
			// 'min'   => $this->getSetting( '_min' ),
			// 'max'   => $this->getSetting( '_max' ),
			'step'  => $this->getSetting( '_step' ),
			'data-can-be-parent' => true,
		));

		$output .= ( $after = $this->getSetting('text_after') ) ? ' '. $after : '';

		return $output;
	}

	public static function validate( $type, $value, $settings ){
		$validate = new Validate( $type, $value, $settings );
		return $validate->on()->_required()->_isNumber()->_min()->_max()->_step()->getError();
	}

	public static function sanitize( $value, $settings ){
		return $value;
	}

}