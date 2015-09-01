<?php
class Mango_Attributeswatches_Model_Attributeswatches extends Mage_Core_Model_Abstract {
    public function _construct() {
        parent::_construct();
        $this->_init('attributeswatches/attributeswatches');
    }
    public function getResourceCollection() {
        if (empty($this->_resourceCollectionName)) {
            Mage::throwException(Mage::helper('core')->__('Model collection resource name is not defined.'));
        }
        $resource_collection = Mage::getResourceModel($this->_resourceCollectionName, $this->_getResource());
        $_fields = Mage::helper("attributeswatches")->getAttributesWithSwatchesForSQL();
        $resource_collection
                ->getSelect()
                ->join(
                        array('attribute_value' => $this->getResource()->getTable("eav/attribute_option_value")), 'attribute_value.option_id = main_table.option_id and attribute_value.store_id=0', array("value", "eav_option_id" => "option_id"))
                ->join(
                        array('attribute' => $this->getResource()->getTable("eav/attribute_option")), 'attribute_value.option_id = attribute.option_id', array())
                ->join(
                        array('attribute_info' => $this->getResource()->getTable("eav/attribute")), 'attribute.attribute_id = attribute_info.attribute_id and attribute_code in (' . $_fields . ')', array())
        ;
        return $resource_collection;
    }
    public function refresh() {
        $tablename = $this->getResource()->getTable("attributeswatches/attributeswatches");
        /* include also the attributes that will be displayed in the list and in the product view */
        $_fields = Mage::helper("attributeswatches")->getAttributesWithSwatchesForSQL();
        //echo $_fields . "sdfsfsd";
        if ($_fields) {
            $query = "insert into " . $tablename . " (option_id, attribute, mode) "
                    . "select option_id, attribute_code , 1 from "
                    . $this->getResource()->getTable("eav/attribute_option")
                    . " ao inner join "
                    . $this->getResource()->getTable("eav/attribute")
                    . " a on a.attribute_id = ao.attribute_id "
                    . " where a.attribute_code in ( " . $_fields . " )"
                    . " and ao.option_id not in (select option_id from " . $tablename . ") ";
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            $connection->beginTransaction();
            $connection->query($query);
            $connection->commit();
        }
        return true;
    }
}
