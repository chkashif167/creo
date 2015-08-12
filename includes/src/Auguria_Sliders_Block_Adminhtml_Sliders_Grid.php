<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_Sliders_Block_Adminhtml_Sliders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set defaults
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('slidersGrid');
        $this->setDefaultSort('slider_id');
        $this->setDefaultDir('desc');
    }

    /**
     * Instantiate and prepare collection
     *
     * @return Auguria_Sliders_Block_Adminhtml_Sliders_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('auguria_sliders/sliders_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Define grid columns
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'slider_id',
            array(
                'header'=> Mage::helper('auguria_sliders')->__('ID'),
            	'align'     => 'right',
                'type'  => 'number',
                'index' => 'slider_id',
            )
        );

        $this->addColumn(
            'name', array(
                'header'    => Mage::helper('auguria_sliders')->__('Name'),
                'align'     => 'left',
                'index'     => 'name'
            )
        );

        $this->addColumn(
            'sort_order', array(
            'header'    => Mage::helper('auguria_sliders')->__('Sort order'),
            'align'     => 'right',
            'index'     => 'sort_order',
            'type'  	=> 'number',
            )
        );
        
        $this->addColumn(
            'is_active', array(
            'header'    => Mage::helper('auguria_sliders')->__('Status'),
                'align'     => 'right',
                'index'     => 'is_active',
        		'type'      => 'options',
        		'options'   => Mage::helper('auguria_sliders')->getIsActiveOptionArray()
            )
        );
        
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'stores', array(
                    'header'        => Mage::helper('auguria_sliders')->__('Store View'),
                    'index'         => 'stores',
                    'type'          => 'store',
                    'store_all'     => true,
                    'store_view'    => true,
                    'sortable'      => false,
                    'filter_condition_callback' => array($this, '_filterStoreCondition'),
                )
            );
        }

        return parent::_prepareColumns();
    }
    /**
     * Prepare mass action options for this grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('slider_id');
        $this->getMassactionBlock()->setFormFieldName('sliders');

        $this->getMassactionBlock()->addItem(
        	'delete', array(
            'label'    => Mage::helper('auguria_sliders')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('auguria_sliders')->__('Are you sure you want to delete these slides?')
            )
        );
        
        
//         $status = Mage::helper('auguria_sliders')->getIsActiveOptionArray();
// 		array_unshift($status, array('label'=>'', 'value'=>''));
// 		$this->getMassactionBlock()->addItem('is_active', array(
// 			'label'=> Mage::helper('auguria_sliders')->__('Change status'),
// 			'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
// 			'additional' => array(
// 				'visibility' => array(
// 					'name' => 'is_active',
// 					'type' => 'select',
// 					'class' => 'required-entry',
// 					'label' => Mage::helper('auguria_sliders')->__('Status'),
// 					'values' => $status
// 				 )
// 			)
// 		));
		
        return $this;
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }
}
