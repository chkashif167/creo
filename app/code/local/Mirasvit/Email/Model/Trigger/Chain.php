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


class Mirasvit_Email_Model_Trigger_Chain extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('email/trigger_chain');
    }

    public function getTemplate()
    {
        $template = null;
        $info = explode(':', $this->getTemplateId());

        switch ($info[0]) {
            case 'emaildesign':
                $template = Mage::getModel('emaildesign/template')->load($info[1]);
                break;

            case 'email':
                $template = Mage::getModel('core/email_template')->load($info[1]);
                break;

            case 'newsletter':
                $template = Mage::getModel('newsletter/template')->load($info[1]);
                break;
        }

        return $template;
    }

    public function prepareSerializedData()
    {
        if (!@unserialize($this->getDelay())) {
            return;
        }
        
        foreach (unserialize($this->getDelay()) as $key => $value) {
            switch ($key) {
                case 'days':
                    $value = $value / 60 / 60 / 24;
                    break;

                case 'hours':
                    $value = $value / 60 / 60;
                    break;

                case 'minutes':
                    $value = $value / 60;
                    break;
            }
            $this->setData($key, $value);
            $this->setDataChanges(false);
        }
    }

    public function getScheduledAt($time)
    {
        $scheduledAt = $time;
        $excludeDays = $this->getExcludeDays();
        $days        = ($this->getDays()) * 24 * 60 * 60;
        $hours       = $this->getHours() * 60 * 60;
        $minutes     = $this->getMinutes() * 60;
        $type        = (!$this->getType()) ? 'after' : $this->getType();

        if ($type == 'at') {
            $scheduledAt = $time + (($days - ($time - strtotime('00:00', $time)))  + $hours + $minutes);
            if ($time >= $scheduledAt) {
                $scheduledAt += 86400;
            }
        } else {
            $scheduledAt = $time + $days + $hours + $minutes;
        }

        $scheduledAt = $scheduledAt + $this->addExcludedDays($scheduledAt, $excludeDays) * 86400;

        return $scheduledAt;
    }

    private function addExcludedDays($time, $excludeDaysOfWeek)
    {
        $result = 0;
        if (is_array($excludeDaysOfWeek) && (count($excludeDaysOfWeek) > 0)) {
            while (in_array(date('w', $time + $result * 86400), $excludeDaysOfWeek)) {
                $result++;

                if ($result > 7) {
                    break;
                }
            }
        }

        return $result;
    }
}