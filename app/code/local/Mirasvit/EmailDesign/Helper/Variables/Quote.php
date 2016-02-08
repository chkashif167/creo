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



class Mirasvit_EmailDesign_Helper_Variables_Quote
{
    public function getQuote($parent, $args)
    {
        $quote = false;

        if ($parent->getData('quote')) {
            return $parent->getData('quote');
        } elseif ($parent->getData('quote_id')) {
            $quote = Mage::getModel('sales/quote')->setSharedStoreIds(array_keys(Mage::app()->getStores()))
                ->load($parent->getData('quote_id'));
        }

        $parent->setData('quote', $quote);

        return $quote;
    }

    public function getFirstQuoteItem($parent, $args)
    {
        $item = false;
        if ($parent->getData('first_quote_item')) {
            return $parent->getData('first_quote_item');
        } elseif ($quote = $this->getQuote($parent, $args)) {
            $itemsCollection = $quote->getItemsCollection()
                ->setOrder('item_id', 'ASC');
            foreach ($itemsCollection as $item) {
                if (!$item->isDeleted() && !$item->getParentItemId()) {
                    $item = $item;
                    break;
                }
            }
        }

        $parent->setData('first_quote_item', $item);

        return $item;
    }
}
