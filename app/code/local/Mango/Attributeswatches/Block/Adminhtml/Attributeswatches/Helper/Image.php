<?php

/**
 * Category form image field helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mango_Attributeswatches_Block_Adminhtml_Attributeswatches_Helper_Image extends Varien_Data_Form_Element_Image
{
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = Mage::getBaseUrl('media').'attributeswatches/'. $this->getValue();
        }
        return $url;
    }
}
