<?php

namespace iMi\GroupMounts;

$GLOBALS['TL_DCA']['tl_user']['fields']['groups']['options_callback'] = array('iMi\GroupMounts\tl_user', 'loadMountedGroups');
$GLOBALS['TL_DCA']['tl_user']['fields']['groups']['save_callback'][] = array('iMi\GroupMounts\tl_user', 'saveGroups');

class tl_user extends \tl_user
{

    /**
     * Load accessible groups
     */
    public function loadMountedGroups()
    {
        $arrAllMounts = array();

        if ($this->User->isAdmin) {
            $objResult = \UserGroupModel::findAll();
            while($objResult->next()) {
                $arrAllMounts[$objResult->id] = $objResult->name;
            }
            return $arrAllMounts;
        }

        $arrGroups = deserialize($this->User->groups);

        // merge user group mounts of all user groups we are member in
        foreach($arrGroups as $intGroup) {
            $objGroup = \UserGroupModel::findByPk($intGroup);
            $arrMounts = deserialize($objGroup->user_group_mounts);
            if (empty($arrMounts)) {
                $arrMounts = array();
            }
            foreach($arrMounts as $intMountId) {
                $objGroupMounted = \UserGroupModel::findByPk($intMountId);
                $arrAllMounts[$intMountId] = $objGroupMounted->name;
            }
        }

        asort($arrAllMounts);
        return $arrAllMounts;
    }


    /**
     * Ensure the user can not change the hidden groups
     */
    public function saveGroups($strData, $objDc)
    {
        $objUser = \UserModel::findByPk($objDc->id);
        $arrOldGroups = deserialize($objUser->groups);
        if (empty($arrOldGroups)) {
            return $strData;
        }

        $arrData = unserialize($strData);
        if (empty($arrData)) {
            $arrData = array();

        }
        $arrMounted = $this->loadMountedGroups(); // allo
        foreach($arrOldGroups as $oldGroup) {
            if (!isset($arrMounted[$oldGroup])) { // not accessible but set => add
                $arrData[] = $oldGroup;

            }
        }
        return serialize($arrData);
    }
}
