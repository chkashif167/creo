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


class Mirasvit_FeedExport_Model_Resource_Tracker_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('feedexport/tracker_rule', 'tracker_rule_id');
    }

    public function updateRuleProductData($rule)
    {
        $ruleId = $rule->getId();
        $write  = $this->_getWriteAdapter();

        $write->beginTransaction();
        $write->delete($this->getTable('feedexport/rule_product'), $write->quoteInto('rule_id = ?', $ruleId));

        $productIds = $rule->getMatchingProductIds();

        $rows = array();
        $queryStart = 'INSERT INTO '.$this->getTable('feedexport/feed_rule_product').' (
                rule_id, product_id) values ';
        $queryEnd = ' ON DUPLICATE KEY UPDATE product_id=VALUES(product_id)';
        
        try {
            foreach ($productIds as $productId) {
                $rows[] = "('".implode("','", array($ruleId, $productId))."')";

                if (sizeof($rows) == 1000) {
                    $sql = $queryStart.join(',', $rows).$queryEnd;
                    $write->query($sql);
                    $rows = array();
                }
            }

            if (!empty($rows)) {
                $sql = $queryStart.join(',', $rows).$queryEnd;
                $write->query($sql);
            }

            $write->commit();
        } catch (Exception $e) {
            $write->rollback();
            throw $e;
        }

        return $this;
    }

    public function getRuleProductIds($ruleId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('feedexport/rule_product'), 'product_id')
            ->where('rule_id=?', $ruleId);
        return $read->fetchCol($select);
    }
}
