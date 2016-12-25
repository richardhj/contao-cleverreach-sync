<?php



/**
 * Config
 */
$GLOBALS['TL_DCA']['tl_newsletter_channel']['config']['onload_callback'][] = ['CleverreachSync\Helper\Dca', 'syncNewsletterChannelsWithGroups'];
//@todo add ondelete_callback
//@todo add onsubmit_callback


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_newsletter_channel']['palettes']['default'] .= ';{cleverreach_legend},cr_group_id';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_newsletter_channel']['fields']['cr_group_id'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_newsletter_channel']['cr_group_id'],
    'exclude' => true,
    'inputType' => 'select',
    'options_callback' => ['CleverreachSync\Helper\Hooks', 'getCleverreachGroups'],
    'eval' => [
	    'unique'=>true
        ],
	'sql' => "int(10) NOT NULL default '0'"
    ];
