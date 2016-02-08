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


class Mirasvit_EmailSmtp_Block_Adminhtml_Mail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('grid');
        $this->setDefaultSort('mail_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('emailsmtp/mail')
            ->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('mail_id', array(
                'header'    => Mage::helper('emailsmtp')->__('ID'),
                'align'     => 'right',
                'width'     => '50px',
                'index'     => 'mail_id',
            )
        );

        $this->addColumn('subject', array(
                'header'    => Mage::helper('emailsmtp')->__('Subject'),
                'align'     => 'left',
                'index'     => 'subject',
            )
        );

        $this->addColumn('from_email', array(
                'header'    => Mage::helper('emailsmtp')->__('From Email'),
                'align'     => 'left',
                'index'     => 'from_email',
            )
        );

        $this->addColumn('from_name', array(
                'header'    => Mage::helper('emailsmtp')->__('From Name'),
                'align'     => 'left',
                'index'     => 'from_name',
            )
        );

        $this->addColumn('to_email', array(
                'header'    => Mage::helper('emailsmtp')->__('To Email'),
                'align'     => 'left',
                'index'     => 'to_email',
            )
        );

        $this->addColumn('to_name', array(
                'header'    => Mage::helper('emailsmtp')->__('To Name'),
                'align'     => 'left',
                'index'     => 'to_name',
            )
        );

        $this->addColumn('reply_to', array(
                'header'    => Mage::helper('emailsmtp')->__('Reply To'),
                'align'     => 'left',
                'index'     => 'reply_to',
            )
        );

        $this->addColumn('created_at', array(
                'header'    => Mage::helper('emailsmtp')->__('Created At'),
                'align'     => 'left',
                'type'      => 'datetime',
                'index'     => 'created_at',
            )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }
}
