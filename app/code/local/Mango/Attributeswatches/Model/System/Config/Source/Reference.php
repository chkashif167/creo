<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mango_Loworderfee_Model_System_Config_Source_Reference
{
    const  BaseSubtotalWithDiscount = "BaseSubtotalWithDiscount";
    const  BaseSubtotal = "BaseSubtotal";
    const  SubtotalInclTax = "SubtotalInclTax";
    const  SubtotalInclTaxWithDiscount = "SubtotalInclTaxWithDiscount";    
    
    
    public function toOptionArray()
    {
        $options = array(); 
        
        ///print_r(Mage::getModel('tax/class_source_product')->toOptionArray());
        
        $options[] = array('value'=>'BaseSubtotalWithDiscount', 'label' => Mage::helper('adminhtml')->__('Base Subtotal With Discount (Magento Default)'));
        $options[] = array('value'=>'BaseSubtotal', 'label' => Mage::helper('adminhtml')->__('Base Subtotal'));
        $options[] = array('value'=>'SubtotalInclTax', 'label' => Mage::helper('adminhtml')->__('Subtotal Incl. Tax'));
        $options[] = array('value'=>'SubtotalInclTaxWithDiscount', 'label' => Mage::helper('adminhtml')->__('Subtotal Incl. Tax With Discount'));        
        
        //array_unshift($options, array('value'=>'', 'label' => Mage::helper('tax')->__('None')));
        return $options;
    }

}
