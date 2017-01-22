<?php
/**
 * Test fields being parents
 *
 */
new Zwe\OptionsPage\Register('zwe-test-fields-being-parents', array(
	'parent' => 'zwe-test-field-variations',
	'title' => 'Test fields being parents',
));

$options = new Zwe\OptionsPage\Manage('zwe-test-fields-being-parents');

//------------------------------------//--------------------------------------//

$options->addSection( 'test_text_parent', array(
	'label' => 'Test text field being parent',
) );

$options->addField( 'text_field_parent', 'text', '', array(
	'label' => 'Text field being parent',
	'description' => 'This field is a parent. Try to write <code>Awesome</code>, <code>Help</code> or <code>Give it to me!</code> to see the fields for each value.',
	'_required' => true,
));

$options->addField( 'text_field_child_1', 'select', '', array(
	'label' => 'Select opt 1',
	'description' => "This is a child of <code>text_field_parent</code>. And requires value <code>Help</code>, to make it visible. <code>'parent' => array( 'text_field_parent', 'Help' )</code>",
	'options' => array(
		'opt1' => 'My option 1',
		'opt2' => 'My option 2',
	),
	'parent' => array( 'text_field_parent', 'Help' ),
));

$options->addField( 'text_field_child_2', 'select', '', array(
	'label' => 'Select opt 2',
	'description' => "This is a child of <code>text_field_parent</code>. And requires value <code>Awesome</code>, to make it visible. <code>'parent' => array( 'text_field_parent', 'Awesome' )</code>",
	'options' => array(
		'opt1' => 'My option 1',
		'opt2' => 'My option 2',
	),
	'parent' => array( 'text_field_parent', 'Awesome' ),
));

$options->addField( 'text_field_child_3', 'text', '', array(
	'label' => 'Text opt 3',
	'description' => "This is a child of <code>text_field_parent</code>. And requires value <code>Give it to me!</code>, to make it visible. <code>'parent' => array( 'text_field_parent', 'Give it to me!' )</code>",
	'parent' => array( 'text_field_parent', 'Give it to me!' ),
));
//------------------------------------//--------------------------------------//

$options->addSection( 'test_select_parent', array(
	'label' => 'Test select field being parent',
) );

$options->addField( 'select_field_parent', 'select', '', array(
	'label' => 'Select field being parent',
	'description' => 'This field is a parent. Try to select an option, to see the fields for each value.',
	'options' => array(
		'good' => 'This is good',
		'nice' => 'This is nice',
	),
	'_required' => true,
));


$options->addField( 'select_field_child_1', 'select', '', array(
	'label' => 'Select opt 1',
	'description' => "This is a child of <code>select_field_parent</code>. And requires value <code>nice</code>, to make it visible. <code>'parent' => array( 'select_field_parent', 'nice' )</code>",
	'options' => array(
		'opt1' => 'My option 1',
		'opt2' => 'My option 2',
	),
	'parent' => array( 'select_field_parent', 'nice' ),
));

$options->addField( 'select_field_child_2', 'select', '', array(
	'label' => 'Select opt 2',
	'description' => "This is a child of <code>select_field_parent</code>. And requires value <code>good</code>, to make it visible. <code>'parent' => array( 'select_field_parent', 'good' )</code>",
	'options' => array(
		'opt1' => 'My option 1',
		'opt2' => 'My option 2',
	),
	'parent' => array( 'select_field_parent', 'good' ),
));

$options->addField( 'select_field_child_3', 'text', '', array(
	'label' => 'Text opt 3',
	'description' => "This is a child of <code>select_field_parent</code>. And requires value <code>nice</code>, to make it visible. <code>'parent' => array( 'select_field_parent', 'nice' )</code>",
	'parent' => array( 'select_field_parent', 'nice' ),
));