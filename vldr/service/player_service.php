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

	public function create_character(int $game_id, int $user_id, string $character_name)
	{
		$sql = 'INSERT INTO ' . $this->char_table . ' ' .
				$this->db->sql_build_array('INSERT', array(
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
