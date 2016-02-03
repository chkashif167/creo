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



class Mirasvit_SearchSphinx_Helper_Inflect_De extends Mage_Core_Helper_Abstract
{
    /**
     *  R1 and R2 regions (see the Porter algorithm).
     */
    private $R1;
    private $R1Pos;
    private $R2;
    private $R2Pos;

    private $cache = array();

    private $vowels = array('a','e','i','o','u','y','A','O','U');
    private $s_ending = array('b','d','f','g','h','k','l','m','n','r','t');
    private $st_ending = array('b','d','f','g','h','k','l','m','n','t');

    public function singularize($word)
    {
        $word = strtolower($word);

        if (!isset($this->cache[$word])) {
            $result = $this->getStem($word);
            $this->cache[$word] = $result;
        }

        return $this->cache[$word];
    }

    private function getStem($word)
    {
        $word = $this->step0a($word);
        $word = $this->step1($word);
        $word = $this->step2($word);
        $word = $this->step3($word);
        $word = $this->step0b($word);

        return $word;
    }

    /**
     *  replaces to protect some characters.
     */
    private function step0a($word)
    {
        $word = str_replace(array('ä', 'ö', 'ü'), array('A', 'O', 'U'), $word);
        $vstr = implode('', $this->vowels);
        $word = preg_replace('#(['.$vstr.'])u(['.$vstr.'])#', '$1Z$2', $word);
        $word = preg_replace('#(['.$vstr.'])y(['.$vstr.'])#', '$1Y$2', $word);

        return $word;
    }

    /**
     *   Undo the initial replaces.
     */
    private function step0b($word)
    {
        $word = str_replace(array('A', 'O', 'U', 'Y', 'Z'), array('ä', 'ö', 'ü', 'y', 'u'), $word);

        return $word;
    }

    private function step1($word)
    {
        $word = str_replace('ß', 'ss', $word);

        $this->getR($word);

        $replaceCount = 0;

        $arr = array('em','ern','er');
        foreach ($arr as $s) {
            $this->R1 = preg_replace('#'.$s.'$#', '', $this->R1, -1, $replaceCount);
            if ($replaceCount > 0) {
                $word = preg_replace('#'.$s.'$#', '', $word);
            }
        }

        $arr = array('en','es','e');
        foreach ($arr as $s) {
            $this->R1 = preg_replace('#'.$s.'$#', '', $this->R1, -1, $replaceCount);
            if ($replaceCount > 0) {
                $word = preg_replace('#'.$s.'$#', '', $word);
                $word = preg_replace('#niss$#', 'nis', $word);
            }
        }

        $word = preg_replace('/(['.implode('', $this->s_ending).'])s$/', '$1', $word);

        return $word;
    }

    private function step2($word)
    {
        $this->getR($word);

        $replaceCount = 0;

        $arr = array('est','er','en');
        foreach ($arr as $s) {
            $this->R1 = preg_replace('#'.$s.'$#', '', $this->R1, -1, $replaceCount);
            if ($replaceCount > 0) {
                $word = preg_replace('#'.$s.'$#', '', $word);
            }
        }

        if (strpos($this->R1, 'st') !== false) {
            $this->R1 = preg_replace('#st$#', '', $this->R1);
            $word = preg_replace('#(...['.implode('', $this->st_ending).'])st$#', '$1', $word);
        }

        return $word;
    }

    private function step3($word)
    {
        $this->getR($word);

        $replaceCount = 0;

        $arr = array('end', 'ung');
        foreach ($arr as $s) {
            if (preg_match('#'.$s.'$#', $this->R2)) {
                $word = preg_replace('#([^e])'.$s.'$#', '$1', $word, -1, $replaceCount);
                if ($replaceCount > 0) {
                    $this->R2 = preg_replace('#'.$s.'$#', '', $this->R2, -1, $replaceCount);
                }
            }
        }

        $arr = array('isch', 'ik', 'ig');
        foreach ($arr as $s) {
            if (preg_match('#'.$s.'$#', $this->R2)) {
                $word = preg_replace('#([^e])'.$s.'$#', '$1', $word, -1, $replaceCount);
                if ($replaceCount > 0) {
                    $this->R2 = preg_replace('#'.$s.'$#', '', $this->R2);
                }
            }
        }

        $arr = array('lich', 'heit');
        foreach ($arr as $s) {
            $this->R2 = preg_replace('#'.$s.'$#', '', $this->R2, -1, $replaceCount);
            if ($replaceCount > 0) {
                $word = preg_replace('#'.$s.'$#', '', $word);
            } else {
                if (preg_match('#'.$s.'$#', $this->R1)) {
                    $word = preg_replace('#(er|en)'.$s.'$#', '$1', $word, -1, $replaceCount);
                    if ($replaceCount > 0) {
                        $this->R1 = preg_replace('#'.$s.'$#', '', $this->R1);
                    }
                }
            }
        }

        $arr = array('keit');
        foreach ($arr as $s) {
            $this->R2 = preg_replace('#'.$s.'$#', '', $this->R2, -1, $replaceCount);
            if ($replaceCount > 0) {
                $word = preg_replace('#'.$s.'$#', '', $word);
            }
        }

        return $word;
    }

    /**
     * Find R1 and R2.
     */
    private function getR($word)
    {
        $string = str_split($word);
        $arrV = array_intersect($string, $this->vowels);

        $this->R1Pos = null;
        $this->R2Pos = null;

        // find R1/R2 positions
        for ($i = 0; $i < count($string) - 1; $i++) {
            if (isset($arrV[$i]) && !isset($arrV[$i + 1]) && $this->R1Pos === null) {
                $this->R1Pos = $i + 2;
            } elseif (isset($arrV[$i]) && !isset($arrV[$i + 1])  && $this->R1Pos) {
                $this->R2Pos = $i + 2;
                break;
            }
        }

        if ($this->R1Pos != null) {
            $this->R1 = substr($word, $this->R1Pos);
        }
        if ($this->R2Pos != null) {
            $this->R2 = substr($word, $this->R2Pos);
        }
    }
}
