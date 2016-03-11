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


class Mirasvit_FeedExport_Block_Adminhtml_Dynamic_Attribute_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Attribute Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label'   => __('Attribute Information'),
            'title'   => __('Attribute Information'),
            'content' => $this->getLayout()->createBlock('feedexport/adminhtml_dynamic_attribute_edit_tab_general')
                ->toHtml(),
        ))->addTab('conditions_section', array(
            'label'   => __('Conditions'),
            'title'   => __('Conditions'),
            'content' => $this->getLayout()->createBlock('feedexport/adminhtml_dynamic_attribute_edit_tab_conditions')
                ->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}