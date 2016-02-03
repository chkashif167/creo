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



class Mirasvit_SearchLandingPage_Model_Page extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('searchlandingpage/page');
    }

    public function checkIdentifier($identifier)
    {
        $page = $this->getCollection()
            ->addFieldToFilter('url_key', $identifier)
            ->addFieldToFilter('is_active', 1)
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->getFirstItem();

        if ($page->getId()) {
            return $page;
        }

        return false;
    }
}
