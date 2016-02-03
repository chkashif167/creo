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


class Mirasvit_EmailDesign_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getTemplates()
    {
        $templates = array();

        $collection = Mage::getModel('emaildesign/template')->getCollection();
        foreach ($collection as $item) {
            $templates['emaildesign:'.$item->getId()] = $item->getTitle();
        }

        $arr = Mage::getResourceSingleton('core/email_template_collection')->toArray();
        foreach ($arr['items'] as $value) {
            $templates['email:'.$value['template_id']] = $value['template_code'];
        }

        $arr = Mage::getResourceModel('newsletter/template_collection')->load();
        foreach ($arr as $item) {
            $templates['newsletter:'.$item->getData('template_id')] = $item->getData('template_code');
        }

        return $templates;
    }

    public function styleHtml($html)
    {
        if (preg_match('/<style[^>]*>(?:<\!--)?(.*)(?:-->)?<\/style>/ims', $html, $match)) {
            $css = $match[1];

            $c2i = Mage::helper('emaildesign/cssToInline');
            $c2i->setHtml($html);
            $c2i->setCss($css);

            $html = $c2i->css2inline();
        }

        return $html;
    }
}