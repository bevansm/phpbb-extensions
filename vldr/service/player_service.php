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


class player_service
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

	public function sub_character(int $game_id, int $user_id, int $sub_id, boolean $add_to_pms)
	{
		if ($add_to_pms)
		{
			$sql = 'INSERT INTO ' . PRIVMSGS_TO_TABLE . ' (msg_id, user_id, author_id) 
					SELECT to.msg_id, ' . $sub_id . ', to.author_id 
						FROM ' . $this->loc_table . ' l
					JOIN ' . PRIVMSGS_TABLE . ' m
						ON m.root_level = l.root_level
					JOIN ' . PRIVMSGS_TO_TABLE . ' to
						ON m.msg_id = to.msg_id
					WHERE l.game_id = ' . $game_id . '
						AND to.user_id = '  . $user_id;
			$this->db->sql_query($sql);
		}

		$sql = 'UPDATE ' . $this->char_table . ' 
				SET user_id = ' . $sub_id . ' 
				WHERE game_id = ' . $game_id . ' 
					AND user_id = ' . $user_id;
		$this->db->sql_query($sql);
	}

	public function create_character(int $game_id, int $user_id, string $character_name)
	{
		$sql = 'INSERT INTO ' . $this->char_table . ' ' .
				$db->sql_build_array('INSERT', array(
					'game_id' 			=> $game_id,
					'user_id' 			=> $user_id,
					'character_name' 	=> $character_name, 
				));
		$this->db->sql_query($sql);
	}

	public function delete_character(int $character_id)
	{
		$sql = 'DELETE FROM ' . $this->char_table . ' 
				WHERE character_id = ' . $character_id;
		$this->db->sql_query($sql);
	}


}
