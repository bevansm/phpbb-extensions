<?php
/**
 *
 * PM Import. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, mebird, https://github.com/mebird
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mebird\vldr\migrations;

class install_ucp_module extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return array('\mebird\vldr\migrations\install_schema');
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
				'ucp',
				0,
				'UCP_VLDR'
			)),

			array('module.add', array(
				'ucp',
				'UCP_VLDR',
				array(
					'module_basename'   => '\mebird\vldr\ucp\main_module',
					'title'	=> '',
					'auth'	=> 'ext_mebird/vldr && acl_u_vldr',
					'modes' => array('create', 'manage'),
				),
			)),
		);
	}
}
