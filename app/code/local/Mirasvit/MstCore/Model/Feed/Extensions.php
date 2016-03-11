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


class Mirasvit_MstCore_Model_Feed_Extensions extends Mirasvit_MstCore_Model_Feed_Abstract
{
    public function getList()
    {
        try {
            $xml   = $this->getFeed(Mirasvit_MstCore_Helper_Config::EXTENSIONS_FEED_URL);
            $items = array();

            if ($xml) {
                foreach ($xml->xpath('extension') as $item) {
                    $items[(string) $item->sku] = array(
                        'name'     => (string) $item->name,
                        'sku'      => (string) $item->sku,
                        'version'  => (string) $item->version,
                        'revision' => (string) $item->revision,
                        'url'      => (string) $item->url,
                    );
                }
            }

            return $items;
        } catch (Exception $ex) {
            Mage::logException($ex);
        }

        return false;
    }
}