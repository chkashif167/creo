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



class Mirasvit_Advd_Block_Adminhtml_Widget_Helpdesk_Metric extends Mirasvit_Advd_Block_Adminhtml_Widget_Abstract_Metric
{
    public function isEnabled()
    {
        return Mage::helper('core')->isModuleEnabled('Mirasvit_Helpdesk');
    }

    public function getGroup()
    {
        return 'Helpdesk';
    }

    public function getName()
    {
        return 'Number of tickets';
    }

    public function prepareOptions()
    {
        $all = array(
            array(
                'value' => '',
                'label' => Mage::helper('advr')->__('All')
            ),

            array(
                'value' => '0',
                'label' => Mage::helper('advr')->__('Unassigned')
            )
        );

        $statuses = array_merge($all, Mage::getModel('helpdesk/status')->getCollection()->toOptionArray());
        $priorities = array_merge($all, Mage::getModel('helpdesk/priority')->getCollection()->toOptionArray());
        $owners = array_merge($all, Mage::helper('helpdesk')->getAdminUserOptionArray());

        $this->form->addField(
            'status_ids',
            'multiselect',
            array(
                'name'   => 'status_ids',
                'label'  => Mage::helper('advr')->__('Status'),
                'values' => $statuses,
                'value'  => $this->getParam('status_ids'),
            )
        );

        $this->form->addField(
            'priority_ids',
            'multiselect',
            array(
                'name'   => 'priority_ids',
                'label'  => Mage::helper('advr')->__('Priority'),
                'values' => $priorities,
                'value'  => $this->getParam('priority_ids'),
            )
        );

        $this->form->addField(
            'owner_id',
            'select',
            array(
                'name'   => 'owner_id',
                'label'  => Mage::helper('advr')->__('Ticket Owner'),
                'values' => $owners,
                'value'  => $this->getParam('owner_id'),
            )
        );

        return $this;
    }

    public function getMetricValue()
    {
        $value = $this->_getCollection()->count();

        return $value;
    }

    public function getMetricValueToCompare()
    {
        return false;
    }

    public function formatMetricValue($value)
    {
        return $value;
    }

    public function _getCollection()
    {
        $collection = Mage::getModel('helpdesk/ticket')->getCollection()
            ->addFieldToFilter('is_spam', false)
            ->addFieldToFilter('is_archived', 0);

        $priorityIds = $this->getParam('priority_ids', array());
        $statusIds = $this->getParam('status_ids', array());
        $ownerId = $this->getParam('owner_id', false);

        if ($priorityIds && count($priorityIds)) {
            $collection->addFieldToFilter('priority_id', $priorityIds);
        }

        if ($statusIds && count($statusIds)) {
            $collection->addFieldToFilter('status_id', $statusIds);
        }

        if ($ownerId != '') {
            $collection->addFieldToFilter('user_id', $ownerId);
        }

        return $collection;
    }
}
