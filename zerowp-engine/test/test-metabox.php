<?php

// M1

new Zwe\Metabox\Register('zwe-test-metabox', 'post', array(
	'title'       => 'Zwe Text Metabox',
	'msg:success' => __('It\'s OK, post has been saved!!!', 'zerowp'),
));


// M1 manager

$options = new Zwe\Metabox\Manage('post', 'zwe-test-metabox');

$options->addField( 'select_default', 'select', 'opt2', array(
	'label' => 'Select - default',
	'options' => array(
		'opt1' => 'My option 1',
		'opt2' => 'My option 2',
		'opt3' => 'My option 3',
		'opt4' => 'My option 4',
		'opt5' => 'My option 5',
	),
));

$options->addField( 'textarea_5_rows', 'textarea', '', array(
	'label' => 'Textarea 5 rows',
	'rows' => 5,
	'description' => "<code>'rows' => 5</code>",
	'_required' => 'Yeah!!!!!',
));

$options->addSection('yeah', array(
	'label' => 'Yeah',
	'description' => 'Test description here, ohh yeah',
));
$options->addField( 'upload_new_2', 'upload', '', array(
	'label' => 'Upload media',
	'ext' => 'zip',
));


// M2

new Zwe\Metabox\Register('zwe-test-metabox2', 'post', array(
	'title'       => 'Zwe Text Metabox 2',
	'msg:success' => __('It\'s OK, post has been saved!!!222222', 'zerowp'),
	'context' => 'side',
));


// M2 manager
$options = new Zwe\Metabox\Manage('post', 'zwe-test-metabox2');

$options->addField( 'select_default', 'select', 'opt3', array(
	'label' => 'Select - default',
	'options' => array(
		'opt1' => 'My option 1',
		'opt2' => 'My option 2',
		'opt3' => 'My option 3',
		'opt4' => 'My option 4',
		'opt5' => 'My option 5',
	),
	'_required' => true,
));

$options->addField( 'textarea_55_rows', 'textarea', '', array(
	'label' => 'Textarea 5 rows',
	'rows' => 5,
	'description' => "<code>'rows' => 5</code>",
	'_required' => 'It\'s required!!!!!',
));

$options->addField( 'upload_new_2', 'upload', '', array(
	'label' => 'Upload media',
	// 'ext' => 'zip',
));


// M1 new manager instance

$options = new Zwe\Metabox\Manage('post', 'zwe-test-metabox');

$options->addField( 'select_default_alt', 'select', 'opt5', array(
	'label' => 'Select - default',
	'options' => array(
		'opt1' => 'My option 1',
		'opt2' => 'My option 2',
		'opt3' => 'My option 3',
		'opt4' => 'My option 4',
		'opt5' => 'My option 5',
	),
));