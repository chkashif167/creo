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


class Mirasvit_FeedExport_Block_Adminhtml_Feed_Edit_Tab_History_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('history_grid');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $model = Mage::registry('current_model');

        $this->setCollection($model->getHistoryCollection());

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('history_created_at',
            array(
                'header' => __('Created At'),
                'index'  => 'created_at',
                'type'   => 'datetime',
            )
        );

        $this->addColumn('history_title',
            array(
                'header' => __('Title'),
                'index'  => 'title',
            )
        );


        $this->addColumn('history_message',
            array(
                'header'   => __('Message'),
                'index'    => 'message',
                'renderer' => 'Mirasvit_FeedExport_Block_Adminhtml_Feed_Edit_Tab_History_Grid_Renderer_Message'
            )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($item)
    {
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/historyGrid', array('_current' => true));
    }
}