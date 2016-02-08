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



class Mirasvit_Advr_Block_Adminhtml_Block_Toolbar extends Mage_Adminhtml_Block_Template
{
    public function _prepareLayout()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('toolbar_');
        $this->setForm($form);

        $this->setTemplate('mst_advr/block/toolbar.phtml');

        return parent::_prepareLayout();
    }

    protected function _beforeToHtml()
    {
        $this->_prepareFields();
        $this->_initFormValues();

        return parent::_beforeToHtml();
    }

    protected function _prepareFields()
    {
        $form = $this->getForm();

        $dateFormat = Mage::getSingleton('advr/config')->dateFormat();

        $form->addField('range', 'radios', array(
            'name'   => 'range',
            'values' => array(
                array(
                    'value' => '1d',
                    'label' => $this->__('Day')
                ),
                array(
                    'value' => '1w',
                    'label' => $this->__('Week')
                ),
                array(
                    'value' => '1m',
                    'label' => $this->__('Month')
                ),
                array(
                    'value' => '1q',
                    'label' => $this->__('Quarter')
                ),
                array(
                    'value' => '1y',
                    'label' => $this->__('Year')
                ),
            ),
            'label'  => Mage::helper('advr')->__('Show By'),
            'value'  => '1d'
        ));

        $form->addField('interval', 'select', array(
            'name'   => 'interval',
            'values' => Mage::helper('advr/date')->getIntervalsAsOptions(false, false, true),
            'label'  => Mage::helper('advr')->__('Range'),
        ));

        $form->addField('from', 'date', array(
            'name'   => 'from',
            'format' => $dateFormat,
            'locale' => Mage::app()->getLocale()->getLocaleCode(),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'label'  => Mage::helper('advr')->__('From'),
        ));

        $form->addField('to', 'date', array(
            'name'   => 'to',
            'format' => $dateFormat,
            'locale' => Mage::app()->getLocale()->getLocaleCode(),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'label'  => Mage::helper('advr')->__('To'),
        ));

        $form->addField('compare', 'checkbox', array(
            'name'    => 'compare',
            'label'   => Mage::helper('advr')->__('Compare'),
            'value'   => 1,
            'checked' => $this->getFilterData()->getCompare(),
        ));

        $form->addField('compare_from', 'date', array(
            'name'   => 'compare_from',
            'format' => $dateFormat,
            'locale' => Mage::app()->getLocale()->getLocaleCode(),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'label'  => Mage::helper('advr')->__('From'),
        ));

        $form->addField('compare_to', 'date', array(
            'name'   => 'compare_to',
            'format' => $dateFormat,
            'locale' => Mage::app()->getLocale()->getLocaleCode(),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'label'  => Mage::helper('advr')->__('To'),
        ));

        $this->setForm($form);
    }

    protected function _initFormValues()
    {
        $data = $this->getFilterData()->getData();

        $this->getForm()->addValues($data);

        return $this;
    }

    public function getIntervals()
    {
        $intervals = array();

        $format = Mage::getSingleton('advr/config')->dateFormat();

        foreach (array_keys(Mage::helper('advr/date')->getIntervals()) as $code) {
            $interval = Mage::helper('advr/date')->getInterval($code);
            $intervals[$code] = array($interval->getFrom()->toString($format), $interval->getTo()->toString($format));
        }

        return $intervals;
    }

    public function getCustomElements()
    {
        $elements = array();
        $customElements = array('range', 'interval', 'from', 'to', 'compare', 'compare_from', 'compare_to');

        foreach ($this->getForm()->getElements() as $element) {
            if (!in_array($element->getId(), $customElements)) {
                $elements[] = $element;
            }
        }

        return $elements;
    }

    public function getListOfFilters()
    {
        $filters = array();

        if (method_exists($this->getContainer()->getCollection(), 'getColumns')) {
            foreach ($this->getContainer()->getCollection()->getColumns() as $index => $column) {
                if (is_object($column)) {
                    switch ($column->getTable()) {
                        case 'sales/order':
                            $group = 'Orders';
                            break;

                        case 'sales/order_item':
                            $group = 'Order Item';
                            break;

                        case 'catalog/product':
                            $group = 'Product';
                            break;

                        default:
                            $group = 'Other';
                            break;
                    }

                    if (is_object($column) && $column->getLabel() !== false) {
                        $filters[$group][$index] = $column;
                    }
                }
            }
        }

        return $filters;
    }
}
