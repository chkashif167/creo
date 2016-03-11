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


class Mirasvit_FeedExport_Block_Adminhtml_Template_Edit_Tab_Content_Renderer_AttributeHelper
    implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
         return Mage::app()->getLayout()
            ->createBlock('adminhtml/template')
            ->setTemplate('mirasvit/feedexport/renderer/attribute_helper.phtml')
            ->toHtml();
    }
}