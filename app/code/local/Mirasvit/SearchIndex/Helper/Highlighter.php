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



class Mirasvit_SearchIndex_Helper_Highlighter extends Mage_Core_Helper_Abstract
{
    private static $productId = array(
        'name' => array('(<[\w]{1,3}[^>]class="product-name"><a[^>]*>)', '(<\/a><\/[\w]{1,3}>)'),
        'desc' => array('(<div[^>]*class="desc[^"]*">)', '(<a.*)'),
    );

    private static $categoryId = array(
        '(<a[^>]*>)',
        '(<\/a>)',
    );

    private static $otherId = array(
        'title' => array('(<div class="title">[\s]*<a[^>]*>)', '(<\/a>[\s]*<\/div>)'),
        'content' => array('(<div class="content">[\s]*)', '([\s]*<\/div>)'),
    );

    /**
     * Highlight search term on the search result pages.
     *
     * @param $block
     * @param $transport
     *
     * @return $this
     */
    public function highlightTerms($block, $transport)
    {
        if (!$block instanceof Mirasvit_SearchIndex_Block_Results) {
            return $this;
        }

        $html = $transport->getHtml();
        $query = $this->escapeSpecialChars(Mage::helper('catalogsearch')->getQueryText());
        $replacement = array();
        $pattern = array();

        switch ($block->getCurrentIndex()->getIndexCode()) {
            case 'mage_catalog_product':
                $matchName = $this->getMatches(self::$productId['name'][0], self::$productId['name'][1], $html);
                $matchDesc = $this->getMatches(self::$productId['desc'][0], self::$productId['desc'][1], $html);
                $pattern[] = $this->createPattern(self::$productId['name'][0], self::$productId['name'][1], $matchName);
                $pattern[] = $this->createPattern(self::$productId['desc'][0], self::$productId['desc'][1], $matchDesc);
                $replacement[] = $this->createReplacement($query, $matchName);
                $replacement[] = $this->createReplacement($query, $matchDesc);
                break;
            case 'mage_catalog_category':
                $matchCats = $this->getMatches(self::$categoryId[0], self::$categoryId[1], $html);
                $pattern[] = $this->createPattern(self::$categoryId[0], self::$categoryId[1], $matchCats);
                $replacement[] = $this->createReplacement($query, $matchCats);
                break;
            default:
                $matchTitle = $this->getMatches(self::$otherId['title'][0], self::$otherId['title'][1], $html);
                $matchContent = $this->getMatches(self::$otherId['content'][0], self::$otherId['content'][1], $html);
                $pattern[] = $this->createPattern(self::$otherId['title'][0], self::$otherId['title'][1], $matchTitle);
                $pattern[] = $this->createPattern(self::$otherId['content'][0], self::$otherId['content'][1], $matchContent);
                $replacement[] = $this->createReplacement($query, $matchTitle);
                $replacement[] = $this->createReplacement($query, $matchContent);
                break;
        }

        $html = $this->highlight($pattern, $replacement, $html);
        $transport->setHtml($html);

        return $this;
    }

    private function getMatches($open, $close, $subject)
    {
        preg_match_all('/.'.$open.'([^<]*)'.$close.'/i', $subject, $matches);

        return $matches[2];
    }

    private function createPattern($open, $close, $search)
    {
        foreach ($search as $i => $match) {
            $match = '/.'.$open.'('.$this->escapeSpecialChars($match).')'.$close.'/i';
            $search[$i] = $match;
        }

        return $search;
    }

    private function createReplacement($pattern, $subject)
    {
        $replacement = array();
        $arrPattern = explode(' ', $pattern);
        $replace = '${1}<span class="searchindex-highlight">${2}</span>${3}';
        foreach ($arrPattern as $pattern) {
            $pattern = '/(.*)('.$pattern.')(?![^<>]*[>])(.*)/iU';
            $replacement = preg_replace($pattern, $replace, $subject);
            $subject = $replacement;
        }

        return $replacement;
    }

    private function highlight($pattern, $replacement, $html)
    {
        foreach ($replacement as $ind => $match) {
            foreach ($match as $i => $el) {
                $el = '${1}'.$el.'${3}';
                $match[$i] = $el;
            }
            $replacement[$ind] = $match;
        }

        foreach ($pattern as $i => $search) {
            $html = preg_replace($search, $replacement[$i], $html);
        }

        return $html;
    }

    /**
     * Escape special chars in regex.
     *
     * @param string $chars
     *
     * @return string $chars
     */
    public function escapeSpecialChars($chars)
    {
        $search = array('\\', '/', '^', '[', '{', '-', '(', ')', '.', '?', '+', '|', '*');
        $replace = array('\\\\', '\/', '\^', '\[', '\{', '\-', '\(', '\)', '\.', '\?', '\+', '\|', '\*');

        return str_replace($search, $replace, $chars);
    }
}
