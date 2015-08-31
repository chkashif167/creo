<?php

class Mango_Attributeswatches_Model_Mysql4_Attributes extends Mage_Core_Model_Mysql4_Abstract {

    protected $_serializableFields = array();

    public function _construct() {
        // Note that the attributeswatches_id refers to the key field in your database table.
        $this->_init('catalog/product_super_attribute', 'product_super_attribute_id');
    }

    public function hasConfigurableAttribute($attribute_codes, $product_id) {
        $read = $this->_getReadAdapter();
        $select = $read->select()
                ->from($this->getMainTable())
                ->where($this->getMainTable() . '.product_id=?', $product_id)
                ->where("attribute_code.attribute_code in ('" . $attribute_codes . "')")
                ->join(
                        array('attribute_code' => $this->getTable("eav/attribute")), "attribute_code.attribute_id = " .$this->getMainTable(). ".attribute_id"
                )
                ->order('position ASC')
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns(array("attribute_id","attribute_code.attribute_code"))
                ->limit(1)
        ;
        $attribute_data = $read->fetchRow($select);
        if ($attribute_data) {

            $select = $read->select()->reset()
                    ->from($this->getMainTable())
                    ->where($this->getMainTable() . '.product_id=?', $product_id)
                    ->join(
                            array('attribute_code' => $this->getTable("eav/attribute")), "attribute_code.attribute_id =  " . $this->getMainTable() . ".attribute_id"
                    )->where("attribute_code.attribute_id = ? ", $attribute_data["attribute_id"])
                    ->join(
                            array('children' => $this->getTable("catalog/product_super_link")), "children.parent_id = '" . $product_id . "'"
                    )->join(
                            array('attribute_value' => (string) Mage::getConfig()->getTablePrefix() . "catalog_product_entity_int"), "attribute_value.entity_id = children.product_id and attribute_value.attribute_id = attribute_code.attribute_id "
                    )->join(
                            array('entity_type' => $this->getTable("eav/entity_type")), "attribute_value.entity_type_id = entity_type.entity_type_id and entity_type.entity_type_code = '" . Mage_Catalog_Model_Product::ENTITY . "' "
                    )->join(
                            array('sort_order' => $this->getTable("eav/attribute_option")), "sort_order.attribute_id = attribute_code.attribute_id and sort_order.option_id = attribute_value.value "
                    )->join(
                            array('attributeswatches' => $this->getTable("attributeswatches/attributeswatches")), "attributeswatches.option_id = attribute_value.value "
                    )
                    ->reset(Zend_Db_Select::COLUMNS)
                    ->columns(array("children.product_id", "attribute_value.value", "attributeswatches.filename", "attributeswatches.color", "attributeswatches.mode", "attributeswatches.attribute","attribute_code.attribute_id"))
                    ->order(array("sort_order.sort_order ASC", "sort_order.option_id ASC", "attribute_value.entity_id ASC"));


            /* if show swatches for items out of stock, skip this.... */
            $_show_out_of_stock = Mage::getStoreConfig("attributeswatches/settings/outofstock");
            if (!$_show_out_of_stock) {
                $cond = array(
                    'inventory_in_stock.use_config_manage_stock = 0 AND inventory_in_stock.manage_stock=1 AND inventory_in_stock.is_in_stock=1',
                    'inventory_in_stock.use_config_manage_stock = 0 AND inventory_in_stock.manage_stock=0',
                    'inventory_in_stock.use_config_manage_stock = 1 AND inventory_in_stock.is_in_stock=1'
                );
                $select->join(
                        array('inventory_in_stock' => $this->getTable('cataloginventory/stock_item')), 'inventory_in_stock.product_id=children.product_id'
                );
                $select->where('(' . join(') OR (', $cond) . ')');
            }

            $result = $read->fetchAll($select);
            if ($result) {
                //print_r($result);
                $_swatches_options = array();
                $_current_swatches_option = -1;

                $_labels = array();

                $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute_data["attribute_code"]);
                //print_r($attribute->getSource()->getAllOptions(false, false));
                foreach ($attribute->getSource()->getAllOptions(false, false) as $option) {
                    $_labels[$option['value']] = $option['label'];
                }


                foreach ($result as $key => $_attribute) {

                    if ($_attribute["value"] != $_current_swatches_option) {
                        $_swatches_options[$_attribute["product_id"]] = array(
                            'value' => $_attribute["value"],
                            'filename' => $_attribute["filename"],
                            'color' => $_attribute["color"],
                            'mode' => $_attribute["mode"],
                            'attribute' => $_attribute["attribute"],
                            'attribute_id' => $_attribute["attribute_id"],
                            'label' => $_labels[$_attribute['value']]
                        );
                        $_current_swatches_option = $_attribute["value"];
                    }
                }
                 //print_r($_swatches_options);
                return $_swatches_options;
            }
            return false;
        }
        return false;
    }

    public function hasConfigurableAttributeForList( $product_id, $attribute_code) {
        $read = $this->_getReadAdapter();
        $select = $read->select()
                ->from($this->getMainTable())
                ->where($this->getMainTable() . '.product_id=?', $product_id)
                ->where("attribute_code.attribute_code = '" . $attribute_code . "'")
                ->join(
                        array('attribute_code' => $this->getTable("eav/attribute")), $this->getMainTable() .".attribute_id = attribute_code.attribute_id" 
                )
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns( array('attribute_code.attribute_id') )
        ;

        $data = $read->fetchRow($select);
        return $data;
        //return false;
    }

    public function hasConfigurableAttributeSimple($attribute_code, $product_id) {
        $read = $this->_getReadAdapter();
        $select = $read->select()
                ->from($this->getMainTable())
                ->where($this->getMainTable() . '.product_id=?', $product_id)
                ->join(
                        array('attribute_code' => $this->getTable("eav/attribute")), "attribute_code.attribute_code = '" . $attribute_code . "'"
                )
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns("product_id")
        ;
        $data = $read->fetchRow($select);
        if ($data) {

            $select = $read->select()->reset()
                    ->from($this->getMainTable())
                    ->where($this->getMainTable() . '.product_id=?', $product_id)
                    ->join(
                            array('attribute_code' => $this->getTable("eav/attribute")), "attribute_code.attribute_id =  " . $this->getMainTable() . ".attribute_id"
                    )->where("attribute_code.attribute_code = ? ", $attribute_code)
                    ->join(
                            array('children' => $this->getTable("catalog/product_super_link")), "children.parent_id = '" . $product_id . "'"
                    )->join(
                            array('attribute_value' => (string) Mage::getConfig()->getTablePrefix() . "catalog_product_entity_int"), "attribute_value.entity_id = children.product_id and attribute_value.attribute_id = attribute_code.attribute_id "
                    )->join(
                            array('entity_type' => $this->getTable("eav/entity_type")), "attribute_value.entity_type_id = entity_type.entity_type_id and entity_type.entity_type_code = '" . Mage_Catalog_Model_Product::ENTITY . "' "
                    )->join(
                            array('sort_order' => $this->getTable("eav/attribute_option")), "sort_order.attribute_id = attribute_code.attribute_id and sort_order.option_id = attribute_value.value "
                    )/*->join(
                            array('attributeswatches' => $this->getTable("attributeswatches/attributeswatches")), "attributeswatches.option_id = attribute_value.value "
                    )*/
                    ->reset(Zend_Db_Select::COLUMNS)
                    ->columns(array("children.product_id", "attribute_value.value"))
                    ->order(array("sort_order.sort_order ASC", "sort_order.option_id ASC", "attribute_value.entity_id ASC"));



            $result = $read->fetchAll($select);
            if ($result) {
                //print_r($result);
                $_swatches_options = array();
                $_current_swatches_option = -1;

                $_labels = array();

                $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute_code);
                //print_r($attribute->getSource()->getAllOptions(false, false));
                foreach ($attribute->getSource()->getAllOptions(false, false) as $option) {
                    $_labels[$option['value']] = $option['label'];
                }


                foreach ($result as $key => $_attribute) {

                    if ($_attribute["value"] != $_current_swatches_option) {
                        $_swatches_options[$_attribute["product_id"]] = array(
                            'value' => $_attribute["value"],
                            //'filename' => $_attribute["filename"],
                            //'color' => $_attribute["color"],
                            //'mode' => $_attribute["mode"],
                            //'attribute' => $_attribute["attribute"],
                            //'attribute_id' => $_attribute["attribute_id"],
                            'label' => $_labels[$_attribute['value']]
                        );
                        $_current_swatches_option = $_attribute["value"];
                    }
                }
                 //print_r($_swatches_options);
                return $_swatches_options;
            }
            return false;
        }
        return false;
    }

}