<?php
/**
 *
 * Import PMs. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, moon, https://github.com/mebird
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace moon\pmimport\migrations;

class install_data extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return array('\phpbb\db\migration\data\v320\v320');
	}

	/**
	 * Add, update or delete data stored in the database during extension installation.
	 *	permission.add: Add a new permission.
	 *  permission.permission_set: Set a permission to Yes or Never.
	 *  permission.permission_unset: Set a permission to No.
	 * @return array Array of data update instructions
	 */
	public function update_data()
	{
		return array(
			array('permission.add', array('u_pm_import')), 
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'u_pm_import'))
		);
	}
}
