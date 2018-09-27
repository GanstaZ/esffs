<?php
/**
*
* GanstaZ ESFFS. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\esffs\migrations;

class esffs_main extends \phpbb\db\migration\migration
{
	/**
	* If our config variable already exists in the db
	* skip this migration.
	*/
	public function effectively_installed()
	{
		return isset($this->config['esffs_enable']);
	}

	public function update_data()
	{
		return [
			// Add the config variable we want to be able to set
			['config.add', ['esffs_enable', 1]],
		];
	}

	public function update_schema()
	{
		return [
			'add_columns' => [
				$this->table_prefix . 'forums' => [
					'esffs_fid_enable' => ['BOOL', 0],
				],
			],
		];
	}

	public function revert_schema()
	{
		return [
			// Remove the config variable
			['config.remove', ['esffs_enable']],

			'drop_columns' => [
				$this->table_prefix . 'forums' => [
					'esffs_fid_enable',
				],
			],
		];
	}
}
