<?php
/**
 *
 * isos. An extension for the phpBB Forum Software package.
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

$lang = array_merge($lang, array(
	'ISOS_ISO'		=> 'ISO',
	'ISOS_ISOS'		=> 'ISOS',
	'ISOS_COUNT' 	=> 'Posts'
));
