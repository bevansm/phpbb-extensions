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

	public function sub_character(int $game_id, int $character_id, int $sub_user_id, boolean $add_to_pms)
	{
		if ($add_to_pms)
		{
			// TODO:
		}

		$sql = 'UPDATE ' . $this->char_table . ' SET user_id = ' . $sub_user_id . ' WHERE character_id = ' . $character_id;
		$this->db->sql_query($sql);
	}

	public function create_character(int $game_id, int $user_id, int $character_name)
	{
		// TODO:
	}

	public function delete_character(int $game_id, int $character_id)
	{
		// TODO:
	}


}
