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


class Mirasvit_SearchSphinx_Helper_Inflect_It extends Mage_Core_Helper_Abstract
{
    static $plural = array(
    );
    static $singular = array(
        '/ci$/i'     => "",
        '/gli$/i'     => "",
        '/la$/i'     => "",
        '/le$/i'     => "",
        '/li$/i'     => "",
        '/lo$/i'     => "",
        '/mi$/i'     => "",
        '/ne$/i'     => "",
        '/si$/i'     => "",
        '/ti$/i'     => "",
        '/vi$/i'     => "",
        '/sene$/i'     => "",
        '/gliela$/i'     => "",
        '/gliele$/i'     => "",
        '/glieli$/i'     => "",
        '/glielo$/i'     => "",
        '/gliene$/i'     => "",
        '/mela$/i'     => "",
        '/mele$/i'     => "",
        '/melo$/i'     => "",
        '/mene$/i'     => "",
        '/tela$/i'     => "",
        '/tele$/i'     => "",
        '/teli$/i'     => "",
        '/telo$/i'     => "",
        '/tene$/i'     => "",
    );

    static $irregular = array(
    );

    static $uncountable = array(
    );

    /**
     * Возврашает слово во множественном числе (shoe -> shoes)
     *
     * @param  string $string
     *
     * @return string
     */
    public function pluralize($string)
    {
        // save some time in the case that singular and plural are the same
        if (in_array(strtolower($string), self::$uncountable)) {
            return $string;
        }

        // check for irregular singular forms
        foreach (self::$irregular as $pattern => $result) {
            $pattern = '/' . $pattern . '$/i';

            if (preg_match($pattern, $string)) {
                return preg_replace($pattern, $result, $string);
            }
        }

        // check for matches using regular expressions
        foreach (self::$plural as $pattern => $result ) {
            if (preg_match($pattern, $string)) {
                return preg_replace($pattern, $result, $string);
            }
        }

        return $string;
    }

    /**
     * Возврашает слово в одиночном числе (shoes -> shoe)
     *
     * @param  string $string
     *
     * @return string
     */
    public function singularize($string)
    {
        // save some time in the case that singular and plural are the same
        if (in_array(strtolower($string), self::$uncountable)) {
            return $string;
        }

        // check for irregular plural forms
        foreach (self::$irregular as $result => $pattern) {
            $pattern = '/'.$pattern.'$/i';

            if (preg_match($pattern, $string)) {
                return preg_replace($pattern, $result, $string);
            }
        }

        // check for matches using regular expressions
        foreach (self::$singular as $pattern => $result) {
            if (preg_match($pattern, $string)) {
                $sing = preg_replace($pattern, $result, $string);
                if (strlen($sing) >= 3) {
                    return $sing;
                } else {
                    return $string;
                }
            }
        }

        return $string;
    }
}