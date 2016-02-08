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


class Mirasvit_SearchAutocomplete_Model_Observer
{
    public function onReindexBeforeSave($observer)
    {
        $data      = $observer->getData('data');

        $indexModel = $observer->getData('model');
        $entity     = $observer->getData('entity');
        $storeId    = $observer->getData('store_id');
        $pk         = $indexModel->getPrimaryKey();
        $template   = 'mst_searchautocomplete/autocomplete/index/'.str_replace('_', '/', $indexModel->getCode()).'.phtml';

        $appEmulation           = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId, 'frontend');

        $arData = $data->getData('data');
        // pr($arData);
        $ids = array(0);
        $kids = array();
        foreach ($arData as $key => $itm) {
            $ids[] = $itm[$pk];
            $kids[$itm[$pk]] = $key;
        }

        $itemModel = $indexModel->getItemModel();

        if ($indexModel instanceof Mirasvit_SearchIndex_Model_Index_Mage_Catalog_Product_Index) {
            $collection = $itemModel->getCollection()
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addFieldToFilter('entity_id', $ids)
                ->setStore(Mage::app()->getStore())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addStoreFilter()
                ->addUrlRewrite()
                ;

            foreach ($collection as $item) {
                $block = Mage::app()->getLayout()->createBlock('searchautocomplete/result')
                    ->setTemplate($template)
                    ->setItem($item);

                $html = $block->toHtml();

                $arData[$kids[$item->getId()]]['data'] = $html;
            }

            $data->setData('data', $arData);
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            return;
        } elseif ($itemModel) {

            foreach ($arData as $key => $itm) {
                $item = $itemModel->load($itm[$pk]);
                $block = Mage::app()->getLayout()->createBlock('searchautocomplete/result')
                    ->setTemplate($template)
                    ->setItem($item);

                $html = $block->toHtml();

                $arData[$key]['data'] = $html;
            }

            $data->setData('data', $arData);
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            return;

        } else {
             foreach ($arData as $key => $itm) {
                $item = new Varien_Object($itm);
                $block = Mage::app()->getLayout()->createBlock('searchautocomplete/result')
                    ->setTemplate($template)
                    ->setItem($item);

                $html = $block->toHtml();

                $arData[$key]['data'] = $html;
            }

            $data->setData('data', $arData);
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            return;
        }
    }
}