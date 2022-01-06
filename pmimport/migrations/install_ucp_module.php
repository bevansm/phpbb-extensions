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

class install_ucp_module extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return array('\bevansm\pmimport\migrations\install_schema');
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
				'ucp',
				0,
				'UCP_PM_IMPORT'
			)),

			array('module.add', array(
				'ucp',
				'UCP_PM_IMPORT',
				array(
					'module_basename'   => '\bevansm\pmimport\ucp\main_module',
					'title'	=> 'UCP_PM_IMPORT_SENT',
					'auth'	=> 'ext_bevansm/pmimport && acl_u_pm_import_sent',
					'modes' => array('sent'),
				),
			)),

			array('module.add', array(
				'ucp',
				'UCP_PM_IMPORT',
				array(
					'module_basename'   => '\bevansm\pmimport\ucp\main_module',
					'title'	=> 'UCP_PM_IMPORT_RECEIVED',
					'auth'	=> 'ext_bevansm/pmimport && acl_u_pm_import_received',
					'modes' => array('received'),
				),
			)),
		);
	}
}
