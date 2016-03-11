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



class Mirasvit_FeedExport_Model_Rule_Condition_Combine_Parent extends Mirasvit_FeedExport_Model_Rule_Condition_Combine
{
    const ATTR_CODE_PREFIX = '|parent';

    public function __construct()
    {
        parent::__construct();
        $this->setType('feedexport/rule_condition_combine_parent');
    }

    protected function _getProductAttributes()
    {
        $productCondition = Mage::getModel('feedexport/rule_condition_product_parent');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();

        foreach ($productAttributes as $code => $label) {
            $productAttributes[$code.self::ATTR_CODE_PREFIX] = $label;
            unset($productAttributes[$code]);
        }

        return $productAttributes;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml().
            Mage::helper('feedexport')->__('If %s of these conditions are %s for <b>parent product</b>:', $this->getAggregatorElement()->getHtml(), $this->getValueElement()->getHtml());
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }

        return $html;
    }

    public function asString($format = '')
    {
        $str = Mage::helper('rule')->__('If %s of these conditions are %s for parent product:', $this->getAggregatorName(), $this->getValueName());

        return $str;
    }
}
