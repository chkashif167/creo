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


class Mirasvit_FeedExport_Block_Adminhtml_Rule_New_Created extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mirasvit/feedexport/rule/new/created.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'rule',
            $this->getLayout()->createBlock('feedexport/adminhtml_rule_new_rule')
                ->setRule($this->_getRule())
        );

        $this->setChild(
            'close_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'   => __('Close Window'),
                    'onclick' => 'addRule(true)'
                ))
        );

    }

    protected function _getRule()
    {
        $ruleId = $this->getRequest()->getParam('rule');

        return Mage::getModel('feedexport/rule')->load($ruleId);
    }

    public function getCloseButtonHtml()
    {
        return $this->getChildHtml('close_button');
    }

    public function getRuleBlockJson()
    {
        $result = array(
            'group' => $this->getRequest()->getParam('group'),
            'rule'  => $this->getChildHtml('rule')
        );

        return Mage::helper('core')->jsonEncode($result);
    }
}
