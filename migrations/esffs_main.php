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
			'drop_columns' => [
				$this->table_prefix . 'forums' => [
					'esffs_fid_enable',
				],
			],
		];
	}
}
