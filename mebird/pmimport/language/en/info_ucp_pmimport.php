<?php
/**
 *
 * PM Import. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, mebird, https://github.com/mebird
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'UCP_PM_IMPORT_SENT'					=> 'Import sent PMS',
	'UCP_PM_IMPORT_RECEIVED'				=> 'Import received PMS',
	'UCP_PM_IMPORT_TITLE'					=> 'Import private messages',
	'UCP_PM_IMPORT_INFO'					=> 'Raw CSV/XML',
	'UCP_PM_IMPORT_USER_EXPLAIN'			=> 'Paste the raw CSV or XML from your PM exports. Do not alter any columns or data, aside from users, if appropriate.',
	'UCP_PM_IMPORT_USER_EXPLAIN_SENT'		=> 'You can only import PMS where you were the sender. All recipients must be users that exist on this forum.',
	'UCP_PM_IMPORT_USER_EXPLAIN_RECEIVED'	=> 'You can only include PMS from users who have posted on this forum.',
	'UCP_PM_IMPORT_SAVED'					=> 'Private messages successfully imported!',
));
