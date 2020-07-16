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

	/** @var \phpbb\textformatter\s9e\parser */
	protected $parser;

	/**
	 * Constructor
	 *
	 * @param \phpbb\user 						$user       User object
	 * @param string      						$table_name The name of a db table
	 * @param \phpbb\textformatter\s9e\parser	$parser 	A bbcode text parser
	 */
	public function __construct(\phpbb\user $user, db $db, \phpbb\textformatter\s9e\parser $parser)
	{
		$this->user = $user;
		$this->db = $db;

		$parser->enable_bbcodes();
		$parser->enable_magic_url();
		$parser->enable_smilies();
		$this->parser = $parser;
	}

	private function parse_users(&$users, $str) 
	{
		if (!trim($str))
		{
			return false;
		}
		foreach (explode("\n", $str) as $row)
		{
			$um = array_filter(array_map('trim', explode(",", $row)));
			if (count($um) != 2)
			{
				return $this->user->lang('UCP_PM_IMPORT_ERROR_USR_FORMAT', $row);
			}
			if (array_key_exists($um[0], $users)) {
				return $this->user->lang('UCP_PM_IMPORT_ERROR_USR_DUP', $um[0]);
			}
			$users[utf8_clean_string($um[0])] = utf8_clean_string($um[1]);
		}
		return false;
	}

	public function import(string $str, string $type, string $str_users, string $mode)
	{

		$users = [];
		$err = $this->parse_users($users, $str_users);
		if ($err) {
			return $err;
		}

		$pms = array();
		$err = $type === 'csv' 
			? $this->parse_csv($pms, $str, $users, $mode) 
			: $this->parse_xml($pms, $str, $users, $mode);
		if ($err) {
			return $err;
		}

		$err = $this->send_pms($pms);
		if ($err) {
			return $err;
		}

	}

	private function get_clean_username($un, $users) {
		$clean = utf8_clean_string($un);
		return isset($users[$clean]) ? $users[$clean] : $clean;
	}

	private function parse_xml(&$pms, $xml_str, $users, $mode)
	{
		// TODO:
		return FALSE;
	}

	private function parse_csv(&$pms, $csv_str, $users, $mode)
	{
		$received = $mode === 'received';
		$qstr = '"';
		$csv = array_map(function ($s) { return explode(",", strip_tags($s)); }, explode("\n", $csv_str));
		while (count($csv)) {
			$elem = array_shift($csv);
			$body = '';
			$recipients = [];
			$sender = $received 
				? $this->get_clean_username(utf8_clean_string($elem[1]), $users) 
				: $this->user->data['username_clean'];
			$subject = $elem[0]; 

			$timestamp = strtotime($elem[2]);
			if (!$timestamp) {
				return $this->user->lang('UCP_PM_IMPORT_ERROR_INVALID_TIME', $elem[2]);
			}

			if ($received) {
				$recipients[] = $this->user->data['username_clean'];
			} elseif (mb_strpos($elem[3], $qstr) === 0) {
				$recipients[] = $this->get_clean_username(trim($elem[3], $qstr), $users);
				for ($i = 4; $i < count($elem); $i++) {
					$recipients[] = $this->get_clean_username(trim($elem[$i], $qstr), $users);
					if (mb_strpos($elem[$i], $qstr) !== FALSE) {
						$body .= join(",", array_filter(array_slice($elem, $i + 1))) . "\n";
						break;
					}
				}
			} else {
				$recipients[] = $this->get_clean_username($elem[3], $users);
				$body .= join(",", array_filter(array_slice($elem, 4))) . "\n";
			}
	
			while(count($csv) && strtotime($csv[0][2]) === FALSE) {
				$elem = array_filter(array_shift($csv));
				$body .= count($elem) ? join(",", $elem) : "\n";
			}

			$pms[] = array(
				'sender' => $sender,
				'recipients' => array_unique($recipients),
				'body' => trim($body, $qstr),
				'subject' => $subject,
				'timestamp' => $timestamp,
			);
		}
		return FALSE;
	}

	private function send_pms($pms) {
		global $phpbb_root_path, $phpEx;

		if (!function_exists('user_get_id_name'))
		{
			include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
		}
		if (!function_exists('submit_pm'))
		{
			include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
		}

		foreach ($pms as &$pm) {
			$to = array();
			$err = user_get_id_name($to, $pm['recipients']);
			if ($err || count($to) != count($pm['recipients'])) {
				return $this->user->lang('UCP_PM_IMPORT_ERROR_USERS', implode(', ', $pm['recipients']));
			}

			$from = array();
			$sender = [$pm['sender']];
			$err = user_get_id_name($from, $sender);
			if ($err) {
				return  $this->user->lang('UCP_PM_IMPORT_ERROR_USERS', $pm['sender']);
			}

			$pm = array_merge($pm, array(
				'message' => $this->parser->parse($pm['body']),
				'bbcode_bitfield' => '',
				'bbcode_uid' => '',
				'enable_bbcode' => 1,
				'icon_id' => 7,
				'address_list' => array('u' => array_map(function() { return 'to'; }, array_flip($to))),
				'from_user_ip' => $this->user->ip,
				'from_user_id' => $from[0],
				'enable_sig' => 1,
				'enable_urls' => 1,
				'enable_bbcode' => 1,
				'enable_smilies' => 1
			));
		}

		// if we can already find this pm, just add recipients. else, send it.
		foreach ($pms as $pm) {
			$sql = 'SELECT msg_id, author_id FROM ' . PRIVMSGS_TABLE. '
					WHERE author_id = ' . $pm['from_user_id'] . '
						AND message_time = ' . $pm['timestamp'] . '
						AND message_subject = "' . $this->db->sql_escape($pm['subject']) . '"';
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row) {
				$sql = 'INSERT INTO ' . PRIVMSGS_TO_TABLE . ' (msg_id, user_id, author_id) 
						VALUES '. implode(', ', array_map(
							function ($k) use ($row) { return '(' . $row['msg_id'] . ',' . $k . ',' . $row['author_id'] . ')'; }, 
							array_keys($pm['address_list']['u']))) . '
						ON DUPLICATE KEY UPDATE
							msg_id = VALUES(msg_id),
							user_id = VALUES(user_id),
							author_id = VALUES(author_id)';
				$this->db->sql_query($sql);
			} else {
				/**
				 * only if subject is prepended by "RE:":
				 * let's look for something else with this subject before this timestamp
				 * if we find it, choose the one w/ the most replies so far
				 */
				$root_id = 0;
				if (strpos($pm['subject'], "Re:") === 0) {
					// Check if such a parent message might exist
					$sql = 'SELECT msg_id FROM ' . PRIVMSGS_TABLE . ' pm
								WHERE message_time < ' . $pm['timestamp'] . '
								AND message_subject = "' . substr($pm['subject'], 4) . '"
							ORDER BY ( SELECT COUNT(msg_id) FROM ' . PRIVMSGS_TABLE . ' pm2 WHERE pm2.root_level = pm.msg_id )
							DESC LIMIT 1';
					$result = $this->db->sql_query($sql);
					$row = $this->db->sql_fetchrow($result);
					$this->db->sql_freeresult($result);
					if ($row) {
						$root_id = $row['msg_id'];
					}
				}

				$pm = array_merge($pm, array(
					'message' => $this->parser->parse($pm['body']),
					'bbcode_bitfield' => '',
					'bbcode_uid' => '',
					'enable_bbcode' => 1,
					'icon_id' => 7,
					'address_list' => array('u' => array_map(function() { return 'to'; }, array_flip($to))),
					'from_user_ip' => $this->user->ip,
					'from_user_id' => $from[0],
					'enable_sig' => 1,
					'enable_urls' => 1,
					'enable_bbcode' => 1,
					'enable_smilies' => 1,
					'reply_from_msg_id' => $root_id
				));

				// update the timestamp of this message
				$msg_id = submit_pm('reply', $pm['subject'], $pm, false);
				$sql = 'UPDATE ' . PRIVMSGS_TABLE . ' 
						SET message_time = ' . $pm['timestamp'] . '
						WHERE msg_id = ' . $msg_id;
				$this->db->sql_query($sql);
				
				// check & update any root ids if this is not a reply
				$sql = 'UPDATE ' . PRIVMSGS_TABLE . ' SET root_level = ' . $msg_id . '
							WHERE message_subject = "Re: ' . $pm['subject'] . '"
							AND root_level = 0 
							AND message_time > ' . $pm['timestamp'];
				$this->db->sql_query($sql);
			}
		}
	}
}
