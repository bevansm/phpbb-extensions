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


class game_service
{
	protected $user;
	protected $db;
	protected $loc_table;
	protected $char_table;
	protected $game_table;
	protected $spec_table;

	public function __construct(user $user, db $db, string $game_table, string $loc_table, string $char_table, string $spec_table)
	{
		$this->user = $user;
		$this->db = $db;
		$this->loc_table = $loc_table;
		$this->char_table = $char_table;
		$this->game_table = $game_table;
		$this->spec_table = $spec_table;
	}

	public function create_game(string $game_name, string $game_code) -> int
	{
		$sql = 'INSERT INTO ' . $this->game_table . ' ' .
				$db->sql_build_array('INSERT', array(
					'game_name' => $game_name,
					'game_code'	=> $game_code,
					'host_id' 	=> $this->user->data['user_id'],
				));
		$this->db->sql_query($sql);
		return $this->db->sql_nextid();
	}

	public function transfer_game(int $game_id, int $user_id)
	{
		$sql = 'UPDATE ' . $this->game_table . ' 
				SET user_id = ' . $user_id . ' 
				WHERE game_id = ' . $game_id;
		$this->db->sql_query($sql);
	}

	public function delete_game(int $game_id)
	{
		$sql = 'DELETE FROM ' . $this->char_table . ' WHERE game_id = ' . $game_id . ';
				DELETE FROM ' . $this->game_table . ' WHERE game_id = ' . $game_id . ';
				DELETE FROM ' . $this->loc_table . 	' WHERE game_id = ' . $game_id . ';
				DELETE FROM ' . $this->spec_table .	' WHERE game_id = ' . $game_id . ';'; 
		$this->db->sql_query($sql);
	}

	public function add_spectator(int $game_id, $user_id)
	{	
		$sql = 'INSERT INTO ' . $this->spec_table . ' ' .
				$db->sql_build_array('INSERT', array(
					'game_id' => $game_id,
					'user_id' => $user_id
				));
		$this->db->sql_query($sql);
	}

	public function delete_spectator(int $game_id, $user_id)
	{	
		$sql = 'DELETE FROM ' . $this->spec_table . ' 
				WHERE game_id = ' . $game_id . ' AND user_id = ' . $user_id;
		$this->db->sql_query($sql);
	}

}
