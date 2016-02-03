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
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Email_Block_Cross extends Mage_Catalog_Block_Product_List
{
    public function _toHtml()
    {
        $this->setArea('frontend');
        $this->setTemplate('mst_email/cross.phtml');

        return $this->renderView();
    }

    public function getItems()
    {
        return $this->_productCollection;
    }

    protected function _beforeToHtml()
    {
        $collection = $this->_getProductCollection();
        Mage::dispatchEvent('catalog_block_product_list_collection', array(
            'collection' => $this->_getProductCollection(),
        ));

        $this->_getProductCollection()->load();

        return parent::_beforeToHtml();
    }
}
