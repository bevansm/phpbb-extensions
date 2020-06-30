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
	'UCP_PM_IMPORT'							=> 'Import private messages',
	'UCP_PM_IMPORT_SENT'					=> 'Import sent PMS',
	'UCP_PM_IMPORT_RECEIVED'				=> 'Import received PMS',
	'UCP_PM_IMPORT_INFO'					=> 'Raw CSV/XML',
	'UCP_PM_IMPORT_USER_EXPLAIN_0'			=> 'Paste the raw text of the import.',
	'UCP_PM_IMPORT_USER_EXPLAIN_1' 			=> 'You can obtain the raw text of a CSV or XML document by opening the file in Notepad or Notes. Do not alter any data.',
	'UCP_PM_IMPORT_USER_EXPLAIN_SENT'		=> 'You can only import PMS where you were the sender.',
	'UCP_PM_IMPORT_USER_EXPLAIN_RECEIVED'	=> 'You can only include PMS from users who have posted on this forum.',
	'UCP_PM_IMPORT_USER_CHANGE_SENT'		=> 'Map recipient(s)',
	'UCP_PM_IMPORT_USER_CHANGE_RECEIVED'	=> 'Map sender(s)',
	'UCP_PM_IMPORT_USER_CHANGE_EXPLAIN_0'	=> 'Map the first username to the second during import. You can use this to easily "replace" any users whose usernames may have changed.',
	'UCP_PM_IMPORT_USER_CHANGE_EXPLAIN_1'	=> 'Add each set of usernames on a new line as a comma seperated pair, i.e. OLD_USERNAME, NEW USERNAME.',
	'UCP_PM_IMPORT_ADD_USER'				=> 'Add user',
	'UCP_PM_IMPORT_TYPE'					=> 'Import type',				
	'UCP_PM_IMPORT_SAVED'					=> 'Private messages successfully imported!',
));