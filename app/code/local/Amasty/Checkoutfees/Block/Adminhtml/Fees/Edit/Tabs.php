<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */

/**
 * admin product edit tabs
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Amasty_Checkoutfees_Block_Adminhtml_Fees_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('feesTabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('amcheckoutfees')->__('Customer Group Catalog / Rules'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
                'label'   => Mage::helper('amcheckoutfees')->__('General'),
                'content' => $this->getLayout()->createBlock('amcheckoutfees/adminhtml_fees_edit_tab_general')
                                  ->setTitle('General Settings')
                                  ->toHtml(),
            )
        );

        $this->addTab('restriction', array(
                'label' => Mage::helper('amcheckoutfees')->__('Fee Options'),
                'content' => $this->getLayout()->createBlock('amcheckoutfees/adminhtml_fees_edit_tab_options')
                                  ->setTitle('Restriction Action')
                                  ->toHtml(),
            )
        );
        $this->addTab('conditions', array(
                'label'   => Mage::helper('amcheckoutfees')->__('Conditions'),
                'content' => $this->getLayout()->createBlock('amcheckoutfees/adminhtml_fees_edit_tab_conditions')
                                  ->setTitle('Conditions')
                                  ->toHtml(),
            )
        );

        return parent::_beforeToHtml();
    }
}
