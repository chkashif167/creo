<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */
class Amasty_Checkoutfees_Block_Adminhtml_Fees_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $hlp   = Mage::helper('amcheckoutfees');
        $form  = new Varien_Data_Form();
        $data = Mage::getSingleton('adminhtml/session')->getFormData();
        $model = Mage::registry('amcheckoutfees_fees');
        $this->setForm($form);

        $fldInfo = $form->addFieldset('amcheckoutfees_info', array('legend' => $hlp->__('General Info')));

        $fldInfo->addField('enabled', 'select', array(
                'label'    => $hlp->__('Enabled'),
                'title'    => $hlp->__('Enabled'),
                'name'     => 'enabled',
                'required' => true,
                'options'  => array(
                    '0' => $this->__('No'),
                    '1' => $this->__('Yes'),
                ),
            )
        );

        $fldInfo->addField('name', 'text', array(
                'label'    => $hlp->__('Fee Name'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'name',
            )
        );

        $fldInfo->addField('input', 'select', array(
                'label'    => $hlp->__('Fee Input Type'),
                'title'    => $hlp->__('Fee Input Type'),
                'name'     => 'input',
                'required' => true,
                'options'  => array(
                    '1' => $this->__('Drop-Down'),
                    '2' => $this->__('Checkbox'),
                    '3' => $this->__('Radio Button'),
                ),
            )
        );
        $fldInfo->addField('sort', 'text', array(
                'label' => $hlp->__('Sort Order'),
                'name'  => 'sort',
            )
        );

        $fldInfo->addField('description', 'textarea', array(
                'label' => $hlp->__('Description'),
                'name'  => 'description',
            )
        );

        $fldInfo->addField('cust_groups', 'multiselect', array(
                'label'  => $hlp->__('Customer Groups'),
                'required' => true,
                'name'   => 'cust_groups[]',
                'values' => Mage::getResourceModel('customer/group_collection')->toOptionArray(),
            )
        );

        $field    = $fldInfo->addField('stores', 'multiselect', array(
                'name'     => 'stores[]',
                'label'    => Mage::helper('cms')->__('Store View'),
                'title'    => Mage::helper('cms')->__('Store View'),
                'required' => true,
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            )
        );
        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
        $field->setRenderer($renderer);


        $fldPosition = $form->addFieldset('amcheckoutfees_positions', array('legend' => $hlp->__('Position')));

        $fldPosition->addField('position_cart', 'select', array(
                'label'   => $hlp->__('Cart'),
                'title'   => $hlp->__('Cart'),
                'name'    => 'position_cart',
                'options' => array(
                    '0' => $this->__('No'),
                    '1' => $this->__('Yes'),
                ),
            )
        );
        $fldPosition->addField('position_checkout', 'select', array(
                'label'   => $hlp->__('Checkout'),
                'title'   => $hlp->__('Checkout'),
                'name'    => 'position_checkout',
                'options' => array(
                    '0' => $this->__('No'),
                    '1' => $this->__('Shipping'),
                    '2' => $this->__('Payment'),
                ),
            )
        );
        $fldPosition->addField('autoapply', 'select', array(
                'label'              => $hlp->__('Autoapply'),
                'title'              => $hlp->__('Autoapply'),
                'after_element_html' => $hlp->__('This fee will be hidded and will automatically applied after payment/shipping method checkout step.'),
                'name'               => 'autoapply',
                'options'            => array(
                    '0' => $this->__('No'),
                    '1' => $this->__('Yes'),
                ),
            )
        );


        // normalize data from database stored in string into array
        if ($data) {
            $data['stores']      = explode(',', $data['stores']);
            $data['cust_groups'] = explode(',', $data['cust_groups']);
        }

        // load all titles (translations)
        $storeTitles = array();
        $feesId      = Mage::app()->getRequest()->getParam('id');
        if ($feesId > 0) {
            $feeData = Mage::getModel('amcheckoutfees/fees')->load($feesId);
            $titles  = $feeData->getTitle() ? unserialize($feeData->getTitle()) : array();
            if (is_array($titles) && count($titles) > 0) {
                foreach ($titles as $storeId => $title) {
                    $storeTitles["title_$storeId"] = $title;
                }
            }
        } else {
            $storeTitles = array();
        }

        // process render for titles (translations) sections
        $fieldset = $form->addFieldset('store_labels_fieldset',
            array(
                'legend'      => Mage::helper('salesrule')->__('Store View Specific Labels'),
                'table_class' => 'form-list stores-tree',
            )
        );
        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset');
        $fieldset->setRenderer($renderer);


        foreach (Mage::app()->getWebsites() as $website) {
            $fieldset->addField("w_{$website->getId()}_label", 'note', array(
                    'label'               => $website->getName(),
                    'fieldset_html_class' => 'website',
                )
            );
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField("sg_{$group->getId()}_label", 'note', array(
                        'label'               => $group->getName(),
                        'fieldset_html_class' => 'store-group',
                    )
                );
                foreach ($stores as $store) {
                    $fieldset->addField("title_{$store->getId()}", 'text', array(
                            'name'                => 'title[' . $store->getId() . ']',
                            'required'            => false,
                            'label'               => $store->getName(),
                            'fieldset_html_class' => 'store',
                        )
                    );
                }
            }
        }


        // finally, set form values
        if (!$data && $model->getData()) {
            $data = $model->getData();
        }

        if ($data) {
            $data = array_merge($storeTitles, $data);
            $form->setValues($data);
            Mage::getSingleton('adminhtml/session')->setFormData(NULL);
        }

        return parent::_prepareForm();
    }
}
