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



class Mirasvit_Advr_Block_Adminhtml_Geo_Import_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'method'  => 'post',
            'action'  => $this->getUrl('*/*/processImport'),
            'enctype' => 'multipart/form-data',
        ));

        $general = $form->addFieldset('general', array('legend' => Mage::helper('advr')->__('Import Information')));

        $general->addField('import', 'hidden', array(
            'name'  => 'import',
            'value' => 1,
        ));

        $general->addField('files', 'multiselect', array(
            'name'     => 'files',
            'label'    => Mage::helper('advr')->__('Files to import'),
            'required' => true,
            'values'   => Mage::getSingleton('advr/system_config_source_geoImportFile')->toOptionArray(),
        ));

        $general->addField('unknown', 'label', array(
            'name'  => 'files',
            'label' => Mage::helper('advr')->__('Number of unknown postal codes'),
            'value' => Mage::getSingleton('advr/postcode')->getNumberOfUnknown(),
            'note'  => Mage::helper('advr')->__('Every hour, extension will fetch information for 100 postal codes')
        ));

        $form->setUseContainer(true);
        $this->setForm($form);


        return parent::_prepareForm();
    }
}
