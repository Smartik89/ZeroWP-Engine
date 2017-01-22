<?php
/**
 * Validate Field Value
 *
 * Field validation class. Validate data based on field settings.
 *
 * @since 1.0
 */
namespace Zwe\Field;

class Validate{

	public $type;
	public $value;
	public $settings;
	public $error;


	//------------------------------------//--------------------------------------//

	/**
	 * Validate construct
	 *
	 * @param mixed $value The field value to be validated
	 * @param mixed $settings The field settings
	 * @return void
	 */
	public function __construct( $type, $value, $settings ){
		$this->type = $type;
		$this->value = ( is_string( $value ) ) ? trim( $value ) : $value;
		$this->settings = $settings;
		$this->error = false;
	}

	public function on(){
		return apply_filters( "zwe_field_validate:". $this->type, $this );
	}

	/*
	-------------------------------------------------------------------------------
	Validators
	-------------------------------------------------------------------------------
	*/

	//------------------------------------//--------------------------------------//

	/**
	 * Validate required field
	 *
	 * Check if the value is not empty.
	 *
	 * @param string $message Custom validation message
	 *
	 * @return string|bool(false) The valitation status. 'string' - validation error, 'false' - is validated.
	 */
	public function _required(){
		if( $this->error )
			return $this;

		if( !empty( $this->settings['_required'] ) && empty( $this->value ) && !is_numeric( $this->value ) ){
			if( is_string( $this->settings['_required'] ) && !is_numeric($this->settings['_required']) ){
				$this->error = esc_html( $this->settings['_required'] );
			}
			else{
				$this->error = __('This field is required', 'zerowp');
			}
		}
		return $this;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Validate the value as number
	 *
	 * @return mixed
	 */
	public function _isNumber(){
		if( $this->error )
			return $this;

		$msg = __('Invalid number.', 'zerowp');

		if( $this->isNotBlank( $this->value ) ){
			$rule = $this->settings['_isnumber'];

			if( ! is_numeric($this->value) ){
				if( !empty($rule) ){
					$this->error = $this->errorMessage( $rule );
				}
				else{
					$this->error = $this->errorMessage( $msg );
				}
			}
		}
		return $this;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Validate the max length of a value
	 *
	 * @return mixed
	 */
	public function _maxLength(){
		if( $this->error )
			return $this;

		$msg = __('Please enter no more than {{_maxlength}} characters.', 'zerowp');

		if( !empty( $this->settings['_maxlength'] ) && $this->isNotBlank( $this->value ) ){
			$rule = $this->settings['_maxlength'];

			if( is_array($rule) && !empty($rule[0]) && !empty($rule[1]) ){
				if( $this->stringLength( $this->value ) > $rule[0] ){
					$this->error = $this->errorMessage( $rule[1], '{{_maxlength}}', $rule[0] );
				}
			}
			elseif( is_numeric($rule) ){
				if( $this->stringLength( $this->value ) > $rule ){
					$this->error = $this->errorMessage( $msg, '{{_maxlength}}', $rule );
				}
			}
		}
		return $this;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Validate the min length of a value
	 *
	 * @return mixed
	 */
	public function _minLength(){
		if( $this->error )
			return $this;

		$msg = __('Please enter at least {{_minlength}} characters.', 'zerowp');

		if( !empty( $this->settings['_minlength'] ) && $this->isNotBlank( $this->value ) ){
			$rule = $this->settings['_minlength'];

			if( is_array($rule) && !empty($rule[0]) && !empty($rule[1]) ){
				if( $this->stringLength( $this->value ) < $rule[0] ){
					$this->error = $this->errorMessage($rule[1], '{{_minlength}}', $rule[0] );
				}
			}
			elseif( is_numeric($rule) ){
				if( $this->stringLength( $this->value ) < $rule ){
					$this->error = $this->errorMessage( $msg, '{{_minlength}}', $rule );
				}
			}
		}
		return $this;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Validate the min number
	 *
	 * @return mixed
	 */
	public function _min(){
		if( $this->error )
			return $this;

		$msg = __('Value must be greater or equal to {{_min}}.', 'zerowp');

		if( !empty( $this->settings['_min'] ) && $this->isNotBlank( $this->value ) ){
			$the_number = $this->toNumber( $this->value );
			$rule       = $this->settings['_min'];

			if( is_array($rule) && !empty($rule[0]) && !empty($rule[1]) ){
				if( $the_number < $rule[0] ){
					$this->error = $this->errorMessage( $rule[1], '{{_min}}', $rule[0] );
				}
			}
			elseif( is_numeric($rule) ){
				if( $the_number < $rule ){
					$this->error = $this->errorMessage( $msg, '{{_min}}', $rule );
				}
			}
		}
		return $this;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Validate the max number
	 *
	 * @return mixed
	 */
	public function _max(){
		if( $this->error )
			return $this;

		$msg = __('Value must be less than or equal to {{_max}}.', 'zerowp');

		if( !empty( $this->settings['_max'] ) && $this->isNotBlank( $this->value ) ){
			$the_number = $this->toNumber( $this->value );
			$rule       = $this->settings['_max'];

			if( is_array($rule) && !empty($rule[0]) && !empty($rule[1]) ){
				if( $the_number > $rule[0] ){
					$this->error = $this->errorMessage( $rule[1], '{{_max}}', $rule[0] );
				}
			}
			elseif( is_numeric($rule) ){
				if( $the_number > $rule ){
					$this->error = $this->errorMessage( $msg, '{{_max}}', $rule );
				}
			}
		}
		return $this;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Validate the step number
	 *
	 * @return mixed
	 */
	public function _step(){
		if( $this->error )
			return $this;

		$msg = __('Please enter a valid value. The two nearest valid values are {{_step_min}} and {{_step_max}}.', 'zerowp');

		if( !empty( $this->settings['_step'] ) && $this->isNotBlank( $this->value ) ){
			$the_number = $this->toNumber( $this->value );
			$rule       = $this->settings['_step'];

			if( is_array($rule) && !empty($rule[0]) && !empty($rule[1]) ){
				if( $this->calcModulo( $the_number, $rule[0] ) != 0 ){
					$_down = $this->roundDownToAny( $the_number, $rule[0] );
					$_up   = $this->roundUpToAny( $the_number, $rule[0] );
					$this->error = $this->errorMessage( $rule[1], array( '{{_step_min}}', '{{_step_max}}' ), array( $_down, $_up ) );
				}
			}
			elseif( is_numeric($rule) ){
				if( $this->calcModulo( $the_number, $rule ) != 0 ){
					$_down = $this->roundDownToAny( $the_number, $rule );
					$_up   = $this->roundUpToAny( $the_number, $rule );
					$this->error = $this->errorMessage( $msg, array( '{{_step_min}}', '{{_step_max}}' ), array( $_down, $_up ) );
				}
			}
		}

		return $this;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Validate selected option
	 *
	 * Used for fields that have more options(eg: select, radio). Determine if selected option exists
	 *
	 * @param string $message Custom validation message
	 *
	 * @return string|bool(false) The valitation status. 'string' - validation error, 'false' - is validated.
	 */
	public function _selectedOption(){
		if( $this->error )
			return $this;

		$msg = !empty( $this->settings['_required'] ) && is_string( $this->settings['_required'] )
		        ? esc_html( $this->settings['_required'] ) : __('This field is required', 'zerowp');

		if( $this->isNotBlank( $this->value ) ){
			$options = ( !empty($this->settings['options']) ) ? (array) $this->settings['options'] : array();

			if( ! array_key_exists( $this->value, $options ) ){
				$this->error = $this->errorMessage( $msg );
			}
		}

		return $this;
	}

	public function getError(){
		return $this->error;
	}

	/*
	-------------------------------------------------------------------------------
	Helpers
	-------------------------------------------------------------------------------
	*/

	//------------------------------------//--------------------------------------//

	/**
	 * Check if a value is not blank
	 *
	 * Check if a value is not blank
	 *
	 * @param string $val
	 * @return bool `true` if is not blank
	 */
	public function isNotBlank( $val ){
		return ( isset( $val ) && false !== $val && '' !== $val );
	}

	/**
	 * Validation error message
	 *
	 * @param string|array $variables A string or array of strings to be replaced in message
	 * @param string|array $replacements A string or array of strings to be replace with in message
	 * @param string $message The message
	 * @return string The message
	 */
	public function errorMessage( $message, $variables = null, $replacements = null ){
		if( isset($variables) && isset($replacements) ){
			return esc_html( str_ireplace($variables, $replacements, $message) );
		}
		else{
			return esc_html( $message );
		}
	}

	//------------------------------------//--------------------------------------//

	/**
	 * String length
	 *
	 * Get string length value
	 *
	 * @param string $value The value to check
	 * @return string Length or false on failure.
	 */
	public function stringLength( $value ){
		if (!is_string($value)) {
			return false;
		}

		return mb_strlen($value);
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Convert a string to number
	 *
	 * Convert a string to number. Accepts strings that may be integers or floats.
	 *
	 * @param string $value
	 * @return int|float Converted string to number.
	 */
	public function toNumber( $value ){
		if( is_numeric( $value ) ){
			return $value + 0;
		}
		else{
			return $value;
		}
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Calculate modulus
	 *
	 * @param int|float $number The number to calculate
	 * @param int|float $modulo The modulo step
	 * @return int|float 0 on success, other number on failure
	 */
	public function calcModulo($number, $modulo) {
		return $number-$modulo*floor($number/$modulo);
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Round Up To Any
	 *
	 * Round up to the nearest number that can be equally divided by $x
	 *
	 * @param int|float $number The number to round UP
	 * @param int|float $divident
	 * @return int|float Rounded number
	 */
	public function roundUpToAny($number, $divident=1) {
		return (round($number)%$divident === 0) ? round($number) : round(($number+$divident/2)/$divident)*$divident;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Round Down To Any
	 *
	 * Round down to the nearest number that can be equally divided by $x
	 *
	 * @param int|float $number The number to round DOWN
	 * @param int|float $divident
	 * @return int|float Rounded number
	 */
	public function roundDownToAny($number, $divident=1){
		$rounded_up = $this->roundUpToAny( $number, $divident );
		return (round($number)%$divident === 0) ? round($number) : $rounded_up-$divident;
	}


}