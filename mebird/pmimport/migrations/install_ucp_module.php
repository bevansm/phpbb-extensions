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

class install_ucp_module extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return array('\mebird\pmimport\migrations\install_schema');
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
				'ucp',
				0,
				'UCP_PM_IMPORT_TITLE'
			)),
			array('module.add', array(
				'ucp',
				'UCP_PM_IMPORT_TITLE',
				array(
					'module_basename'	=> '\mebird\pmimport\ucp\main_module',
					'modes'				=> array('settings'),
				),
			)),
		);
	}
}
