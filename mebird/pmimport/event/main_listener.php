<?php
/**
 *
 * PM Import. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, mebird, https://github.com/mebird
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mebird\pmimport\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * PM Import Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		return array(
			'core.user_setup'							=> 'load_language_on_setup',
			'core.permissions'							=> 'add_permissions',
		);
	}

	/**
	 * Load common language files during user setup
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'mebird/pmimport',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/** @param \phpbb\event\data	$event	Event object */
	public function add_permissions($event)
	{
		$permissions = $event['permissions'];
		$permissions['u_pm_import_sent'] = array(
			'lang' => 'ACL_U_PM_IMPORT_SENT', 'cat' => 'pm'
		);
		$permissions['u_pm_import_received'] = array(
			'lang' => 'ACL_U_PM_IMPORT_RECEIVED', 'cat' => 'pm'
		);
		$event['permissions'] = $permissions;
	}
}
