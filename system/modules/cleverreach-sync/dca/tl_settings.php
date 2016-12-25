<?php


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][] = 'cr_active';
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{cleverreach_legend},cr_active';


/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['cr_active'] = 'cr_client_id,cr_login,cr_password';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['cr_active'] = [
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['cr_active'],
	'inputType' => 'checkbox',
	'eval'      => [
		'submitOnChange' => true,
		'tl_class' => 'w50 m12'
		]
	];

$GLOBALS['TL_DCA']['tl_settings']['fields']['cr_client_id'] = [
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['cr_client_id'],
	'inputType' => 'text',
	'eval'      => [
		'mandatory' => true,
		'tl_class' => 'w50'
		]
	];

$GLOBALS['TL_DCA']['tl_settings']['fields']['cr_login'] = [
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['cr_login'],
	'inputType' => 'text',
	'eval'      => [
		'mandatory' => true,
		'tl_class'  => 'w50'
		]
	];

$GLOBALS['TL_DCA']['tl_settings']['fields']['cr_password'] = [
	'label'     => &$GLOBALS['TL_LANG']['tl_settings']['cr_password'],
	'inputType' => 'text',
	'eval'      => [
		'mandatory' => true,
		'hideInput' => true,
		'encrypt'   => true,
		'tl_class'  => 'w50'
		]
	];
