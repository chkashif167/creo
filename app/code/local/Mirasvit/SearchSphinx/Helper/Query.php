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
 * Хелпер для преобразования пользовательского запроса в готовый запрос (для системы - масив).
 *
 * @category Mirasvit
 */
class Mirasvit_SearchSphinx_Helper_Query extends Mage_Core_Helper_Abstract
{
    /**
     * Ключевая функция. Преобразует пользовательский запрос в масив с
     * синонимами, без стопcлов, исключениями wildcard, И/ИЛИ условиями.
     *
     * @param string $query      пользовательский запрос
     * @param int    $store      ИД магазина
     * @param bool   $inverseNot изменияем И на ИЛИ
     *
     * @return array
     */
    public function buildQuery($query, $store, $inverseNot = false)
    {
        if (Mage::helper('catalogsearch')->isMinQueryLength()) {
            return false;
        }

        $result = array();
        $config = Mage::getSingleton('searchsphinx/config');

        $query = strtolower($query);

        // необходимо если синоним состоит из 2х и более слов
        $query = ' '.$query.' ';

        $replaceWords = Mage::getSingleton('searchsphinx/config')->getReplaceWords();

        foreach ($replaceWords as $replace => $find) {
            foreach ($find as $word) {
                $query = str_replace(' '.$word.' ', ' '.$replace.' ', $query);
            }
        }

        $arWords = Mage::helper('core/string')->splitWords($query, true, Mage::helper('catalogsearch')->getMaxQueryWords());
        $arSynonyms = Mage::getSingleton('searchsphinx/synonym')->getSynonymsByWord($arWords, $store);

        $logic = 'like';

        foreach ($arWords as $word) {
            if (in_array($word, $config->getNotwords())) {
                $logic = 'not like';
                continue;
            }

            if ($this->isStopword($word, $store)) {
                continue;
            }

            $wordArr = array();
            $this->_addWord($wordArr, $word);

            if ($logic == 'like') {
                $longTail = $this->longtail($word);
                $this->_addWord($wordArr, $longTail);

                $singular = Mage::helper('searchsphinx/inflect_en')->singularize($word);
                $this->_addWord($wordArr, $singular);

                if (isset($arSynonyms[$word])) {
                    # for synonyms we always diable wildcards
                    $this->_addWord($wordArr, $arSynonyms[$word], Mirasvit_SearchSphinx_Model_Config::WILDCARD_DISABLED);
                }

                $template = Mage::getSingleton('searchsphinx/config')->getMatchMode();
                $result[$logic][$template][$word] = array('or' => $wordArr);
            } else {
                if (!$inverseNot) {
                    $result[$logic]['and'][$word] = array('and' => $wordArr);
                } else {
                    $result[$logic]['or'][$word] = array('and' => $wordArr);
                }
            }
        }

        return $result;
    }

    /**
     * Это стоп-слово?
     *
     * @param string $word
     * @param int    $store
     *
     * @return bool
     */
    public function isStopword($word, $store)
    {
        if (Mage::getSingleton('searchsphinx/stopword')->isStopWord($word, $store)) {
            return true;
        }

        return false;
    }

    /**
     * Для слова выполняет регулярные выражения заданые в настройках SearchIndex.
     *
     * @param string $word
     *
     * @return string
     */
    public function longtail($word)
    {
        $expressions = Mage::getSingleton('searchindex/config')->getMergeExpressins();

        foreach ($expressions as $expr) {
            $matches = null;
            preg_match_all($expr['match'], $word, $matches);

            foreach ($matches[0] as $math) {
                $math = preg_replace($expr['replace'], $expr['char'], $math);
                $word = $math;
            }
        }

        return $word;
    }

    /**
     * Добавляет слова в масив, при этом учитывает wildcard и исключения из wildcard.
     *
     * @param array $to    масив куда надо добавить слова
     * @param array $words слова, которые надо добавить
     */
    protected function _addWord(&$to, $words, $wildcard = null)
    {
        $exceptions = Mage::getSingleton('searchsphinx/config')->getWildcardExceptions();
        if ($wildcard == null) {
            $wildcard = Mage::getSingleton('searchsphinx/config')->getWildcardMode();
        }

        if (!is_array($words)) {
            $words = array($words);
        }

        foreach ($words as $word) {
            $match = false;
            foreach ($exceptions as $exp){
                if (preg_match('/^[\/].+[\/]$/', $exp)) {
                    if (preg_match($exp, $word)) {
                        $match = true;
                    }
                } elseif ($exp == $word) {
                    $match = true;
                }
            }

            if ($wildcard == Mirasvit_SearchSphinx_Model_Config::WILDCARD_PREFIX) {
                $word = $word.' ';
            } elseif ($wildcard == Mirasvit_SearchSphinx_Model_Config::WILDCARD_SUFFIX) {
                $word = ' '.$word;
            } elseif ($wildcard == Mirasvit_SearchSphinx_Model_Config::WILDCARD_DISABLED || $match === true) {
                $word = ' '.$word.' ';
            }

            if (trim($word) !== '') {
                $to[$word] = $word;
            }
        }

        ksort($to);
    }
}
