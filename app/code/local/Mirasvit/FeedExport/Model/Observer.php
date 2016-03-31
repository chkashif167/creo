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



class Mirasvit_FeedExport_Model_Observer
{
    public function generate()
    {
        $collection = Mage::getModel('feedexport/feed')->getCollection()
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('cron', 1);

        foreach ($collection as $feed) {
            $feed = $feed->load($feed->getId());
            $canRunCron = $feed->canRunCron();
            if ($canRunCron !== false) {
                Mage::helper('feedexport')->addToHistory($feed, 'Cron scheduler at '.($canRunCron / 60));

                $feed->generateCli();
                $feed->delivery();
            } else {
                Mage::helper('feedexport')->addToHistory($feed, 'Skip cron scheduler');
            }
        }

        return $this;
    }

    public function cleanHistory()
    {
        $date = new Zend_Date();
        $date->subDay(3);

        $collection = Mage::getModel('feedexport/feed_history')->getCollection()
            ->addFieldToFilter('created_at', array('lt' => $date->toString('Y-MM-dd H:mm:s')));

        foreach ($collection as $entry) {
            $entry->delete();
        }

        return $this;
    }

    public function onAdminhtmlCatalogProductGridPrepareMassaction($observer)
    {
        $block = $observer->getBlock();

        $feeds = array();
        $collection = Mage::getModel('feedexport/feed')->getCollection()
            ->setOrder('name');
        foreach ($collection as $feed) {
            $feeds[] = array(
                'label' => $feed->getName(),
                'value' => $feed->getId(),
            );
        }
        $block->getMassactionBlock()->addItem('feedexport_export', array(
            'label' => __('Export Products'),
            'url' => $block->getUrl('adminhtml/feedexport_feed/massProductsExport', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'feed_id',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => __('Feed'),
                    'values' => $feeds,
                ),
            ),
        ));
    }
}
