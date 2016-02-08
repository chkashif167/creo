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
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_EmailDesign_Helper_CssToInline extends Mage_Core_Helper_Abstract
{
    private $html = '';
    private $css = '';
    private $unprocessableHTMLTags = array('wbr');

    public function __construct($html = '', $css = '')
    {
        $this->html = $html;
        $this->css  = $css;
    }

    public function setHtml($html = '')
    {
        $this->html = $html;
    }

    public function setCss($css = '')
    {
        $this->css = $css;
    }

    public function addUnprocessableHTMLTag($tag)
    {
        $this->unprocessableHTMLTags[] = $tag;
    }

    public function removeUnprocessableHTMLTag($tag)
    {
        if (($key = array_search($tag, $this->unprocessableHTMLTags)) !== false) {
            unset($this->unprocessableHTMLTags[$key]);
        }
    }


    public function css2inline() 
    {
        $body = $this->html;

        if (count($this->unprocessableHTMLTags)) {
            $unprocessableHTMLTags = implode('|',$this->unprocessableHTMLTags);
            $body = preg_replace("/<($unprocessableHTMLTags)[^>]*>/i",'',$body);
        }

        libxml_use_internal_errors(true);
        $xmldoc = new DOMDocument;
        $xmldoc->strictErrorChecking = false;
        $xmldoc->formatOutput = true;
        $xmldoc->loadHTML($body);
        $xmldoc->normalizeDocument();

        $xpath = new DOMXPath($xmldoc);

        $nodes = @$xpath->query('//*[@style]');
        if ($nodes->length > 0) {
            foreach ($nodes as $node) {
                $node->setAttribute('style', @preg_replace('/[A-z\-]+(?=\:)/Se', "strtolower('\\0')", $node->getAttribute('style')));
            }
        }

        $reCommentCss = '/\/\*.*\*\//sU';
        $css = preg_replace($reCommentCss, '', $this->css);

        // process the CSS file for selectors and definitions
        $reCss = '/^\s*([^{]+){([^}]+)}/mis';
        preg_match_all($reCss, $css, $matches);

        $allSelectors = array();
        foreach ($matches[1] as $key => $selectorString) {
            if (!strlen(trim($matches[2][$key]))) {
                continue;
            }

            $selectors = explode(',',$selectorString);
            foreach ($selectors as $selector) {
                if (strpos($selector,':') !== false) {
                    continue;
                }
                $allSelectors[] = array(
                    'selector'   => $selector,
                    'attributes' => $matches[2][$key],
                    'index'      => $key,
                );
            }
        }

        usort($allSelectors, array('self','sortBySelectorPrecedence'));

        foreach ($allSelectors as $value) {
            $nodes = $xpath->query($this->translateCSStoXpath(trim($value['selector'])));

            foreach($nodes as $node) {
                if ($node->hasAttribute('style')) {
                    $oldStyleArr = $this->cssStyleDefinitionToArray($node->getAttribute('style'));
                    $newStyleArr = $this->cssStyleDefinitionToArray($value['attributes']);

                    $combinedArr = array_merge($oldStyleArr,$newStyleArr);
                    $style = '';
                    foreach ($combinedArr as $k => $v) {
                        $style .= (strtolower($k) . ':' . $v . ';');
                    }
                } else {
                    $style = trim($value['attributes']);
                }
                $node->setAttribute('style',$style);
            }
        }

        $nodes = $xpath->query('//*[contains(translate(@style," ",""),"display:none")]');
        foreach ($nodes as $node) {
            $node->parentNode->removeChild($node);
        }

        return $xmldoc->saveHTML();
    }

    private static function sortBySelectorPrecedence($a, $b)
    {
        $precedenceA = self::getCSSSelectorPrecedence($a['selector']);
        $precedenceB = self::getCSSSelectorPrecedence($b['selector']);

        // we want these sorted ascendingly so selectors with lesser precedence get processed first and
        // selectors with greater precedence get sorted last
        return ($precedenceA == $precedenceB) ? ($a['index'] < $b['index'] ? -1 : 1) : ($precedenceA < $precedenceB ? -1 : 1);
    }

    private static function getCSSSelectorPrecedence($selector)
    {
        $precedence = 0;
        $value = 100;
        $search = array('\#','\.',''); // ids: worth 100, classes: worth 10, elements: worth 1

        foreach ($search as $s) {
            if (trim($selector == '')) break;
            $num = 0;
            $selector = preg_replace('/'.$s.'\w+/','',$selector,-1,$num);
            $precedence += ($value * $num);
            $value /= 10;
        }

        return $precedence;
    }

    private function translateCSStoXpath($css_selector)
    {
        $search = array(
                           '/\s+>\s+/', // Matches any F element that is a child of an element E.
                           '/(\w+)\s+\+\s+(\w+)/', // Matches any F element that is a child of an element E.
                           '/\s+/', // Matches any F element that is a descendant of an E element.
                           '/(\w+)?\#([\w\-]+)/e', // Matches id attributes
                           '/(\w+|\*)?((\.[\w\-]+)+)/e', // Matches class attributes
        );
        $replace = array(
                           '/',
                           '\\1/following-sibling::*[1]/self::\\2',
                           '//',
                           "(strlen('\\1') ? '\\1' : '*').'[@id=\"\\2\"]'",
                           "(strlen('\\1') ? '\\1' : '*').'[contains(concat(\" \",@class,\" \"),concat(\" \",\"'.implode('\",\" \"))][contains(concat(\" \",@class,\" \"),concat(\" \",\"',explode('.',substr('\\2',1))).'\",\" \"))]'",
        );
        return '//'.@preg_replace($search,$replace,trim($css_selector));
    }

    private function cssStyleDefinitionToArray($style)
    {
        $definitions = explode(';',$style);
        $retArr = array();
        foreach ($definitions as $def) {
            if (empty($def) || strpos($def, ':') === false) continue;
            list($key,$value) = explode(':',$def,2);
            if (empty($key) || empty($value)) continue;
            $retArr[trim($key)] = trim($value);
        }
        return $retArr;
    }
}