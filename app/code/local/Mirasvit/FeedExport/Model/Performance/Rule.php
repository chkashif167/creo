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
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_Model_Tracker_Rule extends Mage_Rule_Model_Abstract
{
    protected $_productIds;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('feedexport/tracker_rule');
        $this->setIdFieldName('tracker_rule_id');
    }

    public function getConditionsInstance()
    {
        return Mage::getModel('feedexport/tracker_rule_condition_combine');
    }

    public function getActionsInstance()
    {
        return Mage::getModel('feedexport/tracker_rule_action_collection');
    }

    public function updateRuleProductData()
    {
        $this->_getResource()->updateRuleProductData($this);
    }

    protected function _afterSave()
    {
        parent::_afterSave();
    }

    protected function _resetConditions($conditions = null)
    {
        if (is_null($conditions)) {
            $conditions = $this->getConditionsInstance();
        }
        $conditions->setRule($this)->setId('1')->setPrefix('trackerconditions');
        $this->setConditions($conditions);

        return $this;
    }

    protected function _resetActions($actions = null)
    {
        if (is_null($actions)) {
            $actions = $this->getActionsInstance();
        }
        $actions->setRule($this)->setId('1')->setPrefix('trackeractions');
        $this->setActions($actions);

        return $this;
    }

    public function getMatchingProductIds()
    {
        if (is_null($this->_productIds)) {
            $this->_productIds = array();
            $this->setCollectedAttributes(array());

            $productCollection = Mage::getResourceModel('catalog/product_collection');

            $this->getConditions()->collectValidatedAttributes($productCollection);

            Mage::getSingleton('core/resource_iterator')->walk(
                $productCollection->getSelect(),
                array(array($this, 'callbackValidateProduct')),
                array(
                    'attributes' => $this->getCollectedAttributes(),
                    'product'    => Mage::getModel('catalog/product'),
                )
            );
        }

        return $this->_productIds;
    }

    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
        }
    }

    public function getProductIds()
    {
        return $this->_getResource()->getRuleProductIds($this->getId());
    }
}