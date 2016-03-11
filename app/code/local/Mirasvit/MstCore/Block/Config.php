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


class Mirasvit_MstCore_Block_Config extends Mage_Adminhtml_Block_Template
{
    protected function _prepareLayout()
    {
        $this->_section = $this->getAction()->getRequest()->getParam('section', false);

        parent::_prepareLayout();
    }

    protected function _toHtml()
    {
        if ($this->_section == 'mstcore_store') {
            return parent::_toHtml();
        } else {
            return '';
        }
    }

    public function getStoreHtml()
    {
        $url = Mirasvit_MstCore_Helper_Config::STORE_URL;

        $html = Mage::app()->loadCache($url);

        if (!$html) {
            $html = $this->_loadUrl($url);
            Mage::app()->saveCache($html, $url);
        }

        return $html;
    }

    protected function _loadUrl($url)
    {
        $curl = new Varien_Http_Adapter_Curl();
        $curl->setConfig(array('timeout' => 30));
        $curl->write(Zend_Http_Client::GET, $url, '1.0');

        $text = $curl->read();
        $text = preg_split('/^\r?$/m', $text, 2);
        $text = trim($text[1]);

        return $text;
    }
}