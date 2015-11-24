<?php

/**
 * FENOMICS extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Fenomics GTM module to newer versions in the future.
 * If you wish to customize the Fenomics GTM module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Fenomics
 * @package    Fenomics_GTM
 * @copyright  Copyright (C) 2014 FENOMICS GmbH (http://www.fenomics.de/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * @category Fenomics
 * @package Fenomics_GTM
 * @subpackage Helper
 * @author Wolfgang Embach <w.embach@fenomics.de>
 */
class Fenomics_GTM_Helper_Data extends Mage_Core_Helper_Data
{

    /**
     *
     * @return boolean
     */
    public function isGTMenabled()
    {
        $w = Mage::getStoreConfigFlag('gtm/customization/loadgtm');
        return $w;
    }

    /**
     *
     * @return Ambigous <mixed, string, NULL, multitype:, multitype:Ambigous <string, multitype:, NULL> >
     */
    public function getGTMId()
    {
        $w = Mage::getStoreConfig('gtm/customization/idgtm');
        return $w;
    }
}
