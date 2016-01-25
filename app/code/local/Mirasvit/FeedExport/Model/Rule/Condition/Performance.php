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
 * @package   Advanced Product Feeds
 * @version   1.1.2
 * @build     616
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_Model_Rule_Condition_Performance extends Mage_Rule_Model_Condition_Abstract
{
    public function loadAttributeOptions()
    {
        $attributes =array(
            'clicks_7'   => Mage::helper('feedexport')->__('Last 7-days Clicks'),
            'orders_7'   => Mage::helper('feedexport')->__('Last 7-days Orders'),
            'revenue_7'  => Mage::helper('feedexport')->__('Last 7-days Revenue'),
            'cr_7'       => Mage::helper('feedexport')->__('Last 7-days Conversion Rate (%)'),

            'clicks_14'  => Mage::helper('feedexport')->__('Last 14-days Clicks'),
            'orders_14'  => Mage::helper('feedexport')->__('Last 14-days Orders'),
            'revenue_14' => Mage::helper('feedexport')->__('Last 14-days Revenue'),
            'cr_14'      => Mage::helper('feedexport')->__('Last 14-days Conversion Rate (%)'),

            'clicks_30'  => Mage::helper('feedexport')->__('Last 30-days Clicks'),
            'orders_30'  => Mage::helper('feedexport')->__('Last 30-days Orders'),
            'revenue_30' => Mage::helper('feedexport')->__('Last 30-days Revenue'),
            'cr_30'      => Mage::helper('feedexport')->__('Last 30-days Conversion Rate (%)'),
        );

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    public function collectValidatedAttributes($productCollection)
    {
        $attribute = $this->getAttribute();

        $arr    = explode('_', $attribute);
        $type   = $arr[0];
        $period = $arr[1];

        $date = new Zend_Date();
        $date->sub($period * 24 * 60 * 60);

        $resource   = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');

        switch($type) {
            case 'clicks':
                $expr = new Zend_Db_Expr('SUM(clicks)');
            break;

            case 'orders':
                $expr = new Zend_Db_Expr('SUM(orders)');
            break;

            case 'revenue':
                $expr = new Zend_Db_Expr('SUM(revenue)');
            break;

            case 'cr':
                $expr = new Zend_Db_Expr('SUM(orders) / SUM(clicks) * 100');
            break;
        }

        $select = $connection->select();
        $select->from(array('ta' => $resource->getTableName('feedexport/performance_aggregated')), array($expr))
            ->where('ta.product_id = e.entity_id')
            ->where('ta.period >= ?', $date->toString('YYYY-MM-dd'));

        $select = $productCollection->getSelect()->columns(array($attribute => $select));


        return $this;
    }

    public function getInputType()
    {
        return 'string';
    }

    public function getValueElementType()
    {
        return 'text';
    }

    public function validate(Varien_Object $object)
    {
        $attrCode = $this->getAttribute();
        $value = $object->getData($attrCode);

        return $this->validateAttribute($value);
    }

    public function getJsFormObject()
    {
        return 'rule_conditions_fieldset';
    }
}
