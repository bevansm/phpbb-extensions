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

class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\mebird\pmimport\ucp\main_module',
			'title'		=> 'UCP_PM_IMPORT',
			'modes'		=> array(
				'received'	=> array(
					'title'	=> 'UCP_PM_IMPORT_RECEIVED',
					'auth'	=> 'ext_mebird/pmimport && acl_u_pm_import_received',
					'cat'	=> array('UCP_PM')
				),
				'sent'	=> array(
					'title'	=> 'UCP_PM_IMPORT_SENT',
					'auth'	=> 'ext_mebird/pmimport && acl_u_pm_import_sent',
					'cat'	=> array('UCP_PM')
				),
			),
		);
	}
}