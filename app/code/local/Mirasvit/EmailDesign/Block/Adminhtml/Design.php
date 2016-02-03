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
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_EmailDesign_Block_Adminhtml_Design extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller     = 'adminhtml_design';
        $this->_blockGroup     = 'emaildesign';
        $this->_headerText     = __('Manage Designs');
        $this->_addButtonLabel = __('Add Design');

        $this->_addButton('import', array(
            'label'     => __('Import Mailchimp Design'),
            'onclick'   => 'setLocation(\''.Mage::helper("adminhtml")->getUrl('*/*/importMailchimp').'\')',
            'class'     => 'import',
        ));

        return parent::__construct();
    }
}