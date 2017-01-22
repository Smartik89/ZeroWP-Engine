<?php
namespace Zwe\Field\Textarea;

use Zwe\Field\Base;
use Zwe\Field\Validate;

class BackendTextarea extends Base{

	public function defaultSettings(){
		return array(
			'rows' => 5, //number of rows(int)
			'size' => 'large', // 'large' or number of columns(int)
			'allow_html' => true, // true, false, 'raw' or 'limited'
		);
	}

	public function render(){
		//Number of rows
		$rows = ( $newrows = absint( $this->getSetting( 'rows' ) ) ) > 0 ? $newrows : 5;

		// Input width
		$size = $this->getSetting( 'size' );
		$size_attr = '';

		if( !empty($size) && ($size = trim( $size )) ){
			if( in_array( $size, array('wide', 'widefat', 'large') ) ){
				$size_attr = ' class="widefat"';
			}
			elseif( is_numeric( $size ) ){
				$size_attr = ' cols="'. absint( $size ) .'"';
			}
		}

		return '<textarea name="'. $this->getName() .'"'. $size_attr .' rows="'. $rows .'">'. esc_textarea( $this->getValue() ) .'</textarea>';
	}

	public static function validate( $type, $value, $settings ){
		$value = wp_strip_all_tags( $value ); // Get only text without html tags, usefull to get the real text length

		$validate = new Validate( $type, $value, $settings );
		return $validate->on()->_required()->_minLength()->_maxLength()->getError();
	}

	public static function sanitize( $value, $settings ){
		$allow_html = $settings['allow_html'];

		// Sanitize
		if( 'limited' == $allow_html ){
			$value = wp_kses_data( $value ); // Only some inline tags
		}
		elseif( 'raw' == $allow_html ){
			$value = $value; // Any HTML tags and attr, even 'script'. RAW
		}
		elseif( $allow_html === false ){
			$value = strip_tags( $value ); // No tags allowed at all
		}
		else{
			$value = wp_kses_post( $value ); // Default. Can use only the tags that are allowed in posts.
		}

		return $value;
	}

}