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
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_EmailSmtp_Model_Observer extends Varien_Object
{
    public function clearOldData()
    {
        $smtpLoggingEnabled = Mage::getSingleton('emailsmtp/config')->isSmtpLoggingEnabled();
        $logsDelete = Mage::getSingleton('emailsmtp/config')->isLogsDelete();

        if ($smtpLoggingEnabled && $logsDelete) {
            $smtpMailLogsDays = Mage::getSingleton('emailsmtp/config')->getSmtpMailLogsDays();
            if ($smtpMailLogsDays) {
                $time = date('Y-m-d H:i:s', Mage::getSingleton('core/date')->gmtTimestamp() - $smtpMailLogsDays * 24 * 60 * 60);
                $resource = Mage::getSingleton('core/resource');
                $write = $resource->getConnection('core_write');
                $write->query('DELETE FROM '.$resource->getTableName('emailsmtp/mail')." WHERE updated_at > '".$time."'");
            }
        }
    }
}
