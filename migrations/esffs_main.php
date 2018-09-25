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
    public function update_data()
    {
        return array(
            // Add the config variables we want to be able to set
			['config.add', ['esffs_exclude_ids', '']],
        );
    }

    public function revert_schema()
    {
        return array(
            // Remove the config variables
			['config.remove', ['esffs_exclude_ids']],
        );
    }
}
