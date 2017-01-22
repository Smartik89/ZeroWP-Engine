<?php
namespace Zwe\Field\Wpeditor;

use Zwe\Field\Base;
use Zwe\Field\Validate;

class BackendWpeditor extends Base{

	public function defaultSettings(){
		return array(
			'allow_html' => true, // true, false, 'raw' or 'limited'

			//Custom editor settings. See: https://codex.wordpress.org/Function_Reference/wp_editor#Arguments
			'editor_settings' => false,
		);
	}

	public function mustUseEditorSettings(){
		return array(
			'textarea_name' => $this->getName(),
		);
	}

	public function render(){
		$editor_id = strtolower( str_ireplace(array('-', '[', ']'), '_', $this->getName()) );

		$editor_setting = $this->getSetting('editor_settings');
		$custom_editor_settings = !empty($editor_setting) && is_array($editor_setting) ? $editor_setting : array();
		$editor_settings = wp_parse_args( $this->mustUseEditorSettings(), $custom_editor_settings );

		ob_start();
		wp_editor( $this->getValue(), $editor_id, $editor_settings);

		$editor = ob_get_contents();
		ob_end_clean();

		return $editor;
	}

	public static function validate( $type, $value, $settings ){
		$value = str_ireplace( '&nbsp;', '', $value ); // Removes white space added by tinymce
		$value = trim( wp_strip_all_tags( $value ) ); // Get only text without html tags, usefull to get the real text length

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