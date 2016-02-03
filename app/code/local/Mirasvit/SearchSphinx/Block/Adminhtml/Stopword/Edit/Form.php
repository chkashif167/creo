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



class Mirasvit_SearchSphinx_Block_Adminhtml_Stopword_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
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

        $general = $form->addFieldset('general', array('legend' => Mage::helper('searchsphinx')->__('General Information')));

        if ($model->getId()) {
            $general->addField('stopword_id', 'hidden', array(
                'name' => 'stopword_id',
                'value' => $model->getId(),
            ));
        }

        $general->addField('word', 'text', array(
            'name' => 'word',
            'label' => Mage::helper('searchsphinx')->__('Stopword'),
            'required' => true,
            'value' => $model->getWord(),
        ));

        $general->addField('store', 'select', array(
            'label' => Mage::helper('searchsphinx')->__('Store View'),
            'required' => true,
            'name' => 'store',
            'value' => $model->getStore(),
            'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, false),
        ));

        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
