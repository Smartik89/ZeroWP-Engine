<?php
/**
 * Test field variations
 *
 * This test will create an options page with all possible variations.
 *
 */
new Zwe\OptionsPage\Register('zwe-test-field-variations', array(
	'parent' => false,
	'title' => 'Field variations',
));

$options = new Zwe\OptionsPage\Manage('zwe-test-field-variations');

//------------------------------------//--------------------------------------//

$input_types = array( 'text', 'number' );

foreach ($input_types as $type) {
	$options->addSection( 'test-input-variations-' . $type, array(
		'label' => ucfirst( $type ),
		'description' => 'Test field type ' . $type,
	) );

	$options->addField( 'size-not-defined-' . $type, $type, '', array(
		'label' => 'Size not defined',
	));

	$options->addField( 'size-small-' . $type, $type, '', array(
		'label' => 'Size small',
		'size' => 'small',
		'description' => "<code>'size' => 'small'</code>"
	));

	$options->addField( 'size-regular-text-' . $type, $type, '', array(
		'label' => 'Size regular-text',
		'size' => 'regular-text',
		'description' => "<code>'size' => 'regular-text'</code>",
	));

	$options->addField( 'size-widefat-' . $type, $type, '', array(
		'label' => 'Size widefat',
		'size' => 'widefat',
		'description' => "<code>'size' => 'widefat'</code>"
	));

	$options->addField( 'size-none-' . $type, $type, '', array(
		'label' => 'Size none',
		'size' => 'none',
		'description' => "<code>'size' => 'none'</code>"
	));

	$options->addField( 'text-before-after-' . $type, $type, '', array(
		'label' => 'Text before and after',
		'text_before' => 'John is',
		'text_after' => 'years old',
		'size' => 'small',
		'description' => "<code>'size' => 'small'</code> <code>'text_before' => 'John is'</code> <code>'text_after' => 'years old'</code>"
	));
}

/*------------------------------------//--------------------------------------*/

$options->addSection( 'test-textarea-variations', array(
	'label' => 'Textarea',
	'description' => 'Test field type textarea',
) );

$options->addField( 'textarea_default', 'textarea', '', array(
	'label' => 'Default textarea',
));

$options->addField( 'textarea_80_cols', 'textarea', '', array(
	'label' => 'Textarea 80 cols',
	'size' => 80,
	'description' => "<code>'size' => 80</code>",
));

$options->addField( 'textarea_20_rows', 'textarea', '', array(
	'label' => 'Textarea 20 rows',
	'rows' => 20,
	'description' => "<code>'rows' => 20</code>",
));

$options->addField( 'textarea_20_rows', 'textarea', '', array(
	'label' => 'Textarea 20 rows',
	'rows' => 20,
	'description' => "<code>'rows' => 20</code>",
));

//------------------------------------//--------------------------------------//

$options->addSection( 'test-select-variations', array(
	'label' => 'Select',
	'description' => 'Test field type select',
) );

$options->addField( 'select_default', 'select', '', array(
	'label' => 'Select - default',
	'options' => array(
		'opt1' => 'My option 1',
		'opt2' => 'My option 2',
		'opt3' => 'My option 3',
		'opt4' => 'My option 4',
		'opt5' => 'My option 5',
	),
));
$options->addField( 'select_empty_option_false', 'select', '', array(
	'label' => 'Select - no empty option',
	'empty_option' => false,
	'description' => "<code>'empty_option' => false</code>",
	'options' => array(
		'opt1' => 'My option 1',
		'opt2' => 'My option 2',
		'opt3' => 'My option 3',
		'opt4' => 'My option 4',
		'opt5' => 'My option 5',
	),
));
$options->addField( 'select_empty_option_custom', 'select', '', array(
	'label' => 'Select - custom empty option',
	'empty_option' => ' -- Select an option -- ',
	'description' => "<code>'empty_option' => ' -- Select an option -- '</code>",
	'options' => array(
		'opt1' => 'My option 1',
		'opt2' => 'My option 2',
		'opt3' => 'My option 3',
		'opt4' => 'My option 4',
		'opt5' => 'My option 5',
	),
));
$options->addField( 'select_empty_option_selected', 'select', 'opt3', array(
	'label' => 'Select - selected by default',
	'options' => array(
		'opt1' => 'My option 1',
		'opt2' => 'My option 2',
		'opt3' => 'My option 3',
		'opt4' => 'My option 4',
		'opt5' => 'My option 5',
	),
));

//------------------------------------//--------------------------------------//

$options->addSection( 'test-upload-variations', array(
	'label' => 'Upload variations',
	'description' => 'Test upload field variations',
) );

$options->addField( 'upload_default', 'upload', '', array(
	'label' => 'Upload media',
	'ext' => array( 'jpeg', 'png', 'docx', 'txt', 'psd' ),
));

$options->addField( 'upload_new_2', 'upload', '', array(
	'label' => 'Upload media',
	'ext' => 'zip',
));

$options->addField( 'upload_new_3', 'upload', '', array(
	'label' => 'Multiple false',
	'multiple' => false,
));

$options->addField( 'upload_new_4', 'upload', '', array(
	'label' => 'Multiple false and required',
	'multiple' => false,
	'_required' => 'Please select a file from media library',
));