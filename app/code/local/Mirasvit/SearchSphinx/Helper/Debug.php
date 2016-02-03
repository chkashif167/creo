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
 * @category Mirasvit
 */
class Mirasvit_SearchSphinx_Helper_Debug extends Mage_Core_Helper_Abstract
{
    /**
     * Return result of request to table catalogsearch_fulltext.
     *
     * @param array  $result - products ids and their search relevance
     * @param array  $weight - products ids and their search weight
     * @param object $select - Varien_Db_Select
     */
    public function searchDebug($result, $weight, $select)
    {
        echo '<h3 style="text-align:center;">Query:</h3>';
        echo '<p style="border:3px solid gray;padding:10px;">'.$select->__toString().'</p>';
        echo '<table border="2" cellpadding="2" cellspacing="0" align="center"><caption style="text-align:center;font-size:1.17em;font-weight:bold">Result:</caption><tr><th>Product Id</th><th>Relevance</th><th>Search Weight</th></tr>';

        foreach ($result as $entity_id => $entity) {
            echo '<tr style="text-align:center;"><td>'.$entity_id.'</td><td>'.$entity.'</td><td>'.$weight[$entity_id].'</td></tr>';
        }
        echo '</table>';
        die();
    }
}
