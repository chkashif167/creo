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



class Mirasvit_SearchIndex_Block_Adminhtml_Validation_Tab_Result extends Mage_Adminhtml_Block_Template
{
    public function _prepareLayout()
    {
        $this->setTemplate('searchindex/validation/tab/result.phtml');

        return parent::_prepareLayout();
    }

    public function getQ()
    {
        return Mage::app()->getRequest()->getParam('q');
    }

    public function getId()
    {
        return intval(Mage::app()->getRequest()->getParam('id'));
    }

    public function getValidationResult()
    {
        $result = array();

        if (!$this->getQ() || !$this->getId()) {
            return array();
        }

        $catalogIndex = Mage::helper('searchindex/index')->getIndex('mage_catalog_product');
        $matchedIds = $catalogIndex->getMatchedIds($this->getQ());

        if (isset($matchedIds[$this->getId()])) {
            $result[] = array(true, 'Search return this product by this search phase', '');
        } else {
            $description = array();

            $validateUrl = Mage::getSingleton('adminhtml/url')->getUrl('mstcore/adminhtml_validator/index', array('modules' => ''));
            $description[] = "Please, <a href='$validateUrl' target='_blank'>validate extension installation</a>.";
            $description[] = 'Be sure, that this product contains these search phrase.';

            $result[] = array(false, 'Search did NOT return this product by this search phase', implode('<br>', $description));
        }

        $res = Mage::getSingleton('core/resource');
        $conn = $res->getConnection('core_read');

        $tables = array(
            'catalog/product' => array('entity_id', ' This product does not exist!'),
            'catalogsearch/fulltext' => array('product_id', 'Run full reindex of "Catalog Search" index".'),
            'catalog/category_product' => array('product_id', 'Please, assign the product to some category.'),
            'catalog/category_product_index' => array('product_id', 'Run full reindex of "Category Products".'),
            'catalog/product_index_price' => array('entity_id', 'Run full reindex of "Product Prices".'),
            'cataloginventory/stock_status' => array('product_id', 'Run full reindex of "Stock Status".'),
        );

        foreach ($tables as $table => $data) {
            $tabl = $res->getTableName($table);

            $rows = $conn->fetchAll("SELECT * FROM $tabl WHERE $data[0]=".$this->getId());

            if (count($rows) > 0) {
                $result[] = array(true, "Table $tabl contains this product", '');
            } else {
                $result[] = array(false, "Table $tabl DOES NOT contain this product.", $data[1]);
            }

            if (isset($_GET['table'])) {
                echo $this->printTable($rows);
            }
        }

        return $result;
    }

    public function printTable($result)
    {
        $html = '';
        $html .= '<table border="1">';
        foreach ($result as $row) {
            $html .= '<tr>';
            foreach ($row as $column => $value) {
                $html .= '<th>'.$column.'</th>';
            }
            $html .= '</tr>';
            break;
        }

        foreach ($result as $row) {
            $html .= '<tr>';
            foreach ($row as $value) {
                $html .= '<td>'.$value.'</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';

        return $html;
    }
}
