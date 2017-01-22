<?php
/**
 * Test field number
 *
 * This test will create an options page with all possible validation tests.
 *
 */
new Zwe\OptionsPage\Register('zwe-test-field-number', array(
	'parent' => 'zwe-test-field-variations',
	'title' => 'Number validation',
));

$options = new Zwe\OptionsPage\Manage('zwe-test-field-number');

//------------------------------------//--------------------------------------//

$options->addSection( 'validation', array(
	'label' => 'Number Validation',
	'description' => 'Test number field validation',
) );

$options->addField( '_required_true', 'number', '', array(
	'label' => 'Validate required',
	'description' => 'Validate <code>\'_required\' => true</code>',
	'_required' => true,
));

$options->addField( '_required_custom_message', 'number', '', array(
	'label' => 'Validate required with custom message',
	'description' => 'Validate <code>\'_required\' => \'Please tell me your age!\'</code>',
	'_required' => 'Please tell me your age!',
));

$options->addField( '_isnumber_test', 'number', '0053', array(
	'label' => 'Validate number',
	'description' => 'Validate if the value is a valid number',
));

$options->addField( '_isnumber_test_custom', 'number', '53qwerty', array(
	'label' => 'Validate number custom',
	'description' => '<code>\'_isnumber\' => \'Please enter a valid number!\'</code>',
));

$options->addField( '_min_true', 'number', '', array(
	'label' => 'Validate min',
	'description' => 'Validate <code>\'_min\' => 15</code>',
	'_min' => 15,
));

$options->addField( '_max_true', 'number', '', array(
	'label' => 'Validate max',
	'description' => 'Validate <code>\'_max\' => 39</code>',
	'_max' => 39,
));

$options->addField( '_step_true', 'number', '', array(
	'label' => 'Validate step',
	'description' => 'Validate <code>\'_step\' => 0.5</code>',
	'_step' => 0.5,
));

$options->addField( '_step_0_33', 'number', '', array(
	'label' => 'Validate step 0.33',
	'description' => 'Validate <code>\'_step\' => 0.33</code>',
	'_step' => 0.33,
));

$options->addField( '_step_0_255', 'number', '', array(
	'label' => 'Validate step 0.255',
	'description' => 'Validate <code>\'_step\' => 0.255</code>',
	'_step' => 0.255,
));


$options->addField( '_min_and_step', 'number', 2.3, array(
	'label' => 'Validate min and step',
	'description' => 'Validate <code>\'_min\' => 4</code> <code>\'_step\' => 0.2</code>',
	'_step' => 0.2,
	'_min' => 4,
));

$options->addField( '_max_and_step', 'number', 9.5, array(
	'label' => 'Validate max and step',
	'description' => 'Validate <code>\'_max\' => 8</code> <code>\'_step\' => 0.33</code>',
	'_step' => 0.33,
	'_max' => 8,
));

$options->addField( '_min_max_and_step', 'number', 12.1, array(
	'label' => 'Validate min, max and step',
	'description' => 'Validate <code>\'_min\' => 4</code> <code>\'_max\' => 8</code> <code>\'_step\' => 0.25</code>',
	'_step' => 0.25,
	'_min' => 4,
	'_max' => 8,
));

$options->addField( '_required_min_max_and_step', 'number', 12.1, array(
	'label' => 'Validate required, min, max and step',
	'description' => 'Validate <code>\'_required\' => true</code> <code>\'_min\' => 4</code> <code>\'_max\' => 8</code> <code>\'_step\' => 0.25</code>',
	'_required' => true,
	'_step' => 0.25,
	'_min' => 4,
	'_max' => 8,
));