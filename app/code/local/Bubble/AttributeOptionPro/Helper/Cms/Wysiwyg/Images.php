<?php
/**
 * @category    Bubble
 * @package     Bubble_AttributeOptionPro
 * @version     1.1.4
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_AttributeOptionPro_Helper_Cms_Wysiwyg_Images extends Mage_Cms_Helper_Wysiwyg_Images
{
    public function isUsingStaticUrlsAllowed()
    {
        if (Mage::getSingleton('adminhtml/session')->getStaticUrlsAllowed()) {
            return true;
        }

        return parent::isUsingStaticUrlsAllowed();
    }
}