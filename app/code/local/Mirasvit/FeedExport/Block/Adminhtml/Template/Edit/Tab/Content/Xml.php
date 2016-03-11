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


class Mirasvit_FeedExport_Block_Adminhtml_Template_Edit_Tab_Content_Xml extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_model');
        $form  = new Varien_Data_Form();
        $form->setFieldNameSuffix('xml');
        $this->setForm($form);

        $content = $form->addFieldset('content_set', array('legend' => __('Content Settings')));

        $generateUrl = Mage::getUrl('feedexport/generate/runTest', array('id' => $model->getId()));

        $content->addField('format', 'textarea', array(
            'label'    => __('Feed Pattern'),
            'required' => false,
            'name'     => 'format',
            'value'    => $model->getXmlFormat(),
            'class'    => 'codemirror',
            'style'    => 'width: 500px;',
        ));

        $helperFieldset = $form->addFieldset('attribute_helper', array('legend' => __('Helper')));

        $helper = new Mirasvit_FeedExport_Block_Adminhtml_Template_Edit_Tab_Content_Renderer_AttributeHelper();
        $helperFieldset->setRenderer($helper);

        return parent::_prepareForm();
    }
}
