<?php
/**
*
* GanstaZ ESFFS. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
* Translated By : Bassel Taha Alhitary <http://alhitary.net>
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
	'ACP_ESFFS_LEGEND'			=> 'استبعاد المنتدى من الإحصائيات',
	'ACP_ESFFS_EXCLUDE'			=> 'استبعاد المنتدى',
	'ACP_ESFFS_EXCLUDE_EXPLAIN' => 'اختار <strong>نعم</strong> لإستبعاد هذا المنتدى من الإحصائيات (الصفحة الرئيسية الخ.).',
]);
