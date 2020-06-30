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
 * PM Import UCP module.
 */
class main_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	protected $user;
	protected $template;

	protected $import_service;

	/**
	 * Main UCP module
	 *
	 * @param int    $id   The module ID
	 * @param string $mode The module mode ("sent" or "received")
	 * @throws \Exception
	 */
	public function main($id, $mode)
	{
		global $phpbb_container, $user, $template;

		$this->user = $user;
		$this->template = $template;
		$this->import_service = $phpbb_container->get('mebird.pmimport.import_service');

		switch ($mode) {
			case 'sent':
				$this->mode_sent();
			break;
			case 'received':
				$this->mode_received();
			break;
			default:
				trigger_error('NO_ACTION_MODE', E_USER_ERROR);
			break;
		}
	}

	private function mode_sent(): void
	{
		$this->page_title = $this->user->lang('UCP_PM_IMPORT_SENT');
		$this->tpl_name = 'ucp_pm_import';
		$this->template->assign_vars(array(
			'U_TITLE' 	=> 'UCP_PM_IMPORT_SENT',
			'U_EXPLAIN' => 'UCP_PM_IMPORT_USER_EXPLAIN_SENT',
			'U_CHANGE'	=> 'UCP_PM_IMPORT_USER_CHANGE_SENT'
		));
	}

	private function mode_received(): void
	{
		$this->page_title = $this->user->lang('UCP_PM_IMPORT_RECEIVED');
		$this->tpl_name = 'ucp_pm_import';
		$this->template->assign_vars(array(
			'U_TITLE' 	=> 'UCP_PM_IMPORT_RECEIVED',
			'U_EXPLAIN' => 'UCP_PM_IMPORT_USER_EXPLAIN_RECEIVED',
			'U_CHANGE'	=> 'UCP_PM_IMPORT_USER_CHANGE_RECEIVED'
		));
	}
}
