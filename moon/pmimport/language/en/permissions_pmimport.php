<?php
/**
 *
 * Import PMs. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, moon, https://github.com/mebird
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

/**
*	EXTENSION-DEVELOPERS PLEASE NOTE
*
*	You are able to put your permission sets into your extension.
*	The permissions logic should be added via the 'core.permissions' event.
*	You can easily add new permission categories, types and permissions, by
*	simply merging them into the respective arrays.
*	The respective language strings should be added into a language file, that
*	start with 'permissions_', so they are automatically loaded within the ACP.
*/

$lang = array_merge($lang, array(
	'ACL_A_NEW_MOON_PMIMPORT'	=> 'Can use this Import PMs admin feature',
	'ACL_M_NEW_MOON_PMIMPORT'	=> 'Can use this Import PMs moderator feature',
	'ACL_U_NEW_MOON_PMIMPORT'	=> 'Can use this Import PMs user feature',
));
