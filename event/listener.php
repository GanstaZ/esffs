<?php
/**
*
* GanstaZ ESFFS. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\esffs\event;

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* GanstaZ ESFFS Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/**
	* Constructor
	*
	* @param \phpbb\config\config			   $config	 Config object
	* @param \phpbb\db\driver\driver_interface $db		 Db object
	* @param \phpbb\language\language		   $language Language object
	* @param \phpbb\request\request			   $request	 Request object
	* @param \phpbb\template\template		   $template Template object
	*/
	public function __construct(config $config, driver_interface $db, language $language, request $request, template $template)
	{
		$this->config	= $config;
		$this->db = $db;
		$this->language = $language;
		$this->request	= $request;
		$this->template = $template;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	*/
	public static function getSubscribedEvents()
	{
		return [
			'core.acp_manage_forums_request_data'	 => 'esffs_manage_forums_request_data',
			'core.acp_manage_forums_initialise_data' => 'esffs_manage_forums_initialise_data',
			'core.acp_manage_forums_display_form'	 => 'esffs_manage_forums_display_form',
			'core.acp_board_config_edit_add' => 'board_config_add',
			'core.index_modify_page_title'	 => 'modify_stats',
		];
	}

	/**
	* Event core.acp_manage_forums_request_data
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function esffs_manage_forums_request_data($event)
	{
		$forum_data = $event['forum_data'];
		$forum_data['esffs_fid_enable'] = $this->request->variable('esffs_fid_enable', 0);
		$event['forum_data'] = $forum_data;
	}

	/**
	* Event core.acp_manage_forums_initialise_data
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function esffs_manage_forums_initialise_data($event)
	{
		if ($event['action'] == 'add')
		{
			$forum_data = $event['forum_data'];
			$forum_data['esffs_fid_enable'] = (bool) false;
			$event['forum_data'] = $forum_data;
		}
	}

	/**
	* Event core.acp_manage_forums_display_form
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function esffs_manage_forums_display_form($event)
	{
		$template_data = $event['template_data'];
		$template_data['S_ESFFS_FID'] = $event['forum_data']['esffs_fid_enable'];
		$event['template_data'] = $template_data;
	}

	/**
	* Event core.acp_board_config_edit_add
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function board_config_add($event)
	{
		if ($event['mode'] === 'settings')
		{
			$display_vars = $event['display_vars'];

			$set_data = [
				'esffs_enable' => [
					'lang'	   => 'ESFFS_ENABLE',
					'validate' => 'bool',
					'type'	   => 'radio:yes_no',
					'explain'  => true
				],
			];

			$display_vars['vars'] = phpbb_insert_config_array($display_vars['vars'], $set_data, ['after' => 'warnings_expire_days']);

			$event['display_vars'] = $display_vars;
		}
	}

	/**
	* Event core.index_modify_page_title
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function modify_stats($event)
	{
		// If not enabled, then stop the process.
		if (!$this->config['esffs_enable'])
		{
			return;
		}

		$sql = 'SELECT forum_id, forum_posts_approved, forum_topics_approved, esffs_fid_enable
				FROM ' . FORUMS_TABLE . '
				WHERE esffs_fid_enable = 1';
		$result = $this->db->sql_query($sql);

		$hidden = [
			'posts'	 => (int) $this->config['num_posts'],
			'topics' => (int) $this->config['num_topics'],
		];

		while ($row = $this->db->sql_fetchrow($result))
		{
			$hidden['posts']  -= (int) $row['forum_posts_approved'];
			$hidden['topics'] -= (int) $row['forum_topics_approved'];
		}
		$this->db->sql_freeresult($result);

		// Update index specific vars
		$this->template->assign_vars([
			'TOTAL_POSTS'  => $this->language->lang('TOTAL_POSTS_COUNT', $hidden['posts']),
			'TOTAL_TOPICS' => $this->language->lang('TOTAL_TOPICS', $hidden['topics']),
		]);
	}
}
