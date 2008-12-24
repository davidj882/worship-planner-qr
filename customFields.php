<?php
  // This file defines custom text fields that you want in your database


$customFieldLength = '100';  // max number of characters for custom text fields

$customFields = array('Lesson1'=> 'Lesson 1',
		      'Psalm'  => 'Psalm',
		      'MessageText' => 'Message Text');


// custom fields should be in the form '<fieldName>' => '<display label>'
// fieldName must be a valid mySQL field name.  The Display label
// is just the way the field is displayed in the planning view, etc.

?>