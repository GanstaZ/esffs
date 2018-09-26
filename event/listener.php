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
			'core.acp_board_config_edit_add' => 'board_config_add',
			'core.index_modify_page_title' => 'modify_stats',
		];
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
			$this->language->add_lang('esffs_acp', 'ganstaz/esffs');

			$set_data = $event['display_vars'];

			$set_data['vars']['esffs_exclude_ids'] = [
				'lang' => 'ESFFS_EXCLUDE_IDS',
				'validate' => 'string', 'type' => 'text:40:255',
				'explain' => true
			];

			$event['display_vars'] = $set_data;
		}
	}

	/**
	* Event core.index_modify_page_title
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function modify_stats($event)
	{
		$exc_ops = $this->config['esffs_exclude_ids'];

		// If strings is empty ('' or 0), then stop the process.
		if (!$exc_ops)
		{
			return;
		}

		// Check spaces in a string and remove if any.
		if (strpos($exc_ops, ' ') !== false)
		{
			$exc_ops = str_replace(' ', '', $exc_ops);
		}

		$exclude_ids_ary = [];
		// Convert string into an array and assign validated ids into exclude_ids_ary.
		foreach (explode(',', $exc_ops) as $e_id)
		{
			if (ctype_digit($e_id) && $e_id > 1)
			{
				$exclude_ids_ary[] = $e_id;
			}
		}

		if ($exclude_ids_ary)
		{
			$sql = 'SELECT forum_id, forum_posts_approved, forum_topics_approved
					FROM ' . FORUMS_TABLE . '
					WHERE ' . $this->db->sql_in_set('forum_id', $exclude_ids_ary);
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
}
