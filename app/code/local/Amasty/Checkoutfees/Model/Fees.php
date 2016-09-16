<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */
class Amasty_Checkoutfees_Model_Fees extends Mage_Rule_Model_Rule
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('amcheckoutfees/fees');
    }

    public function massDelete($ids)
    {
        return $this->getResource()->massDelete($ids);
    }

    public function massEnable($ids)
    {
        return $this->getResource()->massEnable($ids);
    }

    public function massDisable($ids)
    {
        return $this->getResource()->massDisable($ids);
    }

    public function getTitleForStore()
    {
        $store = Mage::app()->getStore()->getId();
        $title = $this->getData('title') ? unserialize($this->getData('title')) : array();
        if (isset($title[$store]) && !empty($title[$store])) {
            $title = $title[$store];
        } else if (isset($title[0]) && !empty($title[0])) {
            $title = $title[0];
        } else {
            $title = $this->getData('name');
        }

        return $title;
    }

    public function validateFee()
    {
        /**@var $model Mage_SalesRule_Model_Rule */
        $quote     = Mage::getSingleton('checkout/session')->getQuote();
        $address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        $store     = Mage::app()->getStore()->getId();
        $custGroup = Mage::getSingleton('customer/session')->getCustomerGroupId();

        // validate Store
        if (strpos($this->getStores(), ",$store,") === false && strpos($this->getStores(), ",0,") === false) {
            return false;
        }

        // validate Customer Group
        if (strpos($this->getCustGroups(), ",$custGroup,") === false) {
            return false;
        }

        // validate other Conditions tab
        $result = $this->validate($address);

        return $result;
    }

    public function getConditionsInstance()
    {
        return Mage::getModel('amcheckoutfees/rule_condition_combine');
    }


    /**
     * Initialize rule model data from array
     *
     * @param   array $rule
     *
     * @return  Mage_SalesRule_Model_Rule
     */
    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
        }

        return $this;
    }

    /**
     * Set specified data to current rule.
     * Set conditions and actions recursively.
     * Convert dates into Zend_Date.
     *
     * @param array $data
     *
     * @return array
     */
    protected function _convertFlatToRecursive(array $data)
    {
        $arr = array();
        foreach ($data as $key => $value) {
            if (($key === 'conditions' || $key === 'actions') && is_array($value)) {
                foreach ($value as $id => $data) {
                    $path = explode('--', $id);
                    $node =& $arr;
                    for ($i = 0, $l = sizeof($path); $i < $l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = array();
                        }
                        $node =& $node[$key][$path[$i]];
                    }
                    foreach ($data as $k => $v) {
                        $node[$k] = $v;
                    }
                }
            } else {
                /**
                 * Convert dates into Zend_Date
                 */
                if (in_array($key, array('from_date', 'to_date')) && $value) {
                    $value = Mage::app()->getLocale()->date(
                        $value,
                        Varien_Date::DATE_INTERNAL_FORMAT,
                        null,
                        false
                    );
                }
                $this->setData($key, $value);
            }
        }

        return $arr;
    }


    protected function _beforeSave()
    {
        $this->_setWebsiteIds();

        return parent::_beforeSave();
    }

    protected function _setWebsiteIds()
    {
        $websites = array();

        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $websites[$website->getId()] = $website->getId();
                }
            }
        }

        $this->setOrigData('website_ids', $websites);
    }

    protected function _beforeDelete()
    {
        $this->_setWebsiteIds();

        return parent::_beforeDelete();
    }

}