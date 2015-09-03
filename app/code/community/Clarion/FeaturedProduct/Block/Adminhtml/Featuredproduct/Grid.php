<?php
/**
 * Manage featured product grid block
 * 
 * @category    Clarion
 * @package     Clarion_FeaturedProduct
 * @author      Clarion Magento Team <magento@clariontechnologies.co.in>
 * 
 */
class Clarion_FeaturedProduct_Block_Adminhtml_Featuredproduct_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('featuredproductGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');

    }
    
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    
    /**
     * Prepare featuredproduct grid collection object
     *
     * @return Clarion_FeaturedProduct_Block_Adminhtml_Featuredproduct_Grid
     */
    protected function _prepareCollection()
    {
       $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id');

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('is_featured_product');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }

        $this->setCollection($collection);

        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }
    
    /**
     * Prepare default grid column
     *
     * @return Clarion_FeaturedProduct_Block_Adminhtml_Featuredproduct_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('clarion_featuredproduct')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));
        $this->addColumn('clarion_featuredproduct',
            array(
                'header'=> Mage::helper('clarion_featuredproduct')->__('Featured'),
                'width' => '50px',
                'type'  => 'options',
                'index' => 'is_featured_product',
                'options' => array(
                    0 => 'No',
                    1 => 'Yes')
        ));
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('clarion_featuredproduct')->__('Name'),
                'index' => 'name',
        ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> Mage::helper('clarion_featuredProduct')->__('Name in %s', $store->getName()),
                    'index' => 'custom_name',
            ));
        }

        $this->addColumn('type',
            array(
                'header'=> Mage::helper('clarion_featuredproduct')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));
        
        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('clarion_featuredproduct')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('clarion_featuredproduct')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));

        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('clarion_featuredproduct')->__('Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
        ));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('qty',
                array(
                    'header'=> Mage::helper('clarion_featuredproduct')->__('Qty'),
                    'width' => '100px',
                    'type'  => 'number',
                    'index' => 'qty',
            ));
        }

        $this->addColumn('visibility',
            array(
                'header'=> Mage::helper('clarion_featuredproduct')->__('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('status',
            array(
                'header'=> Mage::helper('clarion_featuredproduct')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> Mage::helper('clarion_featuredproduct')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        //set database primary key 
        $this->setMassactionIdField('entity_id');
        //set checkbox name
        $this->getMassactionBlock()->setFormFieldName('product');
        
        //Unset featured
         $this->getMassactionBlock()->addItem('unset_featured', array(
             'label'=> Mage::helper('clarion_featuredproduct')->__('Unset Featured'),
             'url'  => $this->getUrl('*/*/massUnsetFeatured', array('_current'=>true)),
        ));
         
        //Set featured
         $this->getMassactionBlock()->addItem('set_featured', array(
             'label'=> Mage::helper('clarion_featuredproduct')->__('Set Featured'),
             'url'  => $this->getUrl('*/*/massSetFeatured', array('_current'=>true)),
        ));
        return $this;
    }
    
    /**
     * Used for Ajax Based Grid
     * 
     * URL which is called in the Ajax Request, to the get
     *  the content of the grid. _current Uses the current module, controller, 
     * action and parameters.
     *
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }
}

