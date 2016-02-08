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
 * Отвечает за работу/преобразование строк.
 *
 * @category Mirasvit
 */
class Mirasvit_Misspell_Helper_String extends Mage_Core_Helper_String
{
    public function getGram()
    {
        return 4;
    }

    public function cleanString($string)
    {
        $string = parent::cleanString($string);
        $string = preg_replace('/[^\p{L}0-9\-]/u', ' ', $string);

        return $string;
    }

    public function strtolower($string)
    {
        return strtolower($string);
    }

    public function getTrigram($keyword)
    {
        $trigram = array();
        $len = $this->strlen($keyword);

        for ($i = 1; $i < $len + $this->getGram(); $i++) {
            $trig = '';
            for ($j = $i - $this->getGram(); $j < $i; $j++) {
                if ($j >= 0 && $j < $len) {
                    $trig .= $this->substr($keyword, $j, 1);
                } else {
                    $trig .= '_';
                }
            }

            $trigram[] = $trig;
        }

        return implode(' ', $trigram);
    }
}
