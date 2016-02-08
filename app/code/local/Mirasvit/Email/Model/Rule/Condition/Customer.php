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


class Mirasvit_Email_Model_Rule_Condition_Customer extends Mirasvit_Email_Model_Rule_Condition_Abstract
{
    public function loadAttributeOptions()
    {
        $attributes = array(
            'group_id'         => Mage::helper('email')->__('Customer: Group'),
            'lifetime_sales'   => Mage::helper('email')->__('Customer: Lifetime Sales'),
            'number_of_orders' => Mage::helper('email')->__('Customer: Number of Orders'),
            'is_subscriber'    => Mage::helper('email')->__('Customer: Is subscriber of newsletter'),
            'reviews_count'    => Mage::helper('email')->__('Customer: Number of reviews'),
            'last_order_date'  => Mage::helper('email')->__('Customer: Last order date'),
        );

        $arAttbiutes = Mage::getModel('customer/customer')->getAttributes();
        foreach ($arAttbiutes as $attr) {
            if ($attr->getStoreLabel()
                && $attr->getAttributeCode()) {
                $attributes[$attr->getAttributeCode()] = Mage::helper('email')->__('Customer: ').$attr->getStoreLabel();
            }
        }

        if (Mage::helper('mstcore')->isModuleInstalled('AW_Marketsuite')) {
            $attributes['mss_rule'] = Mage::helper('email')->__('Customer: AheadWorks MSS rule');
        }

        // asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    public function getInputType()
    {
        $type = 'string';

        switch ($this->getAttribute()) {
            case 'group_id':
                $type = 'multiselect';
            break;

            case 'is_subscriber':
            case 'mss_rule':
            case 'store_id':
                $type = 'select';
            break;

            case 'last_order_date':
            case 'created_in':
            case 'dob':
                $type = 'date';
            break;
        }

        return $type;
    }

    public function getValueElementType()
    {
        $type = 'text';

        switch ($this->getAttribute()) {
            case 'group_id':
                $type = 'multiselect';
            break;

            case 'is_subscriber':
            case 'mss_rule':
            case 'store_id':
                $type = 'select';
            break;

            case 'last_order_date':
            case 'created_in':
            case 'dob':
                $type = 'date';
            break;
        }

        return $type;
    }

    public function getValueElement()
    {
        $element = parent::getValueElement();
        switch ($this->getAttribute()) {
            case 'last_order_date':
            case 'created_in':
            case 'dob':
                $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
                break;
        }

        return $element;
    }

    public function getExplicitApply()
    {
    	$result = parent::getExplicitApply();
        switch ($this->getAttribute()) {
            case 'last_order_date':
            case 'created_in':
            case 'dob':
                $result = true;
                break;
        }

        return $result;
    }

    protected function _prepareValueOptions()
    {
        $selectOptions = array();

        if ($this->getAttribute() === 'group_id') {
            $selectOptions = Mage::helper('customer')->getGroups()->toOptionArray();
            array_unshift($selectOptions, array('value' => 0, 'label' => Mage::helper('email')->__('Not registered')));
        }

        if ($this->getAttribute() === 'is_subscriber') {
            $selectOptions = array(
                array('value' => 0, 'label' => Mage::helper('email')->__('No')),
                array('value' => 1, 'label' => Mage::helper('email')->__('Yes')),
            );
        }

        if ($this->getAttribute() === 'store_id') {
            $selectOptions = Mage::getModel('adminhtml/system_config_source_store')->toOptionArray();
        }

        if ($this->getAttribute() === 'mss_rule' && Mage::helper('mstcore')->isModuleInstalled('AW_Marketsuite')) {
            $ruleCollection = Mage::getModel('marketsuite/filter')->getActiveRuleCollection();
            foreach ($ruleCollection as $rule) {
                $selectOptions[] = array(
                    'value' => $rule->getId(),
                    'label' => $rule->getName(),
                );
            }
        }
        
        $this->setData('value_select_options', $selectOptions);

        $hashedOptions = array();
        foreach ($selectOptions as $o) {
            $hashedOptions[$o['value']] = $o['label'];
        }
        $this->setData('value_option', $hashedOptions);

        return $this;
    }

    public function validate(Varien_Object $object)
    {
        $attrCode       = $this->getAttribute();
        $data           = array();
        $reviewsCount   = 0;
        $lifetimeSales  = 0;
        $numberOfOrders = 0;
        $mssRule        = 0;
        $lastOrderDate  = null;

        $orders     = Mage::getModel('sales/order')->getCollection()
            ->setOrder('created_at', Varien_Data_Collection_Db::SORT_ORDER_DESC);
        $totals     = Mage::getResourceModel('sales/sale_collection');
        $subscriber = Mage::getModel('newsletter/subscriber');
        $customer   = Mage::getModel('customer/customer');
        if ($customerId = $object->getData('customer_id')) {
            $customer->load($customerId);
        } else {
            $customer->setWebsiteId(Mage::app()->getStore($object->getStoreId())->getWebsiteId());
            $customer->loadByEmail($object->getData('customer_email'));
        }

        if ($customer->getId()) {
            $data = $customer->getData();
            $subscriber->loadByEmail($customer->getEmail());

            $reviewsCount = Mage::getModel('review/review')->getCollection()
                ->addFieldToFilter('customer_id', $customer->getId())
                ->count();

            $customerTotals = $totals->setCustomerFilter($customer)
                ->setOrderStateFilter(Mage_Sales_Model_Order::STATE_CANCELED, true)
                ->load()
                ->getTotals();

            $lifetimeSales = floatval($customerTotals['lifetime']);
            $numberOfOrders = $orders->addFieldToFilter('customer_id', $customer->getId())->count();
            if ($numberOfOrders > 0) {
                $lastOrderDate = $orders->getFirstItem()->getCreatedAt();
            }

            if (Mage::helper('mstcore')->isModuleInstalled('AW_Marketsuite')) {
                $mssApi = Mage::getModel('marketsuite/api');
                if ($mssApi->checkRule($customer, (int) $this->getValue())) {
                    $mssRule = $this->getValue();
                }
            }
        } else {
            $email = $object->getData('customer_email');
            $subscriber->loadByEmail($email);
            $data = array('group_id' => 1);

            $customerTotals = $totals->addFieldToFilter('customer_email', $email)
                ->setOrderStateFilter(Mage_Sales_Model_Order::STATE_CANCELED, true)
                ->load()
                ->getTotals();

            $lifetimeSales = isset($customerTotals['lifetime']) ? floatval($customerTotals['lifetime']) : 0;
            $numberOfOrders = $orders->addFieldToFilter('customer_email', $email)->count();
            if ($numberOfOrders > 0) {
                $lastOrderDate = $orders->getFirstItem()->getCreatedAt();
            }
        }

        $object->addData($data)
            ->setData('is_subscriber', $subscriber->getId() ? 1 : 0)
            ->setData('reviews_count', $reviewsCount)
            ->setData('lifetime_sales', $lifetimeSales)
            ->setData('number_of_orders', $numberOfOrders)
            ->setData('mss_rule', $mssRule)
            ->setData('last_order_date', $lastOrderDate);

        $value = $object->getData($attrCode);

        return $this->validateAttribute($value);
    }
}
