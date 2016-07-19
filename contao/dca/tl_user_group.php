<?php

/**
 * Extend default palette
 */
$GLOBALS['TL_DCA']['tl_user_group']['palettes']['default'] =
	str_replace('formp;', 'formp;{groupmounts_legend},user_group_mounts,member_group_mounts;',
		$GLOBALS['TL_DCA']['tl_user_group']['palettes']['default']);

$GLOBALS['TL_DCA']['tl_user_group']['fields']['user_group_mounts'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user_group']['user_group_mounts'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_user_group.name',
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_user_group']['fields']['member_group_mounts'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user_group']['member_group_mounts'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_member_group.name',
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);
