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


class Mirasvit_EmailDesign_Helper_Variables_Store
{
    public function getStore($parent, $args)
    {
        $store = false;

        if ($parent->getData('store')) {
            return $parent->getData('store');
        } elseif ($parent->getData('store_id')) {
            $store = Mage::getModel('core/store')->load($parent->getData('store_id'));
        } else {
            $store = $defaultStoreId = Mage::app()->getWebsite(true)
                ->getDefaultGroup()
                ->getDefaultStore();
        }

        $parent->setData('store', $store);

        return $store;
    }

    public function getStoreName($parent, $args)
    {
        return $this->getStore($parent, $args)->getFrontendName();
    }

    public function getStoreEmail($parent, $args)
    {
        return Mage::getStoreConfig('trans_email/ident_general/email', $this->getStore($parent, $args)->getId());
    }

    public function getStorePhone($parent, $args)
    {
        return Mage::getStoreConfig('general/store_information/phone', $this->getStore($parent, $args)->getId());
    }

    public function getStoreAddress($parent, $args)
    {
        return Mage::getStoreConfig('general/store_information/address', $this->getStore($parent, $args)->getId());
    }

    public function getStoreUrl($parent, $args)
    {
        return $this->getStore($parent, $args)->getBaseUrl();
    }

    public function getFacebookUrl($parent, $args)
    {
        return Mage::getStoreConfig('trigger_email/info/facebook_url', $this->getStore($parent, $args)->getId());
    }

    public function getTwitterUrl($parent, $args)
    {
        return Mage::getStoreConfig('trigger_email/info/twitter_url', $this->getStore($parent, $args)->getId());
    }

    public function getLogoUrl($parent, $args)
    {
        $fileName = Mage::getStoreConfig('design/header/logo_src', $this->getStore($parent, $args)->getId());
        $path     = $parent->getSkinUrl($fileName);

        return $path;
    }

    public function getLogoAlt($parent, $args)
    {
        return Mage::getStoreConfig('design/email/logo_alt', $this->getStore($parent, $args)->getId());
    }

    public function getEmailLogoUrl($parent, $args)
    {
        $fileName = Mage::getStoreConfig('design/email/logo', $this->getStore($parent, $args)->getId());
        $path     = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'email' . DS . 'logo' . DS . $fileName;

        return $path;
    }
}