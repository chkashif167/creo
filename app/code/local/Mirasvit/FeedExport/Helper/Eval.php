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
 * @package   Advanced Product Feeds
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_FeedExport_Helper_Eval extends Mage_Core_Helper_Abstract
{
    public function execute($value, $formatterLine)
    {
        $formatter = explode(' ', $formatterLine);
        $method = $formatter[0];

        array_shift($formatter);
        $args = $formatter;
        if (!is_array($args)) {
            $args = array();
        }

        if (function_exists($method)) {
            foreach ($args as $key => $arg) {
                if (!is_numeric($arg)) {
                    $args[$key] = "'".$arg."'";
                }
            }

            $cmd = 'return '.$method.'("'.addslashes($value).'"';

            if (count($args)) {
                $cmd .= ','.implode(',', $args).'';
            }

            $cmd .= ');';

            $value = @eval($cmd);
            $value = stripcslashes($value);
        } elseif (method_exists($this, $method)) {
            array_unshift($args, $value);
            $value = call_user_func_array(array($this, $method), $args);
        } else {
            $value .= $formatterLine;
        }

        return $value;
    }

    public function convert($value, $currency)
    {
        $value = Mage::helper('directory')->currencyConvert($value, Mage::app()->getStore()->getBaseCurrencyCode(), $currency);
        $value = number_format($value, 2, '.', '');

        return $value;
    }

    public function csvPretty($value, $delimiter)
    {
        if ($delimiter == 'tab') {
            $delimiter = "\t";
        }

        $value = str_replace("\n", ' ', $value);
        $value = str_replace("\r", '', $value);
        $value = str_replace("\t", '', $value);
        $value = str_replace($delimiter, ' ', $value);

        return $value;
    }

    public function html2plain($value)
    {
        // 194 -> 32
        $value = str_replace(' ', ' ', $value);

        $value = strip_tags($value);

        $value = str_replace('\\\'', '\'', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        //{{block type="cms/block" block_id="product-3-in-1" template="cms/content.phtml"}}
        $value = preg_replace('/({{.*}})/is', '', $value);

        return $value;
    }

    public function if_not_empty($value)
    {
        if (trim($value) == '' || $value == false) {
            $value = '###TOREMOVE###';
        }

        return $value;
    }

    public function urlToUnsecure($value)
    {
        return str_replace('https', 'http', $value);
    }

    public function urlToSecure($value)
    {
        return str_replace('http', 'https', $value);
    }

    /**
     * Substr string, but first convert all chars into HTML entities and decode them back before returning
     * Allows to avoid problems with substr and chars
     *
     * @param  string  $value
     * @param  int     $limit - length
     * @return string  $value
     */
    public function limit($value, $limit)
    {
        return html_entity_decode(substr(htmlentities($value), 0, $limit));
    }

    /**
     * Remove all non-utf-8 characters from string
     *
     * @param  string $value
     * @return string $value
     */
    public function clear($value)
    {
        $value = preg_replace('/[^(\x20-\x7F)]*/','', $value);
        $value = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
         '|[\x00-\x7F][\x80-\xBF]+'.
         '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
         '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
         '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
         '', $value );
        $value = preg_replace('/\xE0[\x80-\x9F][\x80-\xBF]'.
         '|\xED[\xA0-\xBF][\x80-\xBF]/S','', $value );

        return $value;
    }
}