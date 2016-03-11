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


class Mirasvit_FeedExport_Block_Adminhtml_Feed_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');
        $form  = new Varien_Data_Form();
        $form->setFieldNameSuffix('feed');
        $this->setForm($form);

        $general = $form->addFieldset('general', array('legend' => __('General Information')));

        if ($model->getId()) {
            $general->addField('feed_id', 'hidden', array(
                'name'      => 'feed_id',
                'value'     => $model->getId(),
            ));
        }

        $general->addField('name', 'text', array(
            'label'    => __('Name'),
            'required' => true,
            'name'     => 'name',
            'value'    => $model->getName(),
        ));

        $general->addField('filename', 'text', array(
            'label'    => __('Filename'),
            'required' => true,
            'name'     => 'filename',
            'value'    => $model->getFilename(),
            'note'     => Mage::helper('feedexport/help')->field('filename')
        ));

        $general->addField('type', 'select', array(
            'label'    => __('File Type'),
            'required' => true,
            'name'     => 'type',
            'value'    => $model->getType(),
            'values'   => Mage::getSingleton('feedexport/system_config_source_type')->toOptionArray(),
            'onchange' => "FeedExportMapping.changeFormat(this);",
            'note'     => Mage::helper('feedexport/help')->field('type'),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $general->addField('store_id', 'select', array(
                'label'    => __('Store View'),
                'required' => true,
                'name'     => 'store_id',
                'value'    => $model->getStoreId(),
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
            ));
        } else {
            $general->addField('store_id', 'hidden', array(
                'name'  => 'store_id',
                'value' => Mage::app()->getStore(true)->getId()
            ));
        }

        $general->addField('is_active', 'select', array(
            'label'    => __('Is Active'),
            'required' => true,
            'name'     => 'is_active',
            'value'    => $model->getIsActive(),
            'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        if($model->getId() && $model->getUrl()) {
            $general->addField('access_url', 'note', array(
                'label'    => __('Feed Access Url'),
                'title'    => __('Feed Access Url'),
                'text'    => '<a href="'.$model->getUrl().'" target="_blank">'.$model->getUrl().'</a>',
            ));

            if ($model->getArchiveUrl()) {
                $general->addField('archive_access_url', 'note', array(
                    'label'    => __('Feed Access Url (archive)'),
                    'title'    => __('Feed Access Url (archive)'),
                    'text'    => '<a href="'.$model->getArchiveUrl().'" target="_blank">'.$model->getArchiveUrl().'</a>',
                ));
            }

            if ($model->getGeneratedAt()) {
                $general->addField('generated_at', 'note', array(
                    'label'    => __('Last Generated'),
                    'title'    => __('Last Generated'),
                    'text'     => Mage::getSingleton('core/date')->date('d.m.Y H:i', strtotime($model->getGeneratedAt())),
                ));

                $general->addField('generated_time', 'note', array(
                    'label'    => __('Generation Time'),
                    'title'    => __('Generation Time'),
                    'text'     => Mage::helper('feedexport')->timeSince($model->getGeneratedTime()),
                ));

                $general->addField('generated_cnt', 'note', array(
                    'label'    => __('Count Items'),
                    'title'    => __('Count Items'),
                    'text'    => $model->getGeneratedCnt(),
                ));
            }
        }
        Mage::dispatchEvent('adminhtml_promo_catalog_edit_tab_main_prepare_form', array('form' => $form));
        return parent::_prepareForm();
    }
}
