<?php

/**
 * Extend default palette
 */
$GLOBALS['TL_DCA']['tl_member_group']['palettes']['default'] =
	str_replace('{redirect_legend', '{groupmounts_legend},member_group_mounts;{redirect_legend',
		$GLOBALS['TL_DCA']['tl_member_group']['palettes']['default']);

$GLOBALS['TL_DCA']['tl_member_group']['fields']['member_group_mounts'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_member_group']['member_group_mounts'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_member_group.name',
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);
