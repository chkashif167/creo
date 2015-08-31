<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog product media gallery attribute backend resource
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mango_Attributeswatches_Model_Resource_Catalog_Product_Attribute_Backend_Media extends Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media
{

    /**
     * Load gallery images for product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Product_Attribute_Backend_Media $object
     * @return array
     */
    public function loadGallery($product, $object)
    {
        $adapter = $this->_getReadAdapter();

        $positionCheckSql = $adapter->getCheckSql('value.position IS NULL', 'default_value.position', 'value.position');

        // Select gallery images for product
        $select = $adapter->select()
            ->from(
                array('main'=>$this->getMainTable()),
                array('value_id', 'value AS file')
            )
            ->joinLeft(
                array('value' => $this->getTable(self::GALLERY_VALUE_TABLE)),
                $adapter->quoteInto('main.value_id = value.value_id AND value.store_id = ?', (int)$product->getStoreId()),
                array('label','position','disabled')
            )
            ->joinLeft( // Joining default values
                array('default_value' => $this->getTable(self::GALLERY_VALUE_TABLE)),
                'main.value_id = default_value.value_id AND default_value.store_id = 0',
                array(
                    'label_default' => 'label',
                    'position_default' => 'position',
                    'disabled_default' => 'disabled',
                    'associated_attributes_default' => 'associated_attributes'
                )
            )
            ->where('main.attribute_id = ?', $object->getAttribute()->getId())
            ->where('main.entity_id = ?', $product->getId())
            ->order($positionCheckSql . ' ' . Varien_Db_Select::SQL_ASC);

        $result = $adapter->fetchAll($select);
        $this->_removeDuplicates($result);
        return $result;
    }
    
    
    
    /**
     * Load gallery images for product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Product_Attribute_Backend_Media $object
     * @return array
     */
    public function loadCartImage($product, $options)
    {
        $adapter = $this->_getReadAdapter();

        $positionCheckSql = $adapter->getCheckSql('value.position IS NULL', 'default_value.position', 'value.position');

        $_att_id = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'media_gallery')->getId();
        
        // Select gallery images for product
        $select = $adapter->select()
            ->from(
                array('main'=>$this->getMainTable()),
                array('value_id', 'value AS file')
            )
            ->joinLeft(
                array('value' => $this->getTable(self::GALLERY_VALUE_TABLE)),
                $adapter->quoteInto('main.value_id = value.value_id AND value.store_id = ?', (int)$product->getStoreId()),
                array('label','position','disabled')
            )
            ->joinLeft( // Joining default values
                array('default_value' => $this->getTable(self::GALLERY_VALUE_TABLE)),
                'main.value_id = default_value.value_id AND default_value.store_id = 0',
                array(
                    'label_default' => 'label',
                    'position_default' => 'position',
                    'disabled_default' => 'disabled',
                    'associated_attributes_default' => 'associated_attributes'
                )
            )
            ->where('main.attribute_id = ?', $_att_id)
            ->where('main.entity_id = ?', $product->getId())
            ->order($positionCheckSql . ' ' . Varien_Db_Select::SQL_ASC);
        foreach($options as $selected_option){
            $select->where("default_value.associated_attributes like ?", "%" . $selected_option . "%");
        }
        
        //echo $select;

        $result = $adapter->fetchAll($select);
        $this->_removeDuplicates($result);
        return $result;
    }

    
    
}
