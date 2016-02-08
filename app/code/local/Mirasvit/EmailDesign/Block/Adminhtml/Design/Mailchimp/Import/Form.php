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


class Mirasvit_EmailDesign_Block_Adminhtml_Design_Mailchimp_Import_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'method'  => 'post',
            'action'  => $this->getUrl('*/*/doimportMailchimp'),
            'enctype' => 'multipart/form-data',
        ));

        $general = $form->addFieldset('general', array('legend' => Mage::helper('emaildesign')->__('Import Information')));
        
        $general->addField('import', 'hidden', array(
            'name'  => 'import',
            'value' => 1,
        ));

        $general->addField('design', 'multiselect', array(
            'name'     => 'design',
            'label'    => Mage::helper('emaildesign')->__('Designs'),
            'required' => true,
            'values'   => Mage::getSingleton('emaildesign/system_source_designMailchimp')->toOptionArray(),
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}