<?php
$fields[] = array(
	'field_name'		=> 'respite_providerID',
	'input_type'		=> 'single_line',
	'label'					=> 'Respite Provider',
	'default_value'	=> '',
	'query'					=> array(
										'table'=>'procedure_providers',
										'field'=>'name'
									),
	'group'					=> 1
);

$fields[] = array(
	'field_name'		=> 'billing_code',
	'input_type'		=> 'single_line',
	'label'					=> 'Billing Code',
	'default_value'	=> '',
	'query'					=> '',
	'group'					=> 1
);

$fields[] = array(
	'field_name'		=> 'dateofservice',
	'input_type'		=> 'single_line',
	'label'					=> 'Date of Service',
	'default_value'	=> '',
	'query'					=> '',
	'group'					=> 1
);

$fields[] = array(
	'field_name'		=> 'starttime',
	'input_type'		=> 'single_line',
	'label'					=> 'Start Time',
	'default_value'	=> '',
	'query'					=> '',
	'group'					=> 2
);

$fields[] = array(
	'field_name'		=> 'endtime',
	'input_type'		=> 'single_line',
	'label'					=> 'End Time',
	'default_value'	=> '',
	'query'					=> '',
	'group'					=> 2
);

$fields[] = array(
	'field_name'		=> 'duration',
	'input_type'		=> 'single_line',
	'label'					=> 'Duration',
	'default_value'	=> '',
	'query'					=> '',
	'group'					=> 2
);


