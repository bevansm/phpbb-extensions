<?php
/**
 *
 * isos. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, moon, https://github.com/mebird
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace moon\isos\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * isos Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		return array(
			'core.user_setup'								=> 'load_language_on_setup',
			'core.viewtopic_assign_template_vars_before'	=> 'count_user_posts',
		);
	}

	/* @var \phpbb\language\language */
	protected $language;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/** @var string phpEx */
	protected $php_ext;

	/* @var \phpbb\db\driver\driver_interface */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param \phpbb\language\language			$language	Language object
	 * @param \phpbb\controller\helper			$helper		Controller helper object
	 * @param \phpbb\template\template			$template	Template object
	 * @param string                    		$php_ext    phpEx
	 * @param \phpbb\db\driver\driver_interface $db db
	 */
	public function __construct(
		\phpbb\language\language $language, 
		\phpbb\controller\helper $helper, 
		\phpbb\template\template $template, 
		$php_ext,
		\phpbb\db\driver\driver_interface $db)
	{
		$this->language = $language;
		$this->helper   = $helper;
		$this->template = $template;
		$this->php_ext  = $php_ext;
		$this->db 		= $db;
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
			'ext_name' => 'moon/isos',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Add the user ISO data.
	 */
	public function count_user_posts($event)
	{
		$sql = 'SELECT u.username as USERNAME, COUNT(*) as POST_COUNT
			FROM ' . POSTS_TABLE . ' p
			LEFT OUTER JOIN ' . USERS_TABLE . ' u
			ON u.user_id = p.poster_id
			WHERE topic_id = ' . $event['topic_data']['topic_id'] . '
			GROUP BY u.user_id' ;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result)) {
			$this->template->assign_block_vars('U_ISOS_USERS', $row);
		}
		$this->db->sql_freeresult($result);
	}
}
