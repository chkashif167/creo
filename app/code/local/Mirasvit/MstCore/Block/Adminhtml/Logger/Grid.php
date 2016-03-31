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


class Mirasvit_MstCore_Block_Adminhtml_Logger_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('loggerGrid');
        $this->setDefaultSort('log_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mstcore/logger')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('log_id', array(
                'header' => Mage::helper('mstcore')->__('ID'),
                'index'  => 'log_id',
                'type'   => 'number',
            )
        );

        $this->addColumn('level', array(
                'header' => Mage::helper('mstcore')->__('Level'),
                'index'  => 'level',
                'type'   => 'number',
            )
        );

        $this->addColumn('message', array(
                'header' => Mage::helper('mstcore')->__('Message'),
                'index'  => 'message',
            )
        );

        $this->addColumn('content', array(
                'header'   => Mage::helper('mstcore')->__('Content'),
                'index'    => 'content',
                'width'    => '330px',
                'renderer' => 'Mirasvit_MstCore_Block_Adminhtml_Logger_Renderer_Content'
            )
        );

        $this->addColumn('module', array(
                'header' => Mage::helper('mstcore')->__('Module'),
                'index'  => 'module',
            )
        );

        $this->addColumn('class', array(
                'header' => Mage::helper('mstcore')->__('Class'),
                'index'  => 'class',
            )
        );

        $this->addColumn('created_at', array(
                'header' => Mage::helper('mstcore')->__('Created At'),
                'index'  => 'created_at',
                'align'  => 'right',
                'type'   => 'datetime',
            )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return false;
    }
}
