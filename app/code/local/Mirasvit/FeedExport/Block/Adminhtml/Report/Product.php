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


class Mirasvit_FeedExport_Block_Adminhtml_Report_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    function __construct()
    {
        $this->_controller = 'adminhtml_report_product';
        $this->_blockGroup = 'feedexport';
        $this->_headerText = Mage::helper('feedexport')->__('Feed Report');

        parent::__construct();

        $this->_removeButton('add');
        $this->_addButton('filter_form_submit', array(
            'label'   => Mage::helper('feedexport')->__('Show Report'),
            'onclick' => 'filterFormSubmit()'
        ));
    }

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);

        return $this->getUrl('*/*/index', array('_current' => true));
    }
}