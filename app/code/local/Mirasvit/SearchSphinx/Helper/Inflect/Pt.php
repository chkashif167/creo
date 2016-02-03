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



class Mirasvit_SearchSphinx_Helper_Inflect_Pt extends Mage_Core_Helper_Abstract
{
    public static $plural = array(
    );

    public static $singular = array(
        '/eza$/i' => '',
        '/ezas$/i' => '',
        '/ico$/i' => '',
        '/ica$/i' => '',
        '/icos$/i' => '',
        '/icas$/i' => '',
        '/ismo$/i' => '',
        '/ismos$/i' => '',
        '/ável$/i' => '',
        '/ível$/i' => '',
        '/ista$/i' => '',
        '/istas$/i' => '',
        '/oso$/i' => '',
        '/osa$/i' => '',
        '/osos$/i' => '',
        '/osas$/i' => '',
        '/amento$/i' => '',
        '/amentos$/i' => '',
        '/imento$/i' => '',
        '/imentos$/i' => '',
        '/adora$/i' => '',
        '/ador$/i' => '',
        '/adoras$/i' => '',
        '/adores$/i' => '',
        '/ante$/i' => '',
        '/antes$/i' => '',
        '/ador$/i' => '',
        '/ância$/i' => '',
        '/ado$/i' => '',
        '/ais$/i' => '',
        '/ando$/i' => '',
        '/ara$/i' => '',
        '/aram$/i' => '',
        '/aras$/i' => '',
        '/arei$/i' => '',
        '/arem$/i' => '',
        '/aria$/i' => '',
        '/ariam$/i' => '',
        '/arias$/i' => '',
        '/armos$/i' => '',
        '/asse$/i' => '',
        '/assem$/i' => '',
        '/asses$/i' => '',
        '/aste$/i' => '',
        '/astes$/i' => '',
        '/ava$/i' => '',
        '/avam$/i' => '',
        '/eis$/i' => '',
        '/endo$/i' => '',
        '/era$/i' => '',
        '/eram$/i' => '',
        '/eras$/i' => '',
        '/erei$/i' => '',
        '/erem$/i' => '',
        '/eria$/i' => '',
        '/eriam$/i' => '',
        '/erias$/i' => '',
        '/ermos$/i' => '',
        '/esse$/i' => '',
        '/essem$/i' => '',
        '/esses$/i' => '',
        '/este$/i' => '',
        '/estes$/i' => '',
        '/heten$/i' => '',
        '/ias$/i' => '',
        '/ida$/i' => '',
        '/idas$/i' => '',
        '/ido$/i' => '',
        '/ima$/i' => '',
        '/imos$/i' => '',
        '/indo$/i' => '',
        '/ira$/i' => '',
        '/iram$/i' => '',
        '/irei$/i' => '',
        '/irem$/i' => '',
        '/ires$/i' => '',
        '/iria$/i' => '',
        '/iriam$/i' => '',
        '/irias$/i' => '',
        '/irmos$/i' => '',
        '/isse$/i' => '',
        '/issem$/i' => '',
        '/isses$/i' => '',
        '/iste$/i' => '',
        '/istes$/i' => '',
        '/am$/i' => '',
        '/ar$/i' => '',
        '/ar$/i' => '',
        '/as$/i' => '',
        '/ei$/i' => '',
        '/em$/i' => '',
        '/en$/i' => '',
        '/er$/i' => '',
        '/es$/i' => '',
        '/eu$/i' => '',
        '/ia$/i' => '',
        '/ir$/i' => '',
        '/is$/i' => '',
        '/iu$/i' => '',
        '/os$/i' => '',
        '/ou$/i' => '',
        '/e$/i' => '',
        '/o$/i' => '',
    );

    public static $irregular = array(
    );

    public static $uncountable = array(
    );

    /**
     * Возврашает слово во множественном числе (shoe -> shoes).
     *
     * @param string $string
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
            $pattern = '/'.$pattern.'$/i';

            if (preg_match($pattern, $string)) {
                return preg_replace($pattern, $result, $string);
            }
        }

        // check for matches using regular expressions
        foreach (self::$plural as $pattern => $result) {
            if (preg_match($pattern, $string)) {
                return preg_replace($pattern, $result, $string);
            }
        }

        return $string;
    }

    /**
     * Возврашает слово в одиночном числе (shoes -> shoe).
     *
     * @param string $string
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
