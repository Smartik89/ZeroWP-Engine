<?php
/*
 * Plugin Name: Zwe
 * Plugin Description: Unified API for WordPress development.
 * Author: Andrei Surdu
 */

include __DIR__ . "/engine/start.php";
include __DIR__ . "/test/test-field-variations.php";
include __DIR__ . "/test/test-field-text.php";
include __DIR__ . "/test/test-field-number.php";
include __DIR__ . "/test/test-field-textarea.php";
include __DIR__ . "/test/test-field-wpeditor.php";
include __DIR__ . "/test/test-field-select.php";
include __DIR__ . "/test/test-fields-being-parents.php";



include __DIR__ . "/test/test-metabox.php";


// echo '<pre>';
// print_r( zwe_module_childs( 'OptionsPage' ) );
// print_r( zwe_module_childs( 'Field' ) );
// print_r( apply_filters( 'zwe_OptionsPage_zwe-test-field-number_manage_fields', array() ) );
// echo '</pre>';