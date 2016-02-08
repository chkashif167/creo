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


/**
 * Event Class
 *
 * @category Mirasvit
 * @package  Mirasvit_Email
 */
class Mirasvit_Email_Model_Event extends Mage_Core_Model_Abstract
{
    protected $_args = null;

    protected function _construct()
    {
        $this->_init('email/event');
    }

    /**
     * Return array of event arguments
     *
     * @return array
     */
    public function getArgs()
    {
        if ($this->_args == null) {
            $this->_args = unserialize($this->getData('args_serialized'));
        }

        return $this->_args;
    }

    public function addProcessedTriggerId($triggerId)
    {
        $this->getResource()->addProcessedTriggerId($this->getId(), $triggerId);
    }

    public function removeProcessedTriggers()
    {
        $this->getResource()->removeProcessedTriggers($this->getId());
    }
}