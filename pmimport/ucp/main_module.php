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
	protected $request;

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
		$this->import_service = $phpbb_container->get('mebird.pmimport.import_service');
		$this->request = $request;

		add_form_key('mebird_pmimport');
		$this->tpl_name = 'ucp_pm_import';
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

		$post_mode = '';
		if ($this->request->is_set_post('sent')) {
			$post_mode = 'sent';
		} elseif ($this->request->is_set_post('received')) {
			$post_mode = 'received';
		}
		print($this->request->is_set_post('sent'));
		print($this->request->is_set_post('received'));
		if ($post_mode) {
			if (!check_form_key('mebird_pmimport')) {
				trigger_error('FORM_INVALID', E_USER_WARNING);
			}

			$import = $this->request->variable('import', '', true);
			$users = $this->request->variable('users', '', true);
			$type = $this->request->variable('upload_type', 'csv', true);
			$err = $this->import_service->import($import, $type, $users, $post_mode);
			if ($err) {
				$this->set_error($err);
			}
			redirect($this->u_action . '&amp;mode=' . $post_mode);
		}
	}

	private function set_error(string $err) {
		$this->template->assign_vars([ 'ERROR' => $err ]);
	}

	private function mode_sent(): void
	{
		$this->page_title = $this->user->lang('UCP_PM_IMPORT_SENT');
		$this->template->assign_vars(array(
			'U_TITLE' 	=> 'UCP_PM_IMPORT_SENT',
			'U_EXPLAIN' => 'UCP_PM_IMPORT_USER_EXPLAIN_SENT',
			'U_CHANGE'	=> 'UCP_PM_IMPORT_USER_CHANGE_SENT',
			'U_ACTION' => 'sent'
		));
	}

	private function mode_received(): void
	{
		$this->page_title = $this->user->lang('UCP_PM_IMPORT_RECEIVED');
		$this->template->assign_vars(array(
			'U_TITLE' 	=> 'UCP_PM_IMPORT_RECEIVED',
			'U_EXPLAIN' => 'UCP_PM_IMPORT_USER_EXPLAIN_RECEIVED',
			'U_CHANGE'	=> 'UCP_PM_IMPORT_USER_CHANGE_RECEIVED',
			'U_ACTION' => 'received'
		));
	}
}
