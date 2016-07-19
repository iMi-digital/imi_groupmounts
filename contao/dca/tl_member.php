<?php

namespace iMi\GroupMounts;

$GLOBALS['TL_DCA']['tl_member']['fields']['groups']['options_callback'] = array('iMi\GroupMounts\tl_member', 'loadMountedGroups');
$GLOBALS['TL_DCA']['tl_member']['fields']['groups']['save_callback'][] = array('iMi\GroupMounts\tl_member', 'saveGroups');
$GLOBALS['TL_DCA']['tl_member']['fields']['groups']['eval']['mandatory'] = true;
$GLOBALS['TL_DCA']['tl_member']['config']['onload_callback'][] = array('\iMi\GroupMounts\tl_member', 'checkPermission');

class tl_member extends \tl_member
{
    /**
     * Show only members which are in at least one of the mounted groups
     */
    public function checkPermission()
    {
        $mountedGroups = $this->loadMountedGroups();
        $arrMemberMounts = array();
        $objMember = \MemberModel::findAll();
        while($objMember->next()) {
            $arrGroups = deserialize($objMember->groups);
            if (!is_array($arrGroups)) {
                continue;
            }
            foreach($arrGroups as $intGroupId) { // any of the groups the member is in
                if ($mountedGroups[$intGroupId]) { // is mounted? -> show
                    $arrMemberMounts[] = $objMember->id;
                    continue 2;
                }
            }
        }

        if (empty($arrMemberMounts)) {
            $arrMemberMounts = array(0);
        }
        $GLOBALS['TL_DCA']['tl_member']['list']['sorting']['root'] = $arrMemberMounts;
    }

    /**
     * Load accessible groups
     */
    public function loadMountedGroups()
    {
        $arrAllMounts = array();

        if ($this->User->isAdmin) {
            $objResult = \MemberGroupModel::findAll();
            while($objResult->next()) {
                $arrAllMounts[$objResult->id] = $objResult->name;
            }
            return $arrAllMounts;
        }

        $arrGroups = deserialize($this->User->groups);
        // merge member group mounts of all user groups we are member in
        foreach($arrGroups as $intGroup) {
            $objGroup = \UserGroupModel::findByPk($intGroup);
            $arrMounts = deserialize($objGroup->member_group_mounts);
            if (empty($arrMounts)) {
                $arrMounts = array();
            }
            foreach($arrMounts as $intMountId) {
                $objGroupMounted = \MemberGroupModel::findByPk($intMountId);
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
        $objMember = \MemberModel::findByPk($objDc->id);
        $arrOldGroups = deserialize($objMember->groups);
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
