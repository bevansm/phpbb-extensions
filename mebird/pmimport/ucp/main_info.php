<?php
/**
 *
 * PM Import. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, mebird, https://github.com/mebird
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mebird\pmimport\ucp;

/**
 * PM Import UCP module info.
 */
class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\mebird\pmimport\ucp\main_module',
			'title'		=> 'UCP_PM_IMPORT_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'UCP_PM_IMPORT',
					'auth'	=> 'ext_mebird/pmimport && (acl_u_pm_import_sent || acl_u_pm_import_received)',
					'cat'	=> array('UCP_PM_IMPORT_TITLE')
				),
			),
		);
	}
}
