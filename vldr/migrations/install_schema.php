<?php
/**
 *
 * PM Import. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, mebird, https://github.com/mebird
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mebird\pmimport\migrations;

class install_schema extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return array('\phpbb\db\migration\data\v320\v320');
	}

	public function update_schema()
	{

		return array(
			'add_tables'	=> array(
				$this->table_prefix . 'vldr_games'	=> array(
					'COLUMNS'	=> array(
						'game_id'	=> array('UINT',  NULL, 'auto_increment'),
						'game_name' => array('XSTEXT_UNI', ''),
						'host_id'	=> array('UINT', 0),
						'host_name'	=> array('VCHAR_UNI', ''),
					),
					'PRIMARY_KEY'	=> 'game_id',
					'KEYS'	=> array(
						'host_id'	=> array('INDEX', 'host_id'),
					),
				),
				$this->table_prefix . 'vldr_characters'	=> array(
					'COLUMNS'	=> array(
						'character_id'		=> array('UINT',  NULL, 'auto_increment'),
						'character_name'	=> array('VCHAR_UNI', ''),
						'user_id'			=> array('UINT', 0),
						'game_id'			=> array('UINT', 0),
						'loc_id'			=> array('UINT', NULL),
					),
					'PRIMARY_KEY'	=> 'character_id',
					'KEYS'	=> array(
						'user_id'	=> array('INDEX', 'user_id'),
						'game_id'	=> array('INDEX', 'game_id'),
						'loc_id'	=> array('INDEX', 'loc_id'),
					),
				),
				$this->table_prefix . 'vldr_locations'	=> array(
					'COLUMNS'	=> array(
						'loc_id'		=> array('UINT',  NULL, 'auto_increment'),
						'loc_name'		=> array('VCHAR_UNI', ''),
						'root_level'	=> array('UINT', NULL),
						'game_id'		=> array('UINT', 0),
						'src_id'		=> array('UINT', NULL),
						'dest_id'		=> array('UINT', NULL),
					),
					'PRIMARY_KEY'	=> 'loc_id',
					'KEYS'	=> array(
						'game_id'		=> array('INDEX', 'game_id'),
						'unq_enroute'	=> array('UNIQUE', array('src_id', 'dest_id')),
					),
				),
			),
		);
	}
}
