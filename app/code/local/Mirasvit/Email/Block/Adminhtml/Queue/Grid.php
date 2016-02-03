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


class Mirasvit_Email_Block_Adminhtml_Queue_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('email_queue_grid');
        $this->setDefaultSort('queue_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('email/queue')->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('queue_id', array(
            'header'    => Mage::helper('email')->__('ID'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'queue_id',
        ));

        $this->addColumn('scheduled_at', array(
            'header'    => Mage::helper('email')->__('Scheduled At'),
            'align'     => 'left',
            'type'      => 'datetime',
            'index'     => 'scheduled_at',
        ));

        $this->addColumn('sent_at', array(
            'header'    => Mage::helper('email')->__('Sent At'),
            'align'     => 'left',
            'type'      => 'datetime',
            'index'     => 'sent_at',
        ));

        $triggers = array();
        foreach (Mage::getModel('email/trigger')->getCollection() as $trigger) {
            $triggers[$trigger->getId()] = $trigger->getTitle();
        }
        $this->addColumn('trigger_id', array(
            'header'    => Mage::helper('email')->__('Trigger'),
            'align'     => 'left',
            'index'     => 'trigger_id',
            'type'      => 'options',
            'options'   => $triggers,
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('email')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                Mirasvit_Email_Model_Queue::STATUS_PENDING      => Mage::helper('email')->__('Ready to go'),
                Mirasvit_Email_Model_Queue::STATUS_DELIVERED    => Mage::helper('email')->__('Delivered'),
                Mirasvit_Email_Model_Queue::STATUS_CANCELED     => Mage::helper('email')->__('Canceled'),
                Mirasvit_Email_Model_Queue::STATUS_ERROR        => Mage::helper('email')->__('Error'),
                Mirasvit_Email_Model_Queue::STATUS_MISSED       => Mage::helper('email')->__('Missed'),
                Mirasvit_Email_Model_Queue::STATUS_UNSUBSCRIBED => Mage::helper('email')->__('Unsubcribed'),
            ),
        ));

        $this->addColumn('sender_email', array(
            'header'    => Mage::helper('email')->__('Sender Email'),
            'align'     => 'left',
            'index'     => 'sender_email',
        ));

        $this->addColumn('sender_name', array(
            'header'    => Mage::helper('email')->__('Sender Name'),
            'align'     => 'left',
            'index'     => 'sender_name',
        ));

        $this->addColumn('recipient_email', array(
            'header'    => Mage::helper('email')->__('Recipient Email'),
            'align'     => 'left',
            'index'     => 'recipient_email',
        ));

        $this->addColumn('recipient_name', array(
            'header'    => Mage::helper('email')->__('Recipient Name'),
            'align'     => 'left',
            'index'     => 'recipient_name',
        ));

        $this->addColumn('action',
            array(
                'header'  => Mage::helper('email')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('email')->__('Preview'),
                        'url'     => array('base' => '*/*/preview'),
                        'field'   => 'id',
                        'popup'   => true,
                    ),
                    array(
                        'caption' => Mage::helper('email')->__('Cancel'),
                        'url'     => array('base' => '*/*/cancel'),
                        'field'   => 'id'
                    ),
                    array(
                        'caption' => Mage::helper('email')->__('Send'),
                        'url'     => array('base' => '*/*/send'),
                        'field'   => 'id'
                    ),
                    array(
                        'caption' => Mage::helper('email')->__('Reset'),
                        'url'     => array('base' => '*/*/reset'),
                        'field'   => 'id'
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
            )
        );

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('queue_id');
        $this->getMassactionBlock()->setFormFieldName('queue');

        $this->getMassactionBlock()->addItem('cancel', array(
            'label'    => Mage::helper('email')->__('Cancel'),
            'url'      => $this->getUrl('*/*/massCancel'),
            'confirm'  => Mage::helper('email')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('send', array(
            'label'    => Mage::helper('email')->__('Send'),
            'url'      => $this->getUrl('*/*/massSend'),
            'confirm'  => Mage::helper('email')->__('Are you sure?')
        ));

        $statuses = array(
            Mirasvit_Email_Model_Queue::STATUS_PENDING   => Mage::helper('email')->__('Pending'),
            Mirasvit_Email_Model_Queue::STATUS_DELIVERED => Mage::helper('email')->__('Delivered'),
            Mirasvit_Email_Model_Queue::STATUS_CANCELED  => Mage::helper('email')->__('Canceled'),
            Mirasvit_Email_Model_Queue::STATUS_ERROR     => Mage::helper('email')->__('Error'),
            Mirasvit_Email_Model_Queue::STATUS_MISSED    => Mage::helper('email')->__('Missed'),
        );
        array_unshift($statuses, array('label' => '', 'value' => ''));

        $this->getMassactionBlock()->addItem('status', array(
            'label'      => Mage::helper('email')->__('Change status'),
            'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name'   => 'status',
                    'type'   => 'select',
                    'class'  => 'required-entry',
                    'label'  => Mage::helper('email')->__('Status'),
                    'values' => $statuses
                )
            )
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }
}