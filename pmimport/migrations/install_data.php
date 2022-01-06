<?php
/**
 *
 * PM Import. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, bevansm, https://github.com/bevansm
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace bevansm\pmimport\migrations;

class install_data extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return array(
			array('permission.add', array('u_pm_import_sent')), 
			array('permission.add', array('u_pm_import_received')), 
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'u_pm_import_sent')),
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'u_pm_import_received'))
		);
	}
}
