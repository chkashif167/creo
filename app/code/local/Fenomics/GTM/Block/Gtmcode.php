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
 * @category   Fenomics
 * @package    Fenomics_GTM
 * @subpackage Block
 * @author     Wolfgang Embach <w.embach@fenomics.de>
 */
class Fenomics_GTM_Block_Gtmcode extends Mage_Core_Block_Template
{
    
    protected function _getGTMId() {
    
        // Get the container ID.
        $Id = Mage::helper ( 'fenomics_gtm' )->getGTMId ();
    
        // Return the container id.
        return $Id;
    }
    protected function _toHtml() {
    
        //
        if (! Mage::helper ( 'fenomics_gtm' )->isGTMenabled ())
        return '';
        //
        return parent::_toHtml ();
    }
    
}
