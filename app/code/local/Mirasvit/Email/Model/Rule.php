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


class Mirasvit_Email_Model_Rule extends Mage_Rule_Model_Rule
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('email/rule');
        $this->setIdFieldName('rule_id');
    }

    public function getConditionsInstance()
    {
        return Mage::getModel('email/rule_condition_combine');
    }

    public function getActionsInstance()
    {
        return Mage::getModel('email/rule_action_collection');
    }

     /**
     * Reset rule combine conditions
     *
     * @param null|Mage_Rule_Model_Condition_Combine $conditions
     *
     * @return Mage_Rule_Model_Abstract
     */
    protected function _resetConditions($conditions = null)
    {
        if (is_null($conditions)) {
            $conditions = $this->getConditionsInstance();
        }
        $conditions->setRule($this)->setId('1')->setPrefix('conditions');
        $this->setConditions($conditions);

        return $this;
    }

    /**
     * Reset rule actions
     *
     * @param null|Mage_Rule_Model_Action_Collection $actions
     *
     * @return Mage_Rule_Model_Abstract
     */
    protected function _resetActions($actions = null)
    {
        if (is_null($actions)) {
            $actions = $this->getActionsInstance();
        }
        $actions->setRule($this)->setId('1')->setPrefix('actions');
        $this->setActions($actions);

        return $this;
    }

    public function toString($format = '')
    {
        $string = $this->getConditions()->asStringRecursive();

        $string = nl2br(preg_replace('/ /', '&nbsp;', $string));

        return $string;
    }
}