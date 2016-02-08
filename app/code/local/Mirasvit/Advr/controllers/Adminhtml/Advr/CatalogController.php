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



class Mirasvit_Advr_Adminhtml_Advr_CatalogController extends Mirasvit_Advr_Controller_Report
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_title($this->__('Advanced Reports'))
            ->_title($this->__('Catalog'));

        parent::_initAction();

        return $this;
    }

    public function productAction()
    {
        $this->_initAction()
            ->_title($this->__('Products / Bestsellers'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_catalog_product'))
            ->_processActions()
            ->renderLayout();
    }

    public function productDetailAction()
    {
        $this->_initAction()
            ->_title($this->__('Product Detail'));

        if ($id = $this->getRequest()->getParam('id')) {
            $product = Mage::getModel('catalog/product')->load($id);
            Mage::register('current_product', $product);
        }

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_catalog_product_detail'))
            ->_processActions()
            ->renderLayout();
    }

    public function attributeAction()
    {
        $this->_initAction()
            ->_title($this->__('Sales by Attribute'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_catalog_attribute'))
            ->_processActions()
            ->renderLayout();
    }

    public function attributeDetailAction()
    {
        $this->_initAction()
            ->_title($this->__('Attribute Detail'));

        if ($attrCode = $this->getRequest()->getParam('attribute_code')) {
            $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attrCode);
            Mage::register('current_attribute', $attribute);

            $value = $this->getRequest()->getParam('attribute_value');
            Mage::register('current_attribute_value', $value);

            $option = null;
            $options = $attribute->getSource()->getAllOptions(false);
            foreach ($options as $opt) {
                if ($opt['value'] == $value) {
                    $option = $opt['label'];
                }
            }

            Mage::register('current_attribute_option', $option);
        }

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_catalog_attribute_detail'))
            ->_processActions()
            ->renderLayout();
    }

    public function attributesetAction()
    {
        $this->_initAction()
            ->_title($this->__('Sales by Attribute Set'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_catalog_attributeset'))
            ->_processActions()
            ->renderLayout();
    }

    public function attributesetDetailAction()
    {
        $this->_initAction()
            ->_title($this->__('Attribute Set Detail'));

        if ($setId = $this->getRequest()->getParam('attribute_set_id')) {
            $set = Mage::getModel('eav/entity_attribute_set')->load($setId);
            Mage::register('current_attribute_set', $set);
        }

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_catalog_attributeset_detail'))
            ->_processActions()
            ->renderLayout();
    }

    public function lowstockAction()
    {
        $this->_initAction()
            ->_title($this->__('Low stock'));

        $this->_addContent($this->getLayout()->createBlock('advr/adminhtml_catalog_lowstock'))
            ->_processActions()
            ->renderLayout();
    }

    protected function _isAllowed()
    {
        return (bool)Mage::getSingleton('admin/session')->isAllowed('advr/catalog')
            || Mage::getSingleton('admin/session')->isAllowed('report/advr/catalog');
    }
}
