<?php
/**
*
* GanstaZ ESFFS. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
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
	/**
	* @param config			  $config	Config object
	* @param driver_interface $db		Db object
	* @param language		  $language Language object
	* @param request		  $request	Request object
	* @param template		  $template Template object
	*/
	public function __construct(
		protected config $config,
		protected driver_interface $db,
		protected language $language,
		protected request $request,
		protected template $template
	)
	{
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*/
	public static function getSubscribedEvents(): array
	{
		return [
			'core.acp_manage_forums_request_data' => 'esffs_manage_forums_request_data',
			'core.acp_manage_forums_display_form' => 'esffs_manage_forums_display_form',
			'core.index_modify_page_title'		  => 'esffs_modify_stats',
		];
	}

	/**
	* Event core.acp_manage_forums_request_data
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function esffs_manage_forums_request_data($event): void
	{
		$forum_data = $event['forum_data'];
		$forum_data['esffs_fid_enable'] = $this->request->variable('esffs_fid_enable', 0);
		$event['forum_data'] = $forum_data;
	}

	/**
	* Event core.acp_manage_forums_display_form
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function esffs_manage_forums_display_form($event): void
	{
		$template_data = $event['template_data'];
		$template_data['S_ESFFS_FID'] = $event['forum_data']['esffs_fid_enable'];
		$event['template_data'] = $template_data;
	}

	/**
	* Event core.index_modify_page_title
	*/
	public function esffs_modify_stats(): void
	{
		$sql = 'SELECT forum_posts_approved, forum_topics_approved
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

		if (!$hidden)
		{
			return;
		}

		// Update index specific vars
		$this->template->assign_vars([
			'TOTAL_POSTS'  => $this->language->lang('TOTAL_POSTS_COUNT', $hidden['posts']),
			'TOTAL_TOPICS' => $this->language->lang('TOTAL_TOPICS', $hidden['topics']),
		]);
	}
}
