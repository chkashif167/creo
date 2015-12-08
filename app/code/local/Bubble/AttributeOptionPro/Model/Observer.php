<?php
/**
 * @category    Bubble
 * @package     Bubble_AttributeOptionPro
 * @version     1.1.4
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_AttributeOptionPro_Model_Observer
{
    /**
     * Updates the layout.
     *
     * @return  void
     */
    public function updateLayout()
    {
        $layout = Mage::getSingleton('core/layout');

        $head = $layout->getBlock('head');
        $head->setCanLoadExtJs(true)
             ->addJs('mage/adminhtml/variables.js')
             ->addJs('mage/adminhtml/wysiwyg/widget.js')
             ->addJs('lib/flex.js')
             ->addJs('lib/FABridge.js')
             ->addJs('mage/adminhtml/flexuploader.js')
             ->addJs('mage/adminhtml/browser.js')
             ->addJs('prototype/window.js')
             ->addItem('js_css', 'prototype/windows/themes/default.css');

        // Less than 1.7
        if (version_compare(Mage::getVersion(), '1.7.0.0', '<')) {
            $head->addItem('js_css', 'prototype/windows/themes/magento.css');
        } else {
            $head->addCss('lib/prototype/windows/themes/magento.css');
        }
    }

    /**
     * Add an image field for the attribute
     *
     * @param Varien_Event_Observer $observer
     */
    public function eavAttributeEditFormInit(Varien_Event_Observer $observer)
    {
        $form = $observer->getEvent()->getForm();
        $fieldset = $form->getElement('front_fieldset');

        $browserUrl = Mage::getSingleton('adminhtml/url')->getUrl('*/cms_wysiwyg_images/index', array(
            'static_urls_allowed'   => 1,
            'target_element_id'     => 'attribute_image'
        ));
        $afterElementHtml = <<<HTML
<button id="add_attribute_image" style="vertical-align:middle;" class="scalable" type="button"
        onclick="MediabrowserUtility.openDialog('{$browserUrl}');"><span><span>...</span></span></button>
HTML;
        $fieldset->addField('image', 'text', array(
            'name'                  => 'image',
            'note'                  => Mage::helper('bubble_aop')->__(
                'Associate an image to this attribute that you can display anywhere in frontend'
            ),
            'label'                 => Mage::helper('bubble_aop')->__('Attribute Image'),
            'style'                 => 'width:245px;',
            'after_element_html'    => $afterElementHtml,
        ))->setId('attribute_image');
    }
}
