<?php
namespace Zwe\Field\Upload;

use Zwe\Field\Base;
use Zwe\Field\Validate;

class BackendUpload extends Base{

	public function defaultSettings(){
		return array(
			'multiple'           => true,
			'ext'                => false, // Allowed extensions. Eg: 'png, txt, mp4'
			'sortable'           => true,
			'label_delete'       => __('Delete', 'zerowp'),
			'label_add_more'     => __('Add more files', 'zerowp'),
			'label_add_plural'   => __('Add files', 'zerowp'),
			'label_add_singular' => __('Select a file', 'zerowp'),
			'label_change'       => __('Replace file', 'zerowp'),
			'frame_title'        => __('Select or upload files', 'zerowp'),
			'frame_button_label' => __('Select', 'zerowp'),
		);
	}

	public static function enqueue(){
		wp_enqueue_media();

		wp_register_script(
			'zwe-backend-field-upload',
			zwe_module_js_url( 'Field/Upload', 'backend-uploader.js' ),
			array( 'jquery', 'zwe-media-upload','jquery-ui-core', 'jquery-ui-sortable' ),
			ZWE_VERSION,
			true
		);

		wp_register_style(
			'zwe-backend-field-upload',
			zwe_global_css_url( 'wp-media-upload.css' ),
			false,
			ZWE_VERSION
		);

		wp_enqueue_style( 'zwe-backend-field-upload' );
		wp_enqueue_script( 'zwe-backend-field-upload' );

	}

	//------------------------------------//--------------------------------------//

	public function render(){
		$uploader_atts = array(
			'class="zwe-media-upload"',
			$this->_dataFrameTitle(),
			$this->_dataFrameMultiple(),
			$this->_dataFrameButtonLabel(),
			$this->_dataFrameMime(),
			'data-upload-base-name="'. esc_attr( $this->getName() ) .'"',
		);

		$output = '';
		$output .= '<div '. join( ' ', $uploader_atts ) .'>';

			//Always empty. Used to set the key in DB if no files were selected.
			$output .= $this->htmlInput(array(
				'type'  => 'hidden',
				'value' => '',
				'name'  => $this->getName(),
			));

			//Define an empty template fo a single element. Used to be cloned when one or more files
			// where uploaded and needs to be appended to container.
			$output .= '<div class="item-template" style="display: none;">'. $this->singleMediaItem( '__media_id__', '__input_base_name__', array(
					'url'   => '__media_url__',
					'size'  => '__media_size__',
					'title' => '__media_title__',
					'id'    => '__media_id__',
					'icon'  => '__media_icon__',
				) ) .'</div>';

			// Make sortable if multiple upload is allowed
			$sortable_class = ( $this->getSetting('sortable') && $this->getSetting('multiple') ) ? ' media-sortable' : '';

			//Container where files will be appended.
			$output .= '<div class="media-container'. $sortable_class .'">';

				$value = $this->getValue();

				if( !empty($value) && is_array($value) ){
					foreach ($value as $media_id) {

						$media_id = absint($media_id);
						$media = $this->_getMedia( $media_id );

						$output .= $this->singleMediaItem( $media_id, esc_attr( $this->getName() ), array(
							'id'    => $media_id,
							'url'   => $media['url'],
							'title' => $media['filename'],
							'size'  => $media['filesizeHumanReadable'],
							'icon'  => basename( $media['icon'], '.png' ),
						) );

					}
				}

			$output .= '</div>';

			$data = ' data-change="'. esc_attr( $this->getSetting( 'label_change' ) ) .'"';
			$data .= ' data-add-plural="'. esc_attr( $this->getSetting( 'label_add_plural' ) ) .'"';
			$data .= ' data-add-singular="'. esc_attr( $this->getSetting( 'label_add_singular' ) ) .'"';
			$data .= ' data-more="'. esc_attr( $this->getSetting( 'label_add_more' ) ) .'"';

			$icon = '<span class="the-icon"><span class="dashicons dashicons-plus"></span></span>';
			$text = '<span class="text">'. $this->getSetting('label_add_multiple') .'</span>';

			$output .= '<div class="media-button add-media"'. $data .'>'. $icon . $text .'</div>';
		$output .= '</div>';


		return $output;
	}

	//------------------------------------//--------------------------------------//

	public function singleMediaItem( $value, $input_base_name = '', $details = array() ){
		$output = '';

		$output .= '<div class="media-item" data-item-id="'. esc_attr( $value ) .'">';
			$input_atts = array(
				'type'  => 'hidden',
				'value' => $value,
				'name'  => $input_base_name . '[]',
			);

			// Exclude from $_POST
			if( '__media_id__' == $value ){
				$input_atts['disabled'] = 'disabled';
			}

			// Return the default input.
			$output .= $this->htmlInput( $input_atts );

			$output .= '<div class="media-icon"><span class="the-icon dashicons dashicons-media-'. esc_html( $details['icon'] ) .'"></span></div>';
			$output .= '<div class="media-details">
				<span class="title">'. esc_html( $details['title'] ) .'</span>
				<span class="size">'. esc_html( $details['size'] ) .'</span>
			</div>';
			$output .= '<div class="media-delete" title="'. esc_attr( $this->getSetting('label_delete') ) .'">
				<span class="the-icon dashicons dashicons-trash"></span>
			</div>';
		$output .= '</div>';

		return $output;
	}

	//------------------------------------//--------------------------------------//

	public function _getMedia( $attachment_id, $key = false ){
		$media_details = wp_prepare_attachment_for_js( absint( $attachment_id ) );

		if( $key && isset( $media_details[ $key ] ) ){
			return $media_details[ $key ];
		}

		return $media_details;
	}



	//------------------------------------//--------------------------------------//

	public function _dataFrameTitle(){
		if(  !empty( $this->getSetting( 'frame_title' ) )  ){
			return 'data-frame-title="'. esc_attr( esc_html( $this->getSetting( 'frame_title' ) ) ) .'"';
		}

		return '';
	}

	//------------------------------------//--------------------------------------//

	public function _dataFrameButtonLabel(){
		if(  !empty( $this->getSetting( 'frame_button_label' ) )  ){
			return 'data-frame-button-label="'. esc_attr( esc_html( $this->getSetting( 'frame_button_label' ) ) ) .'"';
		}

		return __('Select', 'zerowp');
	}

	//------------------------------------//--------------------------------------//

	public function _dataFrameMultiple(){
		$mt = absint( $this->getSetting( 'multiple' ) );

		if( !empty( $mt ) && $mt == 1 ){
			return 'data-multiple="yes"';
		}
		elseif( !empty( $mt ) && $mt > 1 ){
			return 'data-multiple="'+ $mt +'"';
		}
		else{
			return 'data-multiple="no"';
		}
	}

	//------------------------------------//--------------------------------------//

	public function _isMultiple(){
		return ( absint( $this->getSetting( 'multiple' ) ) > 0 );
	}

	//------------------------------------//--------------------------------------//

	public function _dataFrameMime(){
		$extensions = $this->getSetting( 'ext' );

		if( !empty( $extensions ) ){
			$mimes = $this->_getMimeByExt( $extensions );

			if( !empty($mimes) ){
				return 'data-mm="'. esc_attr( join( ',', $mimes ) ) .'"';
			}
		}

		return '';
	}

	//------------------------------------//--------------------------------------//

	public function _getMimeByExt( $extensions ){
		$return_mimes = array();
		$mime_types =  wp_get_mime_types();

		if( empty($extensions) )
			return false;

		$extensions = (array) $extensions;

		foreach ( $extensions as $extension) {
			foreach ( $mime_types as $exts => $mime ) {
				if ( preg_match( '!^(' . $exts . ')$!i', $extension ) ) {
					$return_mimes[] = $mime;
					break;
				}
			}
		}

		if( empty( $return_mimes ) )
			return false;

		return $return_mimes;
	}

	//------------------------------------//--------------------------------------//

	public static function validate( $type, $value, $settings ){
		$validate = new Validate( $type, $value, $settings );
		return $validate->on()->_required()->getError();

		/*
		TODO:
		  - Verify mime types
		  - Check if current user can use the selected media( maybe `current_user_can('edit_files')` allow to use any file, else only files own uploaded files. )
		*/
	}

	//------------------------------------//--------------------------------------//

	public static function sanitize( $value, $settings ){
		return $value;
	}

}