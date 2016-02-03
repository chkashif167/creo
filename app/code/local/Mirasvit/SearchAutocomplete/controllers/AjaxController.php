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



/**
 * @category Mirasvit
 */
class Mirasvit_Searchautocomplete_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function getAction()
    {
        $this->loadLayout();
        $query = Mage::helper('catalogsearch')->getQuery();
        $query->setStoreId(Mage::app()->getStore()->getId());

        $result = array();

        if ($query->getQueryText()) {
            if (Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->setId(0)
                    ->setIsActive(1)
                    ->setIsProcessed(1);
            } else {
                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity() + 1);
                } else {
                    $query->setPopularity(1);
                }
                $query->prepare();
            }

            $resultBlock = $this->getLayout()->createBlock('searchautocomplete/result');

            if ($this->getRequest()->getParam('cat')) {
                $resultBlock->setCategoryId(intval($this->getRequest()->getParam('cat')));
            }

            if ($this->getRequest()->getParam('index')) {
                $resultBlock->setIndexFilter($this->getRequest()->getParam('index'));
            }

            $resultBlock->init();

            $result['items'] = $resultBlock->toHtml();

            $result['items'] = str_replace('?___SID=U&', '?', $result['items']);
            $result['items'] = str_replace('?___SID=U', '', $result['items']);

            $result['success'] = true;
            $result['query'] = $query->getQueryText();

            Mage::helper('catalogsearch')->getQuery()->save();
        } else {
            $result['success'] = false;
        }

        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(Mage::helper('core')->jsonEncode($result));
    }
}
