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


class Mirasvit_FeedExport_Block_Adminhtml_Feed_Edit_Tab_Ftp extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');
        $form  = new Varien_Data_Form();
        $form->setFieldNameSuffix('feed');
        $this->setForm($form);

        $general = $form->addFieldset('general', array('legend' => Mage::helper('feedexport')->__('FTP Settings')));

        $general->addField('ftp', 'select', array(
            'name'     => 'ftp',
            'label'    => Mage::helper('feedexport')->__('Enabled'),
            'required' => false,
            'values'   => Mage::getModel('adminhtml/system_config_source_enabledisable')->toOptionArray(),
            'value'    => $model->getFtp(),
        ));

        $general->addField('ftp_protocol', 'select', array(
            'name'     => 'ftp_protocol',
            'label'    => Mage::helper('feedexport')->__('Protocol'),
            'required' => true,
            'values'   => Mage::getModel('feedexport/system_config_source_ftpProtocol')->toOptionArray(),
            'value'    => $model->getFtpProtocol(),
        ));

        $general->addField('ftp_host', 'text', array(
            'name'     => 'ftp_host',
            'label'    => Mage::helper('feedexport')->__('Host Name'),
            'required' => false,
            'value'    => $model->getFtpHost(),
        ));
        $general->addField('ftp_user', 'text', array(
            'name'     => 'ftp_user',
            'label'    => Mage::helper('feedexport')->__('User Name'),
            'required' => false,
            'value'    => $model->getFtpUser(),
        ));
        $general->addField('ftp_password', 'password', array(
            'name'     => 'ftp_password',
            'label'    => Mage::helper('feedexport')->__('Password'),
            'required' => false,
            'value'    => $model->getFtpPassword(),
        ));
        $general->addField('ftp_path', 'text', array(
            'name'     => 'ftp_path',
            'label'    => Mage::helper('feedexport')->__('Path'),
            'required' => false,
            'value'    => $model->getFtpPath(),
        ));

        $general->addField('ftp_passive_mode', 'select', array(
            'name'     => 'ftp_passive_mode',
            'label'    => Mage::helper('feedexport')->__('Passive mode'),
            'required' => false,
            'values'   => Mage::getModel('adminhtml/system_config_source_enabledisable')->toOptionArray(),
            'value'    => $model->getFtpPassiveMode(),
        ));

        return parent::_prepareForm();
    }
}