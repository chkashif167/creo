<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     539
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Advd_Model_Observer extends Varien_Object
{
    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function onControllerActionPredispatch()
    {
        if (Mage::getSingleton('advd/config')->isReplaceDashboardLink()) {
            $menu = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode('menu');

            $itmDashboard = null;
            $itmAdvrDashboard = null;
            $itmAdvrUserDashboard = null;

            foreach ($menu->children() as $key => $children) {
                if ($key == 'dashboard') {
                    $itmDashboard = $children;
                    foreach ($itmDashboard->children->children() as $subKey => $subChildren) {
                        if ($subKey == 'advd_dashboard_global') {
                            $itmAdvrDashboard = $subChildren;
                        }
                        if ($subKey == 'advd_dashboard_user') {
                            $itmAdvrUserDashboard = $subChildren;
                        }
                    }
                }
            }

            if ($itmDashboard
                && $itmAdvrDashboard
                && Mage::getSingleton('admin/session')->isAllowed('dashboard/advd_dashboard_global')) {
                $itmDashboard->action = (string)$itmAdvrDashboard->action;
                unset($itmDashboard->children->advd_dashboard_global);
            } elseif ($itmDashboard && $itmAdvrUserDashboard
                && Mage::getSingleton('admin/session')->isAllowed('dashboard/advd_dashboard_user')) {
                $itmDashboard->action = (string)$itmAdvrUserDashboard->action;
                unset($itmDashboard->children->advd_dashboard_user);
            }
        }
    }

    public function notificationJob()
    {
        $emails = Mage::getModel('advd/notification')->getCollection()
            ->addFieldToFilter('is_active', 1);

        foreach ($emails as $email) {
            $email = $email->load($email->getId());
            if ($email->canSend()) {
                $email->send();
            }
        }
    }
}
