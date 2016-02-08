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
 * @package   Advanced Reports
 * @version   1.0.1
 * @build     539
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



abstract class Mirasvit_Advd_Block_Adminhtml_Widget_Abstract_Grid extends Mirasvit_Advd_Block_Adminhtml_Widget_Abstract
{
    abstract protected function _prepareCollection($grid);

    abstract protected function _prepareColumns($grid);

    public function _prepareLayout()
    {
        $this->setTemplate('mst_advd/widget/grid.phtml');

        return parent::_prepareLayout();
    }

    public function getGridHtml()
    {
        $grid = $this->getLayout()->createBlock('advd/adminhtml_widget_abstract_adminhtml_grid');

        $this->_prepareCollection($grid);
        $this->_prepareColumns($grid);

        if (method_exists($this, 'getRowUrl')) {
            $grid->setRowUrlCallback(array($this, 'getRowUrl'));
        }

        return $grid->toHtml();
    }
}
