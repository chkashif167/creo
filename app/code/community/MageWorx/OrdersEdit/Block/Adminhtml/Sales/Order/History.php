<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Block_Adminhtml_Sales_Order_History extends Mage_Adminhtml_Block_Sales_Order_View_History
{
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $onclick = "submitHistoryAndReload($('order_history_block').parentNode, '" . $this->getSubmitUrl() . "')";

        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('sales')->__('Submit Comment'),
                'class' => 'save',
                'onclick' => $onclick
            ));
        $this->setChild('submit_button', $button);
        return $this;
    }

    /**
     * Get statuses for comment form
     * All available or just for current state
     *
     * @return array
     */
    public function getStatuses()
    {
        $helper = Mage::helper('mageworx_ordersedit');
        if ($helper->isNeedToShowAllStates()) {
            return $this->getOrder()->getConfig()->getStatuses();
        } else {
            return $this->getOrder()->getConfig()->getStateStatuses($this->getOrder()->getState());
        }
    }

    /**
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('adminhtml/mageworx_ordersedit_history/addComment', array('order_id' => $this->getOrder()->getId()));
    }

    /**
     * @return string
     */
    public function getSubmitEditUrl()
    {
        return $this->getUrl('adminhtml/mageworx_ordersedit_history/saveEditComment', array('order_id' => $this->getOrder()->getId()));
    }
}   