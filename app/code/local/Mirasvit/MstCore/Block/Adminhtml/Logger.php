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


class Mirasvit_MstCore_Block_Adminhtml_Logger extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_logger';
        $this->_blockGroup = 'mstcore';
        $this->_headerText = Mage::helper('mstcore')->__('Mirasvit Extensions logging');

        parent::__construct();

        $this->setTemplate('widget/grid/container.phtml');
        $this->_removeButton('add');
    }
}
