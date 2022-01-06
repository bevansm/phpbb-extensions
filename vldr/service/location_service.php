<?php
/**
 *
 * vldr. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, bevansm, https://github.com/bevansm
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace bevansm\vldr\service;
use phpbb\db\driver\factory as db;

class location_service
{
	protected $user;
	protected $db;
	protected $parser;
	protected $loc_table;
	protected $char_table;
	protected $game_table;

	public function __construct(user $user, db $db, \phpbb\textformatter\s9e\parser $parser, string $game_table, string $loc_table, string $char_table)
	{
		$this->user = $user;
		$this->db = $db;
		$this->loc_table = $loc_table;
		$this->char_table = $char_table;
		$this->game_table = $game_table;

		$parser->enable_bbcodes();
		$parser->enable_magic_url();
		$parser->enable_smilies();
		$this->parser = $parser;
	}

	public function create_room(int $game_id, string $room_name, string $loc_text)
	{
		$sql = 'INSERT INTO ' . $this->loc_table . ' ' .
		$this->db->sql_build_array('INSERT', array(
					'game_id' 		=> $game_id,
					'loc_name' 		=> $room_name,
					'loc_text' 		=> $this->parser->parse($loc_text), 
					'loc_subject' 	=> $this->create_room_subject($game_id, $room_name)
				));
		$result = $this->db->sql_query($sql);
		$loc_id = $this->db->sql_nextid();

		$sql = 'UPDATE ' . $this->loc_table . '
				SET dest_id = ' . $loc_id . '
					src_id  = ' . $loc_id . '
				WHERE loc_id = ' . $loc_id;
				$this->db->sql_query($sql);

		$this->create_root_pm($loc_id);
	}

	public function delete_room(int $loc_id)
	{
		$sql = 'UPDATE ' . $this->char_table . ' c,' . $this->loc_table . ' l 
				SET u.loc_id = NULL 
				WHERE c.loc_id = ' . $loc_id . '
					OR (c.loc_id = l.loc_id 
						AND (l.src_id = ' . $loc_id .' OR l.dest_id = '. $loc_id .'))';
						$this->db->sql_query($sql);
		$sql = 'DELETE FROM ' . $this->loc_table . ' 
				WHERE loc_id = ' . $loc_id . '
					OR src_id = ' . $loc_id . '
					OR dest_id = ' . $loc_id;
					$this->db->sql_query($sql);
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
			$left = $types['left'] || [];
			$entered = $types['enter'] || [];
			$this->leave_room($left);
			$this->enter_room($entered);
			$this->send_movement_pm($loc_id, $entered, $left);
		}
	}

	private function get_game_code($game_id)
	{
		$sql = 'SELECT game_code FROM ' . $this->game_table . ' WHERE game_id  = ' . $game_id;
		$result = $this->db->sql_query($sql);
		$game = $this->db->fetch_row($sql);
		$this->db->sql_freeresult($result);
		return $game['game_code'];
	}

	private function create_room_subject($game_id, $loc_name)
	{
		return 'VLDR: ' . $this->get_game_code($game_id) . ' - ' . $loc_name;
	}

	private function leave_room($loc_id, $character_ids)
	{
		$sql = 'UPDATE ' . $this->char_table . ' SET loc_id = NULL 
				WHERE loc_id = ' . $loc_id . 'AND' . $this->db->sql_in_set('character_id', $character_ids);
		$this->db->sql_query($sql);
	}

	private function enter_room($loc_id, $character_ids)
	{
		$sql = 'UPDATE ' . $this->char_table . ' SET loc_id = ' . $loc_id . ' 
				WHERE ' . $this->db->sql_in_set('character_id', $character_ids);
		$this->db->sql_query($sql);
	}

	private function get_character_names($character_ids)
	{
		$sql = 'SELECT character_name FROM ' . $this->char_table . ' 
				WHERE ' . $this->db->sql_in_set('character_id', $character_ids);
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);
		return array_column($rows, 'character_name');
	}

	private function get_users_in_room($loc_id)
	{
		$sql = 'SELECT user_id FROM ' . $this->char_table . '
				WHERE loc_id = ' . $loc_id;
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);
		return array_column($rows, 'user_id');
	}

	private function send_movement_pm($loc_id, $entered, $left)
	{
		$entered = $this->get_character_names($entered);
		$left = $this->get_character_names($left);
		$recipients = $this->get_users_in_room($loc_id);

		$sql = 'SELECT loc_name, root_level, loc_subject, loc_text, bbcode_bitfield, bbcode_uid
				FROM ' . $this->loc_table . ' WHERE loc_id = ' . $loc_id;
		$result = $this->db->sql_query($sql);
		$loc = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$root_level = $row['root_level'];


		global $phpbb_root_path, $phpEx;

		if (!function_exists('submit_pm'))
		{
			include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
		}

		// TODO: better strings for the message & translation
		$pm = array(
			'message' 			=> join(', ', $entered) . ' enter ' . $loc['loc_name'] . '. ' . join(',', $left) . ' leave.
						
						' . $loc['loc_text'],
			'bbcode_bitfield'	=> $loc['bbcode_bitfield'],
			'bbcode_uid' 		=> $loc['bbcode_uid'],
			'enable_bbcode' 	=> 1,
			'icon_id' 			=> 7,
			'address_list' 		=> array('u' => array_map(function() { return 'to'; }, array_flip($recipients))),
			'from_user_ip' 		=> $this->user->ip,
			'from_user_id' 		=> $this->user->data['user_id'],
			'enable_sig' 		=> 1,
			'enable_urls' 		=> 1,
			'enable_bbcode' 	=> 1,
			'enable_smilies' 	=> 1,
			'reply_from_msg_id' => $loc['root_level']
		);
		submit_pm('reply', $loc['loc_subject'], $pm);
	}

	private function get_enroute($src, $dest) {
		$sql = 'SELECT loc_id FROM ' . $this->loc_table . ' WHERE src_id = ' . $src . ' AND dest_id = ' . $dest;
		$row = $this->db->sql_query($sql);
		return $row ? $row['loc_id'] : -1;
	}

	private function create_enroute(int $game_id, int $src, int $dest) {
		// TODO: Use translation for Enroute
		$sql = 'INSERT INTO ' . $this->loc_table . ' (loc_name, game_id, loc_subject, src_id, dest_id) 
				VALUES ( "Enroute ("
						+ (SELECT loc_name FROM ' . $this->loc_table . ' WHERE loc_id = ' . $src . ')
						+ " => "
						+ (SELECT loc_name FROM ' . $this->loc_table . ' WHERE loc_id = ' . $dest . ')
						+ ")",' . $game_id . ',
						"' . $this->create_room_subject($game_id, 'Enroute') . '",
						' . $src . ',' . $dest . ')';
		$this->db->sql_query($sql);
		$loc_id = $this->db->sql_nextid();
		$this->db->sql_freeresult($result);
		$this->create_root_pm($loc_id);
		return $loc_id;
	}

	private function create_root_pm(int $loc_id): int {
		global $phpbb_root_path, $phpEx;

		if (!function_exists('submit_pm'))
		{
			include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
		}

		$sql = 'SELECT loc_subject FROM ' . $this->loc_table . ' WHERE l.loc_id = ' . $loc_id;
		$result = $this->db->sql_query($sql);
		$loc = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// TODO: translate this thing too
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
		$msg_id = submit_pm('post', $loc['loc_subject'], $pm, false);

		$sql = 'UPDATE ' . $this->loc_table . ' SET root_level = ' . $msg_id . ' WHERE loc_id = ' . $loc_id;
		$this->db->sql_query($sql);
		return $msg_id;
	}
}
