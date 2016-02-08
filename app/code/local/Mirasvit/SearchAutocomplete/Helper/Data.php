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
class Mirasvit_SearchAutocomplete_Helper_Data extends Mage_Core_Helper_Data
{
    protected $_indexes = null;

    public function toSingleRegister($base, $needle)
    {
        for ($i = 0; $i < strlen($base); $i++) {
            if (ctype_lower($base[$i])) {
                $needle{$i}
                = strtolower($needle{$i});
            } else {
                $needle{$i}
                = strtoupper($needle{$i});
            }
        }

        return $needle;
    }

    public function higlight($text, $query)
    {
        $result = $text;

        $query = preg_split("/[,\. ]/", $query);

        foreach ($query as $word) {
            $result = preg_replace("|($word)|Ui", '<strong>$1</strong>', $result);
        }

        return $result;
    }

    public function getIndexes($all = true)
    {
        if ($this->_indexes == null) {
            $this->_indexes = array();

            if (Mage::helper('core')->isModuleEnabled('Mirasvit_SearchIndex')) {
                $indexes = Mage::helper('searchindex/index')->getIndexes();
                uasort($indexes, array($this, 'sortIndexes'));
                foreach ($indexes as $index) {
                    // if multistore mode enabled, we select index only for current store
                    if (!$index->getStoreId() || $index->getStoreId() == Mage::app()->getStore()->getId()) {
                        $this->_indexes[$index->getCode()] = $index->getTitle();
                    }
                }
            } else {
                $this->_indexes['mage_catalog_product'] = '';
            }
        }

        if ($all == false) {
            $displayedIndexes = $this->_indexes;
            $forDisplay = explode(',', Mage::getStoreConfig('searchautocomplete/general/indexes'));
            foreach ($displayedIndexes as $code => $label) {
                if (!in_array($code, $forDisplay)) {
                    unset($displayedIndexes[$code]);
                }
            }

            return $displayedIndexes;
        }

        return $this->_indexes;
    }

    /**
     * Sort indexes by position, in ascending order.
     */
    private function sortIndexes($a, $b)
    {
        if ($a->getData('position') == $b->getData('position')) {
            return 0;
        }

        return ($a->getData('position') > $b->getData('position')) ? 1 : -1;
    }
}
