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

	private function parse_time($str) {
		return DateTime::createFromFormat('Y-m-d H:i:s', $myString);
	}

	public function import(string $str, string $type, string $str_users, string $mode)
	{
		global $phpbb_root_path, $phpEx;

		$users = str_getcsv($str_users);
		try {
			$users = array_combine(array_column($arr, 0), array_column($arr, 1));
		} catch (Exception $e) {
			return 'Unable to parse users';
		}

		$pms = array();
		$err = $type === 'csv' ? $this->parse_csv($pms, $str, $users, $mode) : $this->parse_xml($pms, $str, $users, $mode);
		if ($err) {
			return $err;
		}

		if (!function_exists('user_get_id_name'))
		{
			include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
		}
		if (!function_exists('submit_pm'))
		{
			include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
		}
		if (!class_exists('parse_message'))
		{
			include($phpbb_root_path . 'includes/message_parser.' . $phpEx);
		}

		foreach ($pms as &$pm) {
			$to = array();
			$err = user_get_id_name($to, $pm['recipients']);
			if ($err) {
				return $err;
			}

			$from = array();
			$err = user_get_id_name($from, [$pm['sender']]);
			if ($err) {
				return $err;
			}

			$message_parser = new parse_message($pm['body']);
			$message_parser->parse(TRUE, TRUE, TRUE);
			$pm = array_merge($pm, array(
				'message_text' => $message_parser->message,
				'bbcode_bitfield' => $message_parser->bbcode_bitfield,
				'bbcode_uid' => $message_parser->bbcode_uid,
				'enable_bbcode' => 1,
				'icon_id' => 7,
				'address_list' => array('u' => array_map(function() { return 'to'; }, array_flip($to))),
				'from_user_ip' => $this->user->ip,
				'from_user_id' => $from[0],
				'enable_sig' => 1,
				'enable_urls' => 1,
				'enable_bbcode' => 1,
			));
		}

		// if we can already find this pm, just add recipients. else, send it.
		foreach ($pms as $pm) {
			$sql = 'SELECT msg_id, author_id
					FROM ' . PRIVMSGS_TABLE. '
					WHERE author_id = ' . $pm['from_user_id'] . '
					AND message_time ' . $pm['timestamp'] . '
					AND message_subject = ' . $this->$db->sql_escape($pm['subject']) . '
					AND message_text = ' . $this->$db->sql_escape($pm['message_text']);
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			if ($row) {
				$sql = 'INSERT INTO ' . PRIVMSGS_TO_TABLE . ' (msg_id, user_id, author_id) 
						VALUES '. implode(', ', array_map(
							function ($v, $k) { return '(' . $row['msg_id'] . ',' . $k . ',' . $row['author_id'] . ')'; }, 
							$pm['address_list']['u'])) . '
						ON DUPLICATE KEY UPDATE
							msg_id = VALUES(msg_id),
							user_id = VALUES(user_id),
							author_id = VALUES(author_id)';
				$db->sql_query($sql);
			} else {
				$msg_id = submit_pm('post', $pm['subject'], $pm, false);
				$sql = 'UPDATE ' . PRIVMSGS_TABLE . ' 
						SET message_time = ' . $pm['timestamp'] . '
						WHERE msg_id = ' . $msg_id;
				$db->sql_query($sql);
			}
		}
	}

	private function get_clean_username($un, $users) {
		$un = isset($users[$un]) ? $users[$un]: $un;
		return utf8_clean_string($un);
	}

	private function parse_xml(&$pms, $xml_str, $users, $mode)
	{
		// TODO:
		return FALSE;
	}

	private function parse_csv(&$pms, $csv_str, $users, $mode)
	{
		$qstr = '&quot;';
		$csv = str_getcsv($csv_str);
		while (count($csv)) {
			$elem = array_shift($csv);

			$body = '';
			$recipients = [];
			$sender = $mode === 'sent' ? utf8_clean_string($elem[1]) : $this->user->data->username_clean;
			$subject = $elem[0]; 

			$timestamp = strtotime($elem[2]);
			if (!$timestamp) {
				return 'Invalid CSV format.';
			}
			$timestamp = $timestamp->getTimestamp();

			$recipients[] = $received ? $this->user->data->username_clean : $this->get_clean_username(ltrim($elem[3], $qstr), $users);
			for ($i = 4; $i < count($elem); $i++) {
				if (strpos($elem[$i], $qstr) === 0) {
					$body .= join(",", array_slice($elem, $i));
					break;
				}
				$recipients[] = $this->get_clean_username(rtrim($elem[$i], $qstr), $users);
			}
			while(count($csv) && !($this->parse_time($csv[0][2]))) {
				$elem = array_filter(array_shift($csv));
				$body .= $elem ? join(",", $elem) : '\r\n';
			}
			$body = trim($body, $qstr);
			$pms[] = array(
				'sender' => $sender,
				'recipients' => $recipients,
				'body' => $body,
				'subject' => $subject,
				'timestamp' => $timestamp,
			);
		}
		return FALSE;
	}
}
