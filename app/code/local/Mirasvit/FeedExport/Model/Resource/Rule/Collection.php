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
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_Model_Resource_Rule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('feedexport/rule');
    }

    public function addTypePerformanceFilter()
    {
        $this->addFieldToFilter('type', Mirasvit_FeedExport_Model_Rule::TYPE_PERFORMANCE);

        return $this;
    }

    public function addTypeAttributeFilter()
    {
        $this->addFieldToFilter('type', Mirasvit_FeedExport_Model_Rule::TYPE_ATTRIBUTE);

        return $this;
    }
}