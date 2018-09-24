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

	/** @var \phpbb\template\template */
	protected $template;

	/**
	* Constructor
	*
	* @param \phpbb\config\config			   $config	 Config object
	* @param \phpbb\db\driver\driver_interface $db		 Db object
	* @param \phpbb\language\language		   $language Language object
	* @param \phpbb\template\template		   $template Template object
	*/
	public function __construct(config $config, driver_interface $db, language $language, template $template)
	{
		$this->config	= $config;
		$this->db = $db;
		$this->language = $language;
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
			'core.index_modify_page_title' => 'modify_stats',
		];
	}

	/**
	* Event core.index_modify_page_title
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function modify_stats($event)
	{
		$sql = 'SELECT forum_id, forum_posts_approved, forum_topics_approved
				FROM ' . FORUMS_TABLE . '
				WHERE ' . $this->db->sql_in_set('forum_id', [2, 4]);
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

		// Assign index specific vars
		$this->template->assign_vars([
			'TOTAL_POSTS'  => $this->language->lang('TOTAL_POSTS_COUNT', $hidden['posts']),
			'TOTAL_TOPICS' => $this->language->lang('TOTAL_TOPICS', $hidden['topics']),
		]);
	}
}
