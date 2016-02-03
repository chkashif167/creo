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
class Mirasvit_SearchSphinx_Model_Observer
{
    /**
     * Если Search Engine = Sphinx
     * запускает реиндекс.
     */
    public function reindex()
    {
        if (Mage::getSingleton('searchsphinx/config')->getSearchEngine() == Mirasvit_SearchSphinx_Model_Config::ENGINE_SPHINX) {
            Mage::getModel('searchsphinx/engine_sphinx_native')->reindex();
        }
    }

    /**
     * Если Search Engine = Sphinx
     * запускает делта-реиндекс.
     */
    public function reindexDelta()
    {
        if (Mage::getSingleton('searchsphinx/config')->getSearchEngine() == Mirasvit_SearchSphinx_Model_Config::ENGINE_SPHINX) {
            Mage::getModel('searchsphinx/engine_sphinx_native')->reindex(true);
        }
    }

    /**
     * Если Search Engine = Sphinx
     * проверяет если сфинкс не запущен - делает рестарт.
     */
    public function checkDaemon()
    {
        if (Mage::getSingleton('searchsphinx/config')->getSearchEngine() == Mirasvit_SearchSphinx_Model_Config::ENGINE_SPHINX) {
            $engine = Mage::getModel('searchsphinx/engine_sphinx_native');
            if ($engine->isSearchdRunning() == false) {
                $engine->restart();
            }
        }
    }

    /**
     * При индексации Misspell (Spell Correction) добавляем синонимы заданые пользователь к правильным словам (индексу misspell'a).
     *
     * @param object $observer
     */
    public function onMisspellIndexerPrepare($observer)
    {
        // $obj = $observer->getObj();
        // $string = '';

        // $synonyms = Mage::getSingleton('searchsphinx/config')->getSynonyms();

        // foreach ($synonyms as $key => $synonyms) {
        //     $string .= $key.' '.implode(' ', $synonyms).' ';
        // }

        // $obj->setSearchSphinxSynonyms($string);
    }

    /**
     * Redirect to product if redirect is enabled and search return one item.
     */
    public function singleResultRedirect($observer)
    {
        $collection = clone Mage::getSingleton('catalogsearch/layer')->getProductCollection();

        if (Mage::getSingleton('searchsphinx/config')->isSingleResultRedictEnabled()
            && $collection->count() == 1) {
            $product = $collection->getFirstItem();
            header('Location: '.$product->getProductUrl());
            exit;
        }
    }

    /**
     * Get current block and transport and handle it.
     *
     * @param $observer
     */
    public function beforeOutput($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $transport = $observer->getEvent()->getTransport();
        if (empty($transport)) { //it does not work for magento 1.4 and older
            return $this;
        }

        if (Mage::getSingleton('searchsphinx/config')->isTermsHighlightEnabled()) {
            Mage::helper('searchindex/highlighter')->highlightTerms($block, $transport);
        }
    }
}
