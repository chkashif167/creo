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


class Mirasvit_FeedExport_Model_System_Config_Source_TrackerAttribute
{
    protected $_additional = array(
        'clicks_7'   => 'Last 7-days Clicks',
        'orders_7'   => 'Last 7-days Orders',
        'revenue_7'  => 'Last 7-days Revenue',
        'cr_7'       => 'Last 7-days Conversation Rate (%)',
        'roas_7'     => 'Last 7-days ROAS (%)',

        'clicks_14'  => 'Last 14-days Clicks',
        'orders_14'  => 'Last 14-days Orders',
        'revenue_14' => 'Last 14-days Revenue',
        'cr_14'      => 'Last 14-days Conversation Rate (%)',
        'roas_14'    => 'Last 14-days ROAS (%)',

        'clicks_30'  => 'Last 30-days Clicks',
        'orders_30'  => 'Last 30-days Orders',
        'revenue_30' => 'Last 30-days Revenue',
        'cr_30'      => 'Last 30-days Conversation Rate (%)',
        'roas_30'    => 'Last 30-days ROAS (%)',
    );

    public function toOptionArray()
    {
        $result = array();

        foreach ($this->_additional as $value => $label) {
            $result[$label] = array(
                'label' => $label,
                'value' => $value,
            );
        }

        return $result;
    }
}