<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
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
	'ACP_ESFFS_LEGEND'			=> 'Esffs settings',
	'ACP_ESFFS_EXCLUDE'			=> 'Exclude forum',
	'ACP_ESFFS_EXCLUDE_EXPLAIN' => 'Set <strong>Yes</strong> to exclude this forum from statistics.',
]);
