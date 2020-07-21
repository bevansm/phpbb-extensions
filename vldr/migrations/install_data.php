<?php
/**
 *
 * PM Import. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, mebird, https://github.com/mebird
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mebird\pmimport\migrations;

class install_data extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return array(
			array('permission.add', array('u_vldr')), 
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'u_vldr')),
		);
	}
}
