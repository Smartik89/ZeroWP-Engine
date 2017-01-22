<?php
namespace Zwe\Field\Text;

use Zwe\Field\Base;
use Zwe\Field\Validate;

class BackendText extends Base{

	public $input_type = 'text';

	public function defaultSettings(){
		return array(
			'allow_safe_html' => false,
			'size'            => 'regular',
			'text_before'     => false,
			'text_after'      => false,
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
			'data-can-be-parent' => true,
		));

		$output .= ( $after = $this->getSetting('text_after') ) ? ' '. $after : '';

		return $output;
	}

	public static function validate( $type, $value, $settings ){
		$validate = new Validate( $type, $value, $settings );
		return $validate->on()->_required()->_minLength()->_maxLength()->getError();
	}

	public static function sanitize( $value, $settings ){
		$allow_safe_html = $settings['allow_safe_html'];
		return ( !empty($allow_safe_html) ) ? wp_kses_data( $value ) : esc_html( $value );
	}

}