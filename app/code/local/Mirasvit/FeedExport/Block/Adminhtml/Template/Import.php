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


class Mirasvit_FeedExport_Block_Adminhtml_Template_Import extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct ()
    {
        parent::__construct();

        $this->_objectId   = 'template_id';
        $this->_blockGroup = 'feedexport';
        $this->_mode       = 'import';
        $this->_controller = 'adminhtml_template';

        $this->_addButton('save', array(
            'label'     => __('Import Templates'),
            'onclick'   => 'editForm.submit();',
            'class'     => 'save',
        ), 1);

        return $this;
    }

    public function getHeaderText ()
    {
        return __('Import Templates');
    }
}