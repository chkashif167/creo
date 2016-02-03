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
 * Класс для вычесления растояния между двумя строками с учетом перестановок.
 *
 * @category Mirasvit
 */
class Mirasvit_Misspell_Model_Dameraulevenshtein extends Varien_Object
{
    // Measures the Damerau-Levenshtein distance of two words
    public function distance($str1, $str2)
    {
        $d = array();

        $len1 = Mage::helper('misspell/string')->strlen($str1);
        $len2 = Mage::helper('misspell/string')->strlen($str2);

        if ($len1 == 0) {
            return $len2;
        }

        if ($len2 == 0) {
            return $len1;
        }

        for ($i = 0; $i <= $len1; $i++) {
            $d[$i] = array();
            $d[$i][0] = $i;
        }

        for ($j = 0; $j <= $len2; $j++) {
            $d[0][$j] = $j;
        }

        for ($i = 1; $i <= $len1; $i++) {
            for ($j = 1; $j <= $len2; $j++) {
                $cost = substr($str1, $i - 1, 1) == substr($str2, $j - 1, 1) ? 0 : 1;

                $d[$i][$j] = min($d[$i - 1][$j] + 1,                 // deletion
                                $d[$i][$j - 1] + 1,                 // insertion
                                $d[$i - 1][$j - 1] + $cost          // substitution
                            );

                if ($i > 1 &&
                    $j > 1 &&
                    substr($str1, $i - 1, 1) == substr($str2, $j - 2, 1) &&
                    substr($str1, $i - 2, 1) == substr($str2, $j - 1, 1)
                ) {
                    $d[$i][$j] = min($d[$i][$j],
                                    $d[$i - 2][$j - 2] + $cost          // transposition
                                );
                }
            }
        }

        return $d[$len1][$len2];
    }

    // Case insensitive version of distance()
    public function distancei($str1, $str2)
    {
        return $this->distance(Mage::helper('misspell/string')->strtolower($str1),
            Mage::helper('misspell/string')->strtolower($str2));
    }

    // An attempt to measure word similarity in percent
    public function similarity($str1, $str2)
    {
        $len1 = Mage::helper('misspell/string')->strlen($str1);
        $len2 = Mage::helper('misspell/string')->strlen($str2);

        if ($len1 == 0 && $len2 == 0) {
            return 100;
        }

        $distance = $this->distance($str1, $str2);
        $similarity = 100 - (int) round(200 * $distance / ($len1 + $len2));

        return $similarity >= 100 ? 100 : $similarity;
    }

    // Case insensitive version of similarity()
    public function similarityi($str1, $str2)
    {
        return $this->similarity(Mage::helper('misspell/string')->strtolower($str1),
            Mage::helper('misspell/string')->strtolower($str2));
    }
}
