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


class Mirasvit_Advd_Block_Adminhtml_Widget_Question_Metric extends Mirasvit_Advd_Block_Adminhtml_Widget_Abstract_Metric
{
    public function isEnabled()
    {
        return Mage::helper('core')->isModuleEnabled('Mirasvit_ProductQuestion');
    }

    public function getGroup()
    {
        return 'Product Questions';
    }

    public function getName()
    {
        return 'Number of unanswered questions';
    }
    
    public function prepareOptions()
    {
        return $this;
    }

    public function getMetricValue()
    {
        $cnt = 0;
        foreach ($this->_getCollection() as $item) {
            if ($item->getChild() == 0) {
                $cnt++;
            }
        }

        return $cnt;
    }

    public function getMetricValueToCompare()
    {
        return false;
    }

    public function formatMetricValue($value)
    {
        return $value;
    }

    public function _getCollection()
    {
        $collection = Mage::getModel('productquestion/question')->getCollection()
            ->addColumnNumberChildren();

        return $collection;
    }
}
