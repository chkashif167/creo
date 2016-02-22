<?php
/**
 * MagiDev
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MagiDev Package to newer
 * versions in the future. If you wish to customize Package for your
 * needs please refer to http://www.magidev.com for more information.
 *
 * @category    Magidev
 * @package     Magidev_Sort
 * @copyright   Copyright (c) 2014 MagiDev. (http://www.magidev.com)
 */

/**
 * Model of the Package: Change positions of products in category
 *
 * @category   Magidev
 * @package    Magidev_Sort
 * @author     Magidev Team <support@magidev.com>
 */
class Magidev_Sort_Model_System_Config_Source_Type
{
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return array(
				array('value' => Magidev_Sort_Block_Adminhtml_Catalog_Category_Tab_Sort::SORT_TYPE_REPLACE, 'label' => Mage::helper('adminhtml')->__('Replace')),
				array('value' => Magidev_Sort_Block_Adminhtml_Catalog_Category_Tab_Sort::SORT_TYPE_INSERT, 'label' => Mage::helper('adminhtml')->__('Insert')),
		);
	}

	/**
	 * Get options in "key-value" format
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array(
				Magidev_Sort_Block_Adminhtml_Catalog_Category_Tab_Sort::SORT_TYPE_REPLACE => Mage::helper('adminhtml')->__('Replace'),
				Magidev_Sort_Block_Adminhtml_Catalog_Category_Tab_Sort::SORT_TYPE_INSERT => Mage::helper('adminhtml')->__('Insert'),
		);
	}
}