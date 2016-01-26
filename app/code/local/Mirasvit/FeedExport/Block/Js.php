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
 * @version   1.1.2
 * @build     616
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_Block_Js extends Mage_Core_Block_Template
{
    public function _toHtml()
    {
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'mirasvit/code/feedexport/performance.js';
        $baseUrl = Mage::getBaseUrl();

        return '<script>var FEED_BASE_URL="'.$baseUrl.'";</script><script src="'.$url.'" type="text/javascript"></script>';
    }
}