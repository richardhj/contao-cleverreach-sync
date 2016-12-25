<?php


/**
 * Config
 */
$GLOBALS['TL_DCA']['tl_member']['config']['ondelete_callback'][] = ['CleverreachSync\Helper\Hooks', 'deleteMember'];


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_member']['fields']['groups']['save_callback'][] = ['CleverreachSync\Helper\Hooks', 'syncMemberGroupsWithCleverreach'];
$GLOBALS['TL_DCA']['tl_member']['fields']['cr_receiver_id'] = [
	'sql' => "int(10) NOT NULL default '0'"
	];
