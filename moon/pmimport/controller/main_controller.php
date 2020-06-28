<?php
/**
 *
 * Import PMs. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, moon, https://github.com/mebird
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace moon\pmimport\controller;

/**
 * Import PMs main controller.
 */
class main_controller
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\language\language */
	protected $language;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config		$config		Config object
	 * @param \phpbb\controller\helper	$helper		Controller helper object
	 * @param \phpbb\template\template	$template	Template object
	 * @param \phpbb\language\language	$language	Language object
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\language\language $language)
	{
		$this->config	= $config;
		$this->helper	= $helper;
		$this->template	= $template;
		$this->language	= $language;
	}

	/**
	 * Controller handler for route /demo/{name}
	 *
	 * @param string $name
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function handle($name)
	{
		$l_message = !$this->config['moon_pmimport_goodbye'] ? 'PMIMPORT_HELLO' : 'PMIMPORT_GOODBYE';
		$this->template->assign_var('PMIMPORT_MESSAGE', $this->language->lang($l_message, $name));

		return $this->helper->render('@moon_pmimport/pmimport_body.html', $name);
	}
}
