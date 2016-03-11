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


class Mirasvit_FeedExport_Block_Adminhtml_Rule_New_Rule extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('group_fields', array());

        $rule = $this->getRule();

        $fieldset->addField('rule'.$rule->getId(), 'checkbox', array(
            'label'    => $rule->getName(),
            'required' => false,
            'name'     => 'rule_ids['.$rule->getId().']',
            'checked'  => true,
            'note'     => $rule->toString(),
        ));

        $form->setFieldNameSuffix('feed');
        $this->setForm($form);
    }

    protected function _toHtml()
    {
        parent::_toHtml();

        return $this->getForm()->getElement('group_fields')->getChildrenHtml();
    }
}
