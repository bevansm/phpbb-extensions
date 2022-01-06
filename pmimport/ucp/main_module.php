<?php
/**
 *
 * PM Import. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, bevansm, https://github.com/bevansm
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace bevansm\pmimport\ucp;

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
		global $phpbb_container, $user, $template, $request;

		$this->user = $user;
		$this->template = $template;
		$this->import_service = $phpbb_container->get('bevansm.pmimport.import_service');

		$this->tpl_name = 'ucp_pm_import';
		add_form_key('bevansm_pmimport');

		switch ($mode) {
			case 'sent':
				$this->page_title = $this->user->lang('UCP_PM_IMPORT_SENT');
				$this->template->assign_vars(array(
					'U_TITLE' 	=> 'UCP_PM_IMPORT_SENT',
					'U_EXPLAIN' => 'UCP_PM_IMPORT_USER_EXPLAIN_SENT',
					'U_CHANGE'	=> 'UCP_PM_IMPORT_USER_CHANGE_SENT',
					'S_UCP_ACTION' => $this->u_action
				));
			break;
			case 'received':
				$this->page_title = $this->user->lang('UCP_PM_IMPORT_RECEIVED');
				$this->template->assign_vars(array(
					'U_TITLE' 	=> 'UCP_PM_IMPORT_RECEIVED',
					'U_EXPLAIN' => 'UCP_PM_IMPORT_USER_EXPLAIN_RECEIVED',
					'U_CHANGE'	=> 'UCP_PM_IMPORT_USER_CHANGE_RECEIVED',
					'S_UCP_ACTION' => $this->u_action
				));
			break;
			default:
				trigger_error('NO_ACTION_MODE', E_USER_ERROR);
			break;
		}

		if ($request->is_set_post('submit')) {
			if (!check_form_key('bevansm_pmimport')) {
				trigger_error('FORM_INVALID', E_USER_WARNING);
			}
			$import = htmlspecialchars_decode($request->variable('import_text', '', true));
			$users = htmlspecialchars_decode($request->variable('users_text', '', true));
			$type = $request->variable('upload_type', 'csv', true);
			$err = $this->import_service->import($import, $type, $users, $mode);
			if ($err) {
				$this->template->assign_vars(array(
					'U_IMPORT' 	=> $import,
					'U_USERS' => $users
				));
				$this->set_error($err);
				return;
			}
		}
	}

	private function set_error(string $err) {
		$this->template->assign_var('ERROR', $err);
	}
}
