<?php

class Mango_Attributeswatches_Block_Adminhtml_Attributeswatches_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('attributeswatchesGrid');
        $this->setDefaultSort('attributeswatches_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('attributeswatches/attributeswatches')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('attributeswatches_id', array(
            'header' => Mage::helper('attributeswatches')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'attributeswatches_id',
        ));


$this->addColumn('attribute', array(
            'header' => Mage::helper('attributeswatches')->__('Attribute'),
            'align' => 'left',
            'index' => 'attribute',
        ));


        $this->addColumn('value', array(
            'header' => Mage::helper('attributeswatches')->__('Title'),
            'align' => 'left',
            'index' => 'value',
        ));

      $_modevalues = Mage::getModel("attributeswatches/system_config_source_swatchesmode")->toOptionArrayGrid();
      
        $this->addColumn('mode', array(
            'header' => Mage::helper('attributeswatches')->__('Mode'),
            'align' => 'left',
            'index' => 'mode',
            'type' => 'options',
            'options' => $_modevalues
        ));


        $this->addColumn('filename', array(
            'header' => Mage::helper('attributeswatches')->__('File'),
            //'width'     => '150px',
            'index' => 'filename',
            'renderer' => 'Mango_Attributeswatches_Block_Adminhtml_Attributeswatches_Grid_Render_Image',
        ));


        $this->addColumn('color', array(
            'header' => Mage::helper('attributeswatches')->__('Color'),
            //'width'     => '150px',
            'index' => 'color',
            'renderer' => 'Mango_Attributeswatches_Block_Adminhtml_Attributeswatches_Grid_Render_Color',
        ));


        /* $this->addColumn('status', array(
          'header'    => Mage::helper('attributeswatches')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
          1 => 'Enabled',
          2 => 'Disabled',
          ),
          )); */

        $this->addColumn('action', array(
            'header' => Mage::helper('attributeswatches')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('attributeswatches')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'attributeswatches_id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('attributeswatches')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('attributeswatches')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('attributeswatches_id');
        $this->getMassactionBlock()->setFormFieldName('attributeswatches');



        return $this;
    }

    public function getRowUrl($row) {
        //echo("---");

        return $this->getUrl('*/*/edit', array('attributeswatches_id' => $row->getId()));
    }

}
