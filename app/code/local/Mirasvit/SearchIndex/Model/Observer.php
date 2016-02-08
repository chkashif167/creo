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
 * @package   Sphinx Search Ultimate
 * @version   2.3.2
 * @build     1290
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_SearchIndex_Model_Observer
{
    /**
     * После завершения реиндекса индекса catalogsearc_fulltext (System/Index Management)
     * переводим все наши индексы в статус Ready.
     *
     * @return object
     */
    public function onIndexProcessComplete()
    {
        $collection = Mage::getModel('searchindex/index')->getCollection();
        foreach ($collection as $index) {
            $index->setStatus(1)
                ->save();
        }

        return $this;
    }

    public function onNoRoute(Varien_Event_Observer $observer)
    {
        if (Mage::getSingleton('searchindex/config')->isSearchOn404Enabled()) {
            Mage::helper('searchindex/route')->process($observer->getEvent()->getControllerAction());
        }

        return $this;
    }
}
