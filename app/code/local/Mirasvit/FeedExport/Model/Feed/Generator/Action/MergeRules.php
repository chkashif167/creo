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
 * @package   Advanced Product Feeds
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_Model_Feed_Generator_Action_MergeRules extends Mirasvit_FeedExport_Model_Feed_Generator_Action
{
    public function process()
    {
        $this->start();

        $resource   = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');
        $feedId     = intval($this->getFeed()->getId());

        $columns = array(
            'product_id' => 'product_id',
            'feed_id'    => new Zend_Db_Expr($this->getFeed()->getId()),
            'is_new'     => new Zend_Db_Expr('1'),
        );

        $select = $connection->select();
        $select->from(array('main_table' => $resource->getTableName('feedexport/rule_product')), $columns)
            ->group(array('main_table.product_id'))
            ->where('main_table.rule_id IN (?)', $this->getFeed()->getRuleIds())
            ->having('count(main_table.product_id) = ?', count($this->getFeed()->getRuleIds()))
            ->useStraightJoin();

        $feedProductTable = $resource->getTableName('feedexport/feed_product');
        $productTable = $resource->getTableName('catalog/product');
        $generatedAt = $this->getFeed()->getGeneratedAt();

        if ($this->getFeed()->getExportOnlyNew()) {
            $select->where(
                'main_table.product_id NOT IN (SELECT product_id FROM '.$feedProductTable.' WHERE feed_id='.$feedId.') OR ' .
                'main_table.product_id IN (SELECT entity_id FROM '.$productTable.' WHERE updated_at >= "' . $generatedAt . '")'
            );
        } else {
            $connection->query('DELETE FROM '.$feedProductTable.' WHERE feed_id='.$feedId);
        }

        $insertQuery = $select->insertFromSelect($feedProductTable, array_keys($columns));
        $connection->query($insertQuery);

        $this->finish();

        return $this;
    }
}