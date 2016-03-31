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


class Mirasvit_MstCore_Model_Feed_Updates extends Mirasvit_MstCore_Model_Feed_Abstract
{
    public function check()
    {
        if (time() - intval(Mage::app()->loadCache(Mirasvit_MstCore_Helper_Config::UPDATES_FEED_URL)) > 12 * 60 * 60) {
            $this->refresh();
        }
    }

    public function refresh()
    {
        try {
            $params = array();
            $params['domain'] = Mage::getBaseUrl();
            foreach (Mage::getConfig()->getNode('modules')->children() as $name => $module) {
                $params['modules'][$name] = (string) $module->version;
            }

            Mage::app()->saveCache(time(), Mirasvit_MstCore_Helper_Config::UPDATES_FEED_URL);

            $xml = $this->getFeed(Mirasvit_MstCore_Helper_Config::UPDATES_FEED_URL, $params);

            $items = array();
            if ($xml) {
                foreach ($xml->xpath('channel/item') as $item) {
                    $items[] = array(
                        'title'       => (string) $item->title,
                        'description' => (string) Mage::helper('core/string')->truncate(strip_tags($item->description), 255),
                        'url'         => (string) $item->link,
                        'date_added'  => (string) $this->getDate($item->pubDate),
                        'severity'    => 3,
                    );
                }
            }

            if (Mage::getModel('adminnotification/inbox')) {
                Mage::getModel('adminnotification/inbox')->parse($items);
            }
        } catch (Exception $ex) {
            Mage::logException($ex);
        }

        return $this;
    }
}