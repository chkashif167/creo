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
 * @package   Sphinx Search Ultimate
 * @version   2.3.2
 * @build     1290
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_SearchLandingPage_Block_Adminhtml_Page_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ));

        $general = $form->addFieldset('general', array('legend' => Mage::helper('searchlandingpage')->__('General Information')));

        if ($model->getId()) {
            $general->addField('page_id', 'hidden', array(
                'name' => 'page_id',
                'value' => $model->getId(),
            ));
        }

        $general->addField('query_text', 'text', array(
            'name' => 'query_text',
            'label' => Mage::helper('searchlandingpage')->__('Search Phrase'),
            'required' => true,
            'value' => $model->getQueryText(),
        ));

        $general->addField('url_key', 'text', array(
            'name' => 'url_key',
            'label' => Mage::helper('searchlandingpage')->__('Landing Url'),
            'required' => true,
            'value' => $model->getUrlKey(),
            'note' => 'ex. phones/samsung-phone',
        ));

        $general->addField('is_active', 'select', array(
            'name' => 'is_active',
            'label' => Mage::helper('searchlandingpage')->__('Active'),
            'required' => true,
            'value' => $model->getIsActive(),
            'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $general->addField('store_id', 'multiselect', array(
                'name' => 'store_ids[]',
                'label' => Mage::helper('searchlandingpage')->__('Store View'),
                'title' => Mage::helper('searchlandingpage')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'value' => $model->getStoreIds(),
            ));
        } else {
            $general->addField('store_id', 'hidden', array(
                'name' => 'store_ids',
                'value' => Mage::app()->getStore(true)->getId(),
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $general->addField('title', 'text', array(
            'name' => 'title',
            'label' => Mage::helper('searchlandingpage')->__('Title'),
            'required' => true,
            'value' => $model->getTitle(),
        ));

        $general->addField('meta_title', 'text', array(
            'name' => 'meta_title',
            'label' => Mage::helper('searchlandingpage')->__('Meta Title'),
            'required' => false,
            'value' => $model->getMetaTitle(),
        ));

        $general->addField('meta_keywords', 'text', array(
            'name' => 'meta_keywords',
            'label' => Mage::helper('searchlandingpage')->__('Meta Keywords'),
            'required' => false,
            'value' => $model->getMetaKeywords(),
        ));

        $general->addField('meta_description', 'text', array(
            'name' => 'meta_description',
            'label' => Mage::helper('searchlandingpage')->__('Meta Description'),
            'required' => false,
            'value' => $model->getMetaDescription(),
        ));

        $general->addField('layout', 'textarea', array(
            'name' => 'layout',
            'label' => Mage::helper('searchlandingpage')->__('Layout'),
            'required' => false,
            'value' => $model->getLayout(),
            'style' => 'width: 700px;',
        ));

        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
