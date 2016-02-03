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
abstract class Mirasvit_Misspell_Block_Abstract extends Mage_Core_Block_Template
{
    public function isSuggested()
    {
        if ($this->getOriginalQueryText()) {
            return true;
        }

        return false;
    }

    abstract public function getOriginalQueryText();

    public function getQueryUrl($query)
    {
        return Mage::getUrl('catalogsearch/result',
            array('_query' => array('q' => $query)));
    }

    public function highlight($new, $tag = 'em')
    {
        $old = $this->getOriginalQueryText();

        return trim($this->_htmlDiff($new, $old, $tag));
    }

    protected function _htmlDiff($old, $new, $tag)
    {
        $ret = '';
        $diff = $this->_diff(explode(' ', $old), explode(' ', $new));
        foreach ($diff as $k) {
            if (is_array($k)) {
                $ret .= !empty($k['i']) ? '<'.$tag.'>'.implode(' ', $k['i']).'</'.$tag.'> ' : '';
            } else {
                $ret .= $k.' ';
            }
        }

        return $ret;
    }

    protected function _diff($old, $new)
    {
        $maxlen = 0;
        foreach ($old as $oindex => $ovalue) {
            $nkeys = array_keys($new, $ovalue);
            foreach ($nkeys as $nindex) {
                $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
                    $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
                if ($matrix[$oindex][$nindex] > $maxlen) {
                    $maxlen = $matrix[$oindex][$nindex];
                    $omax = $oindex + 1 - $maxlen;
                    $nmax = $nindex + 1 - $maxlen;
                }
            }
        }

        if ($maxlen == 0) {
            return array(array('d' => $old, 'i' => $new));
        }

        return array_merge(
            $this->_diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
            array_slice($new, $nmax, $maxlen),
            $this->_diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
    }
}
