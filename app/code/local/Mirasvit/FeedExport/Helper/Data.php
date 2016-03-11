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


class Mirasvit_FeedExport_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_defaultStore = null;

    public function getMaxAllowedTime()
    {
        $time = intval(ini_get('max_execution_time'));

        if ($time < 1 || $time > 30) {
            $time = 30;
        }

        return $time;
    }

    public function getMaxAllowedMemory()
    {
        return 220 * 1024 * 1024;
    }

    public function addToHistory($feed, $title, $message = null, $type = null)
    {
        $feed->addToHistory($title, $message, $type);

        return $this;
    }

    public function setCurrentStore($storeId)
    {
        if ($this->_defaultStore == null) {
            $this->_defaultStore = Mage::app()->getStore()->getId();
        }

        Mage::app()->setCurrentStore($storeId);

        return $this;
    }

    public function resetCurrentStore()
    {
        Mage::app()->setCurrentStore($this->_defaultStore);
    }

    /**
     * Возвращает время прошедшее с момента $time
     * формам x years x months x days x hours x min x sec
     *
     * @param  integer $time timestamp с какого момента
     *
     * @return string
     */
    public function timeSince($time)
    {
        if ($time > 30 * 24 * 60 * 60) {
            return '';
        }

        $time = abs($time);
        $print = '';
        $chunks = array(
            array(60 * 60 * 24 * 365 , 'year'),
            array(60 * 60 * 24 * 30 , 'month'),
            array(60 * 60 * 24 , 'day'),
            array(60 * 60 , 'hour'),
            array(60 , 'min'),
            array(1 , 'sec')
        );

        for ($i = 0; $i < count($chunks); $i++) {
            $seconds = $chunks[$i][0];
            $name    = $chunks[$i][1];

            if (($count = floor($time / $seconds)) != 0) {
                $print .= $count.' ';
                $print .= $name;
                $print .= ' ';

                $time -= $count * $seconds;
            }
        }

        if ($print == '') {
            $print = '0 seconds';
        }

        return $print;
    }

    public function getState()
    {
        return Mage::registry('current_state');
    }

    public function getProductUrl($product, $storeId)
    {
        $isSeoFormattedUrl = false;
        $productUrl        = null;
        if (Mage::helper('mstcore')->isModuleInstalled('Mirasvit_Seo')) {
            $urlFormat = Mage::getSingleton('seo/config')->getProductUrlFormat();
            if ($urlFormat == Mirasvit_Seo_Model_Config::URL_FORMAT_LONG) {
                $isSeoFormattedUrl = true;
            }
        }

        if ($isSeoFormattedUrl) {
            $longUrl = array();
            $urlRewriteCollection = Mage::getModel('core/url_rewrite')->getCollection()
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('is_system', 1)
                ->addFieldToFilter('product_id', $product->getId())
                ->setOrder('category_id', 'desc');

            foreach ($urlRewriteCollection as $url) {
                $longUrl[strlen($url->getRequestPath())] = $url->getRequestPath();
            }

            $productUrl = Mage::getBaseUrl() . $longUrl[max(array_keys($longUrl))];
        } else {
            $productUrl = $product->getProductUrl(false);
        }

        return $productUrl;
    }
}