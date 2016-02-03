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
 * @package   Sphinx Search Ultimate
 * @version   2.3.2
 * @build     1290
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_SearchIndex_Block_Adminhtml_Index_Edit_Index_Mage_Cms_Page_Additional extends Varien_Data_Form_Element_Fieldset
{
    public function toHtml()
    {
        $model = $this->getModel();

        parent::__construct(array('legend' => __('Additional')));

        $this->addField('ignore', 'multiselect', array(
            'name' => 'properties[ignore]',
            'label' => __('Ingnored Pages'),
            'required' => false,
            'value' => $model->getProperty('ignore'),
            'values' => Mage::getSingleton('adminhtml/system_config_source_cms_page')->toOptionArray(),
            'note' => Mage::helper('searchindex/help')->field('ignore'),
        ));

        return parent::toHtml();
    }
}
