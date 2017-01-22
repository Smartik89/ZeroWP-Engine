<?php
/**
 * Test field select
 *
 * This test will create an options page with all possible validation tests.
 *
 */
new Zwe\OptionsPage\Register('zwe-test-field-select', array(
	'parent' => 'zwe-test-field-variations',
	'title' => 'Select validation',
));

$options = new Zwe\OptionsPage\Manage('zwe-test-field-select');

//------------------------------------//--------------------------------------//

$options->addSection( 'validation', array(
	'label' => 'Validation select field',
	'description' => 'Test select field validation',
) );

$options->addField( '_required_true', 'select', '', array(
	'label' => 'Validate required',
	'description' => 'Validate <code>\'_required\' => true</code>',
	'options' => array(
		'opt1' => 'My option 1',
		'opt2' => 'My option 2',
		'opt3' => 'My option 3',
		'opt4' => 'My option 4',
		'special' => 'Special',
		'opt5' => 'My option 5',
	),
	'_required' => true,
));
$options->addField( '_required_custom', 'select', '', array(
	'label' => 'Validate required - custom message',
	'description' => "Validate <code>'_required' => 'You must select an option'</code>",
	'options' => array(
		'opt1' => 'My option 1',
		'opt2' => 'My option 2',
		'opt3' => 'My option 3',
		'opt4' => 'My option 4',
		'opt5' => 'My option 5',
	),
	'_required' => 'You must select an option',
	// 'parent' => array( '_required_true', 'special' ),
));
$options->addField( '_required_hacked', 'select', '', array(
	'label' => 'Validate hacked',
	'description' => "Edit the source code of an option and try to save by selecting that option. Must return required field error. Note: This field is not required by default, so an empty value can be saved.",
	'options' => array(
		'opt1' => 'My option 1',
		'opt2' => 'My option 2',
		'opt3' => 'My option 3',
		'opt4' => 'My option 4',
		'opt5' => 'My option 5',
	),
));