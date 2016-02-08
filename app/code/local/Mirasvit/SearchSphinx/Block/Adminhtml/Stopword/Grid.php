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



/**
 * Таблица стоп-слов.
 *
 * @category Mirasvit
 */
class Mirasvit_SearchSphinx_Block_Adminhtml_Stopword_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('searchSphinxStopwordGrid');
        $this->setDefaultSort('stopword_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('searchsphinx/stopword')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('stopword_id', array(
            'header' => Mage::helper('searchsphinx')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'stopword_id',
        ));

        $this->addColumn('word', array(
            'header' => Mage::helper('searchsphinx')->__('Word'),
            'align' => 'left',
            'index' => 'word',
        ));

        $this->addColumn('store', array(
            'header' => Mage::helper('searchsphinx')->__('Store'),
            'align' => 'left',
            'width' => '200px',
            'index' => 'store',
            'type' => 'options',
            'options' => Mage::getSingleton('adminhtml/system_store')->getStoreOptionHash(),
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('stopword_id');
        $this->getMassactionBlock()->setFormFieldName('stopword');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('searchsphinx')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('searchsphinx')->__('Are you sure?'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
