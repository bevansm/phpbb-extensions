<?php
/**
 *
 * vldr. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, bevansm, https://github.com/bevansm
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace bevansm\vldr\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * PM Import Event listener.
 */
class main_listener implements EventSubscriberInterface
{

	/* @var \phpbb\db\driver\driver_interface */
	protected $db;
	protected $spec_table;
	protected $loc_table;

	/**
	 * Constructor
	 * @param \phpbb\db\driver\driver_interface $db db
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, $loc_table, $spec_table)
	{
		$this->spec_table = $spec_table;
		$this->loc_table = $loc_table;
		$this->db = $db;
	}

	public static function getSubscribedEvents()
	{
		return array(
			'core.user_setup'		=> 'load_language_on_setup',
			'core.submit_pm_before'	=> 'submit_pm_before',
		);
	}

	/**
	 * Load common language files during user setup
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'bevansm/vldr',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	* Get all parts of the PM that are to be submited to the DB.
	*
	* @event core.submit_pm_before
	* @var	string	mode	PM Post mode - post|reply|quote|quotepost|forward|edit
	* @var	string	subject	Subject of the private message
	* @var	array	data	The whole row data of the PM.
	* @since 3.1.0-b3
	*/
	public function submit_pm_before($mode, $subject, $data_ary)
	{
		if ($data_ary['reply_from_root_level'])
		{
			$sql = 'SELECT DISTINCT s.user_id from ' . $this->loc_table . ' l 
					JOIN ' . $this->spec_table . ' s
						ON l.game_id = s.game_id
					WHERE l.root_level = ' . $data_ary['reply_from_root_level'];
			$result = $this->db->sql_query($sql);
			$rows = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);
			$to = array_keys($data_ary['address_list']['u']);
			foreach ($ids as $id)
			{
				if (!in_array($id, $to)) {
					$data_ary['address_list']['u'][$id] = 'bcc';
				}
			}
		}
		return $data_ary;
	}

}
