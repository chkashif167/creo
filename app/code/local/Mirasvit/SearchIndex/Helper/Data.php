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



class Mirasvit_SearchIndex_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getSearchEngine()
    {
        $engine = null;

        if (Mage::helper('mstcore')->isModuleInstalled('Mirasvit_SearchSphinx')) {
            $engine = Mage::helper('searchsphinx')->getEngine();
        } elseif (Mage::helper('core')->isModuleEnabled('Mirasvit_SearchShared')) {
            $engine = Mage::getSingleton('searchshared/engine_fulltext');
        }

        return $engine;
    }

    public function prepareString($string)
    {
        $string = strip_tags($string);
        $string = str_replace('|', ' ', $string);
        $string = ' '.$string.' ';

        $expressions = Mage::getSingleton('searchindex/config')->getMergeExpressins();

        foreach ($expressions as $expr) {
            $matches = null;
            preg_match_all($expr['match'], $string, $matches);

            foreach ($matches[0] as $math) {
                $math = preg_replace($expr['replace'], $expr['char'], $math);
                $string .= ' '.$math;
            }
        }

        return $string;
    }
}
