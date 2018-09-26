<?php
/**
*
* GanstaZ ESFFS. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	'ESFFS_EXCLUDE_IDS'			=> 'Exclude forums from stats',
	'ESFFS_EXCLUDE_IDS_EXPLAIN' => 'Empty field will trigger an error, so default value should be 0! Use comma-separated list to add forum id/s or just change existing one/s.',
]);
