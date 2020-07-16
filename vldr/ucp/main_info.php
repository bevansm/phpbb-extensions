<?php
/**
 *
 * PM Import. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, mebird, https://github.com/mebird
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mebird\vldr\ucp;

class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\mebird\vldr\ucp\main_module',
			'title'		=> '',
			'modes'		=> array(
				'received'	=> array(
					'title'	=> '',
					'auth'	=> 'ext_mebird/vldr && acl_u_vldr',
					'cat'	=> array('UCP_VLDR')
				),
				'sent'	=> array(
					'title'	=> '',
					'auth'	=> 'ext_mebird/vldr && acl_u_vldr',
					'cat'	=> array('UCP_VLDR')
				),
			),
		);
	}
}