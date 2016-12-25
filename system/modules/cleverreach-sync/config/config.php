<?php

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['activateRecipient'][] = array('CleverreachSync\Helper\Hooks', 'activateRecipient');
$GLOBALS['TL_HOOKS']['removeRecipient'][] = array('CleverreachSync\Helper\Hooks', 'removeRecipient');
