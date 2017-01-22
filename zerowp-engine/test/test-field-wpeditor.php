<?php
/**
 * Test field wpeditor
 *
 * This test will create an options page with all possible validation tests.
 *
 */
new Zwe\OptionsPage\Register('zwe-test-field-wpeditor', array(
	'parent' => 'zwe-test-field-variations',
	'title' => 'Wpeditor validation',
));

$options = new Zwe\OptionsPage\Manage('zwe-test-field-wpeditor');

//------------------------------------//--------------------------------------//

$options->addSection( 'validation', array(
	'label' => 'Validation wpeditor field',
	'description' => 'Test wpeditor field validation',
) );

$options->addField( '_required_true', 'wpeditor', '', array(
	'label' => 'Validate required',
	'description' => 'Validate <code>\'_required\' => true</code>',
	'_required' => true,
));

$options->addField( '_required_custom_message', 'wpeditor', '', array(
	'label' => 'Validate required with custom message',
	'description' => 'Validate <code>\'_required\' => \'Please enter some text!\'</code>',
	'_required' => 'Please enter some text!',
));

$options->addField( '_maxlength_true', 'wpeditor', '', array(
	'label' => 'Validate maxlength',
	'description' => 'Validate <code>\'_maxlength\' => 15</code>',
	'_maxlength' => 15,
));

$options->addField( '_maxlength_custom_message', 'wpeditor', '', array(
	'label' => 'Validate maxlength with custom message',
	'description' => 'Validate <code>\'_maxlength\' => array( 15, \'Stop. Max {{_maxlength}} chars allowed!\' )</code>',
	'_maxlength' => array( 15, 'Stop. Max {{_maxlength}} chars allowed!' ),
));

$options->addField( '_minlength_true', 'wpeditor', '', array(
	'label' => 'Validate minlength',
	'description' => 'Validate <code>\'_minlength\' => 5</code>',
	'_minlength' => 5,
	'_maxlength' => 15,
));

$options->addField( '_minlength_custom_message', 'wpeditor', '', array(
	'label' => 'Validate minlength with custom message',
	'description' => 'Validate <code>\'_minlength\' => array( 5, \'Don\'t be shy! Min chars needed is {{_minlength}}\' )</code>',
	'_minlength' => array( 5, 'Don\'t be shy! Min chars needed is {{_minlength}}' ),
));