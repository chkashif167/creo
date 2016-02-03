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
class Mirasvit_Misspell_Helper_Query extends Mage_Core_Helper_Abstract
{
    protected $_suggests = array();

    public function getCurrentPhase()
    {
        return strip_tags(Mage::app()->getFrontController()->getRequest()->getParam('q'));
    }

    public function getMisspellPhase()
    {
        return strip_tags(Mage::app()->getFrontController()->getRequest()->getParam('o'));
    }

    public function getFallbackPhase()
    {
        return strip_tags(Mage::app()->getFrontController()->getRequest()->getParam('f'));
    }

    public function getMisspellUrl($from, $to)
    {
        return Mage::getUrl('catalogsearch/result',
            array('_query' => array('q' => $to, 'o' => $from)));
    }

    public function getFallbackUrl($from, $to)
    {
        return Mage::getUrl('catalogsearch/result',
            array('_query' => array('q' => $to, 'f' => $from)));
    }

    public function suggestMisspellPhase($phase)
    {
        if (!isset($this->_suggests[$phase])) {
            $model = Mage::getModel('misspell/suggest')->loadByQuery($phase);
            $result = $model->getSuggest();

            $stringHelper = Mage::helper('misspell/string');
            if ($stringHelper->strtolower($phase) == $stringHelper->strtolower($result)
                || !$result) {
                $this->_suggests[$phase] = false;
            } else {
                $this->_suggests[$phase] = $result;
            }
        }

        return $this->_suggests[$phase];
    }

    public function suggestFallbackPhase($phase, $storeId = null)
    {
        $arQuery = explode(' ', $phase);

        for ($i = 1; $i < count($arQuery); $i++) {
            $combinations = $this->_fallbackCombinations($arQuery, $i);
            foreach ($combinations as $combination) {
                $newQuery = $phase;
                foreach ($combination as $word) {
                    $newQuery = str_replace($word, '', $newQuery);
                    $cntResults = $this->getCountResult($newQuery, $storeId);

                    if ($cntResults > 0) {
                        // remove extra spaces
                        $newQuery = preg_replace('/\s{2,}/', ' ', $newQuery);

                        return trim($newQuery);
                    }
                }
            }
        }

        return false;
    }

    public function getCountResult($phase, $storeId = null)
    {
        $cntResults = 0;

        if (Mage::helper('core')->isModuleEnabled('Mirasvit_SearchIndex')) {
            foreach (Mage::helper('searchindex/index')->getIndexes() as $index) {
                // exclude mage_catalogsearch_query
                if ($index->getCode() != 'mage_catalogsearch_query') {
                    $cntResults += count($index->reset()->getMatchedIds($phase, $storeId));
                }
            }
        } else {
            if (Mage::helper('catalogsearch')->getQueryText() == $phase) {
                $cntResults = Mage::helper('catalogsearch')->getQuery()->getNumResults();
            } else {
                $cntResults = 1;
            }
        }

        return $cntResults;
    }

    protected $_combResult = array();
    protected $_combCombination = array();

    protected function _fallbackCombinations(array $array, $choose)
    {
        $n = count($array);
        $this->_inner(0, $choose, $array, $n);

        return $this->_combResult;
    }

    protected function _inner($start, $choose, $arr, $n)
    {
        if ($choose == 0) {
            array_push($this->_combResult, $this->_combCombination);
        } else {
            for ($i = $start; $i <= $n - $choose; ++$i) {
                array_push($this->_combCombination, $arr[$i]);
                $this->_inner($i + 1, $choose - 1, $arr, $n);
                array_pop($this->_combCombination);
            }
        }
    }
}
