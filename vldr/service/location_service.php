<?php
/**
 *
 * vldr. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, mebird, https://github.com/mebird
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mebird\vldr\service;
use phpbb\db\driver\factory as db;

class location_service
{
	protected $user;
	protected $db;
	protected $loc_table;
	protected $char_table;

	public function __construct(user $user, db $db, string $loc_table, string $char_table)
	{
		$this->user = $user;
		$this->db = $db;
		$this->loc_table = $loc_table;
		$this->char_table = $char_table;
	}

	public function create_room(int $game_id, string $room_name)
	{
		// TODO: implement this
	}

	public function delete_room(int $game_id, array $room_id)
	{
		// TODO: implement this
	}

	public function move_characters(int $game_id, $movements)
	{
		$by_location = array();
		foreach ($movements as $mv) {
			$loc_id = $mv['src'];
			if ($mv['src'] != $mv['dest']) {
				$loc_id = $this->get_enroute($mv['src'], $mv['dest']);
				if ($loc_id < 0) {
					$loc_id = $this->create_enroute($game_id, $mv['src'], $mv['dest']);
				}
			}
			array_push($by_location[$loc_id][$mv['type']], $mv['character_id']);
		}

		foreach ($by_location as $loc_id => $types) {
			// TODO: check syntax
			$left = $types['left'] || [];
			$entered = $types['enter'] || [];
			$this->leave_room($left);
			$this->enter_room($entered);
			$this->send_movement_pm($loc_id, $entered, $left);
		}
	}

	private function leave_room($loc_id, $character_ids)
	{
		$sql = 'UPDATE ' . $this->char_table . ' SET loc_id = NULL 
				WHERE loc_id = ' . $loc_id ' 
					AND ' . $db->sql_in_set('character_id', $character_ids);
		$this->db->sql_query($sql);
	}

	private function enter_room($loc_id, $character_ids)
	{
		$sql = 'UPDATE ' . $this->char_table . ' SET loc_id = ' . $loc_id ' 
				WHERE ' . $db->sql_in_set('character_id', $character_ids);
		$this->db->sql_query($sql);
	}

	private function get_character_names($character_ids) -> array
	{
		$sql = 'SELECT character_name FROM ' . $this->char_table . ' 
				WHERE ' . $this->db->sql_in_set('character_id', $character_ids);
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$db->sql_freeresult($result);
		return array_column($rows, 'character_name');
	}

	private function get_users_in_room($loc_id) -> array 
	{
		$sql = 'SELECT user_id FROM ' . $this->char_table . '
				WHERE loc_id = ' . $loc_id;
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		// The host & users are all in the room
		$recipients = array_column($rows, 'user_id');
		array_push($recipients, $this->user->data['user_id']);
		return $recipients;
	}

	private function send_movement_pm($loc_id, $entered, $left)
	{
		$entered = $this->get_character_names($entered);
		$left = $this->get_character_names($left);
		$recipients = $this->get_users_in_room($loc_id);

		$sql = 'SELECT loc_name, root_level FROM ' . $this->loc_table . ' WHERE loc_id = ' . $loc_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		$root_level = $row['root_level'];
		$subject = $row['loc_name'];

		global $phpbb_root_path, $phpEx;

		if (!function_exists('submit_pm'))
		{
			include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
		}

		// TODO: better strings for the message & translation
		$pm = array(
			'message' => join(', ', $entered) . ' enter. ' . join(',', $left) . ' leave.',
			'bbcode_bitfield' => '',
			'bbcode_uid' => '',
			'enable_bbcode' => 1,
			'icon_id' => 7,
			'address_list' => array('u' => array_map(function() { return 'to'; }, array_flip($recipients))),
			'from_user_ip' => $this->user->ip,
			'from_user_id' => $this->user->data['user_id'],
			'enable_sig' => 1,
			'enable_urls' => 1,
			'enable_bbcode' => 1,
			'enable_smilies' => 1,
			'reply_from_msg_id' => $root_level
		);
		submit_pm('reply', $subject, $pm);
	}

	private function get_enroute($src, $dest) {
		$sql = 'SELECT loc_id FROM ' . $this->loc_table . ' WHERE src_id = ' . $src . ' AND dest_id = ' . $dest;
		$row = $this->db->sql_query($sql);
		return $row ? $row['loc_id'] : -1;
	}

	private function create_enroute(int $game_id, int $src, int $dest) {
		// TODO: use usr translation strings in the "ENROUTE" area
		$sql = 'INSERT INTO ' . $this->loc_table . ' (loc_name, game_id, src_id, dest_id) 
				VALUES ( "Enroute ("
						+ (SELECT loc_name FROM ' . $this->loc_table . ' WHERE loc_id = ' . $src . ')
						+ " => "
						+ (SELECT loc_name FROM ' . $this->loc_table . ' WHERE loc_id = ' . $dest . ')
						+ ")", ' . $game_id . ', ' . $src . ', ' . $dest . ')';
		$this->db->sql_query($sql);
		$loc_id = $db->sql_nextid();
		$this->create_root_pm($loc_id);
		return $loc_id;
	}

	private function create_root_pm(int $loc_id): int {
		global $phpbb_root_path, $phpEx;

		if (!function_exists('submit_pm'))
		{
			include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
		}

		$sql = 'SELECT loc_name FROM ' . $this->loc_table . ' WHERE l.loc_id = ' . $loc_id;
		$result = $this->db->sql_query($sql);
		$subject = $this->db->sql_fetchrow($result)['loc_name'];
		$this->db->sql_freeresult($result);

		$pm = array(
			'message' => 'Zero stumbles into the room...',
			'bbcode_bitfield' => '',
			'bbcode_uid' => '',
			'enable_bbcode' => 1,
			'icon_id' => 7,
			'address_list' => array('u' => array([$this->user->data['user_id']] => 'to')),
			'from_user_ip' => $this->user->ip,
			'from_user_id' => $this->user->data['user_id'],
			'enable_sig' => 1,
			'enable_urls' => 1,
			'enable_bbcode' => 1,
			'enable_smilies' => 1
		);
		$msg_id = submit_pm('post', $subject, $pm, false);

		$sql = 'UPDATE ' . $this->loc_table . ' SET root_level = ' . $msg_id . ' WHERE loc_id = ' . $loc_id;
		$this->db->sql_query($sql);
		return $msg_id;
	}
}
