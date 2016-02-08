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
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     539
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Advr_IndexController extends Mage_Core_Controller_Front_Action
{
    public function geoExportAction()
    {
        $page = intval($this->getRequest()->getParam('page'));

        $collection = Mage::getModel('advr/postcode')->getCollection()
            ->addFieldToFilter('updated', 1)
            ->setPageSize(100)
            ->setCurPage($page);

        echo $collection->getSize().'<br>';
        
        foreach ($collection as $postcode) {
            echo $postcode->getCountryId()
                .';'.$postcode->getPostcode()
                .';'.$postcode->getPlace()
                .';'.$postcode->getState()
                .';'.$postcode->getProvince()
                .';'.$postcode->getCommunity()
                .';'.$postcode->getLat()
                .';'.$postcode->getLng()
                .'<br>';
        }
    }

    public function geoUpdateAction()
    {
        Mage::getSingleton('advr/postcode')->copyUnknown(true);
        Mage::getSingleton('advr/postcode')->batchUpdate(true);
        Mage::getSingleton('advr/postcode')->batchMerge(true);
    }
}
