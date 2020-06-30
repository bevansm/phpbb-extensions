<?php
/**
 *
 * PM Import. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, mebird, https://github.com/mebird
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mebird\pmimport\service;
use phpbb\db\driver\factory as db;

/**
 * PM Import Service info.
 */
class import_service
{
	/** @var \phpbb\user */
	protected $user;

	/** @var db */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param \phpbb\user $user       User object
	 * @param string      $table_name The name of a db table
	 */
	public function __construct(\phpbb\user $user, db $db)
	{
		$this->user = $user;
		$this->db = $db;
	}

	/**
	 * Get user object
	 *
	 * @return \phpbb\user $user User object
	 */
	public function get_user()
	{
		var_dump($this->table_name);
		return $this->user;
	}
}
