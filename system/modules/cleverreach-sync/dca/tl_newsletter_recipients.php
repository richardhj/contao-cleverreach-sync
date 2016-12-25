<?php



/**
 * Config
 */
$GLOBALS['TL_DCA']['tl_newsletter_recipients']['config']['onload_callback'][] = ['CleverreachSync\Helper\Hooks', 'syncNewsletterRecipientsWithReceivers'];
//@todo add ondelete_callback
//@todo add onsubmit_callback
