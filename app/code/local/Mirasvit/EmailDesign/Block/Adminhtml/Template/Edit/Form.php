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


class Mirasvit_EmailDesign_Block_Adminhtml_Template_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id'      => 'edit_form',
                'action'  => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method'  => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $model = Mage::registry('current_model');

        $general = $form->addFieldset('general', array('legend' => Mage::helper('emaildesign')->__('General Information'), 'class' => 'fieldset-wide'));
        
        if ($model->getId()) {
            $general->addField('template_id', 'hidden', array(
                'name'  => 'template_id',
                'value' => $model->getId(),
            ));
        }
    
        $general->addField('title', 'text', array(
            'label'    => Mage::helper('emaildesign')->__('Name'),
            'required' => true,
            'name'     => 'title',
            'value'    => $model->getTitle()
        ));

        $general->addField('description', 'textarea', array(
            'label'    => Mage::helper('emaildesign')->__('Description'),
            'required' => false,
            'name'     => 'description',
            'value'    => $model->getDescription(),
            'style'    => 'height: 40px;',
        ));

        $general->addField('design_id', 'select', array(
            'label'    => Mage::helper('emaildesign')->__('Design'),
            'required' => false,
            'name'     => 'design_id',
            'value'    => $model->getDesignId(),
            'values'   => Mage::getModeL('emaildesign/design')->getCollection()->setOrder('title', 'asc')->toOptionArray(),
        ));

        $general->addField('subject', 'text', array(
            'label'    => Mage::helper('emaildesign')->__('Subject'),
            'required' => true,
            'name'     => 'subject',
            'value'    => $model->getSubject()
        ));

        if (Mage::registry('current_model')->getId() > 0) {
            $htmlEditor = $this->getLayout()->createBlock('adminhtml/template')
                ->setTemplate('mst_emaildesign/template/editor.phtml')
                ->setModel($model);
            
            $general->addField('editor', 'note', array(
                'text'  => $htmlEditor->toHtml(),
            ));
        }

        return parent::_prepareForm();
    }
}