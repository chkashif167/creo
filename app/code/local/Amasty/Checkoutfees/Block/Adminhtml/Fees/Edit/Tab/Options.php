<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */

/**
 * customers defined options
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Amasty_Checkoutfees_Block_Adminhtml_Fees_Edit_Tab_Options extends Mage_Adminhtml_Block_Widget
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $fees_id  = $this->getRequest()->getParam('id');
        $feesData = Mage::getModel('amcheckoutfees/feesData')->getCollection()->addFieldToFilter('fees_id', array('eq' => $fees_id))->setOrder('sort', 'ASC');
        $stores = Mage::app()->getStores();
        $this->setTemplate('amasty/amcheckoutfees/adminhtml/catalog/product/edit/options.phtml');
        if ($feesData->count()) {
            $this->setFeesId($fees_id);
            $this->setFeesOptions($feesData);
        }
        $this->setStores($stores);
    }

}
