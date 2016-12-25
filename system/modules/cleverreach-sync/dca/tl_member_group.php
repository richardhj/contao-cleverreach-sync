<?php


/**
 * Config
 */
//@todo $GLOBALS['TL_DCA']['tl_member_group']['config']['onload_callback'][] = array('CleverreachSync\Helper\Hooks', 'syncNewsletterChannelsWithGroups');
//@todo add onsubmit_callback
$GLOBALS['TL_DCA']['tl_member_group']['config']['ondelete_callback'][] = [
    'CleverreachSync\Helper\Dca',
    'deleteMemberGroup',
];


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_member_group']['palettes']['__selector__'][] = 'cr_sync';
$GLOBALS['TL_DCA']['tl_member_group']['palettes']['default'] .= ';{cleverreach_legend},cr_sync';


/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_member_group']['subpalettes']['cr_sync'] = 'cr_group_id';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_member_group']['fields']['cr_sync'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_member_group']['cr_sync'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => [
        'submitOnChange' => true,
        'tl_class'       => 'w50 m12',
    ],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_member_group']['fields']['cr_group_id'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_member_group']['cr_group_id'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => ['CleverreachSync\Helper\Dca', 'getCleverreachGroups'],
    'eval'             => [
        'unique'   => true,
        'tl_class' => 'w50',
    ],
    'sql'              => "int(10) NOT NULL default '0'",
];
