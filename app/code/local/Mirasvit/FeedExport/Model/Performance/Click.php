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
 * @version   1.1.2
 * @build     671
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_Model_Performance_Click extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('feedexport/performance_click');
    }

    public function reAggregate()
    {
        Mage::getResourceModel('feedexport/performance_click_aggregated')->reAggregate();
    }
}