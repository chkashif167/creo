<?php
/**
 * @category    Bubble
 * @package     Bubble_AttributeOptionPro
 * @version     1.1.4
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_AttributeOptionPro_Model_Resource_Eav_Entity_Attribute_Option
    extends Mage_Eav_Model_Resource_Entity_Attribute_Option
{
    public function getAttributeOptionImages()
    {
        $select = $this->getReadConnection()
            ->select()
            ->from($this->getTable('eav/attribute_option'), array('option_id', 'image'));

        return $this->getReadConnection()->fetchPairs($select);
    }

    public function getAttributeOptionAdditionalImages()
    {
        $select = $this->getReadConnection()
            ->select()
            ->from($this->getTable('eav/attribute_option'), array('option_id', 'additional_image'));

        return $this->getReadConnection()->fetchPairs($select);
    }
}