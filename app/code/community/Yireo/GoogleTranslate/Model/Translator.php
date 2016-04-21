<?php
/**
 * Yireo GoogleTranslate for Magento
 *
 * @package     Yireo_GoogleTranslate
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * GoogleTranslate observer
 */
class Yireo_GoogleTranslate_Model_Translator extends Mage_Core_Model_Abstract
{
    /**
     * String containing the API URL
     *
     * @var string
     */
    protected $apiUrl = 'https://www.googleapis.com/language/translate/v2';

    /**
     * Container for possible API errors
     *
     * @var null
     */
    protected $apiError = null;

    /**
     * String containing the translated content received from the API
     *
     * @var null
     */
    protected $apiTranslation = null;

    /**
     * Method to call upon the Bing API
     *
     * @param string $text
     * @param string $fromLang
     * @param string $toLang
     * @return string
     */
    public function translate($text = null, $fromLang = null, $toLang = null)
    {
        // Load text from data-object
        $text = trim($text);
        if (empty($text)) {
            $text = $this->getData('text');
        }

        // Return empty text
        $text = trim($text);
        if (empty($text)) {
            $this->apiError = $this->__('Empty text in translation request');
            return false;
        }

        // Disable translating
        if (Mage::getStoreConfig('catalog/googletranslate/skip_translation')) {
            $this->apiError = $this->__('API-translation is disabled through setting');
            return false;
        }

        // Bork debugging
        if (Mage::getStoreConfig('catalog/googletranslate/bork')) {
            $this->apiTranslation = $this->bork($text);
            return $this->apiTranslation;
        }

        // Demo
        $apiKey = Mage::helper('googletranslate')->getApiKey2();
        if (strtolower($apiKey) == 'demo') {
            $this->apiError = $this->__('API-translation is disabled for this demo');
            return false;
        }

        // Load some variables
        if (empty($text)) {
            $text = $this->getData('text');
        }

        if (empty($fromLang)) {
            $fromLang = $this->getData('from');
        }

        if (empty($toLang)) {
            $toLang = $this->getData('toLang');
        }

        // Exception when toLang is wrong
        if (empty($toLang) || $toLang == 'auto') {
            $this->apiError = $this->__('Translation-target is wrong [' . $toLang . ']');
            return false;
        }

        // Dispatch an event
        Mage::dispatchEvent('content_translate_before', array('text' => &$text, 'from' => $fromLang, 'to' => $toLang));

        $apiKey = Mage::helper('googletranslate')->getApiKey2();
        $headers = array();

        // Google API fields
        $post_fields = array(
            'key' => $apiKey,
            'target' => $toLang,
            'source' => $fromLang,
            'format' => 'html',
            'prettyprint' => '1',
            'q' => $text,
        );
        //Mage::helper('googletranslate')->log('GoogleTranslate debug request', $post_fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
        curl_setopt($ch, CURLOPT_USERAGENT, 'Magento/PHP');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_REFERER, Mage::helper('core/url')->getCurrentUrl());
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: GET'));
        $result = curl_exec($ch);

        // Detect an empty CURL response
        if (empty($result)) {
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($status_code > 200) {
                $this->apiError = Mage::helper('googletranslate')->__('Empty response: HTTP %s', $status_code);
                $this->apiError .= ' [' . Mage::helper('googletranslate')->__('From %s to %s', $fromLang, $toLang) . ']';
                return false;
            } else {
                $test = curl_init();
                curl_setopt($test, CURLOPT_URL, 'https://www.googleapis.com/');
                curl_setopt($test, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($test, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($test, CURLOPT_SSL_VERIFYPEER, false);
                $result = curl_exec($test);
                if (empty($result)) {
                    $this->apiError = Mage::helper('googletranslate')->__('Empty response: Firewall blocking: %s', curl_error($test));
                    return false;
                } else {
                    $this->apiError = Mage::helper('googletranslate')->__('Empty response: CURL-error %s', curl_error($ch));
                    return false;
                }
            }
        }

        // Detect HTML feedback
        if (preg_match('/\<\/html\>$/', $result)) {
            $this->apiError = Mage::helper('googletranslate')->__('Response is HTML, not JSON');
            $this->apiError .= ' [' . Mage::helper('googletranslate')->__('From %s to %s', $fromLang, $toLang) . ']';
            return false;
        }

        // Detect non-JSON feedback
        if (!preg_match('/^\{/', $result)) {
            $this->apiError = Mage::helper('googletranslate')->__('Not a JSON response');
            $this->apiError .= ' [' . Mage::helper('googletranslate')->__('From %s to %s', $fromLang, $toLang) . ']';
            return false;
        }

        // Decode the JSON-data
        $json = json_decode($result, true);
        if (isset($json['data']['translations'][0]['translatedText'])) {
            $translation = trim($json['data']['translations'][0]['translatedText']);

            // Empty translation
            if (empty($translation)) {
                $this->apiError = Mage::helper('googletranslate')->__('Empty translation');
                $this->apiError .= ' [' . Mage::helper('googletranslate')->__('From %s to %s', $fromLang, $toLang) . ']';
                return false;

                // Detect whether the translation was the same or not
            } elseif ($translation == $text) {
                $this->apiError = Mage::helper('googletranslate')->__('Translation resulted in same text');
                $this->apiError .= ' [' . Mage::helper('googletranslate')->__('From %s to %s', $fromLang, $toLang) . ']';
                return false;

                // Send the translation
            } else {

                // Dispatch an event
                Mage::dispatchEvent('content_translate_after', array('text' => &$translation, 'from' => $fromLang, 'to' => $toLang));

                $this->apiTranslation = $translation;
                return $this->apiTranslation;
            }
        }

        // Detect errors and send them as feedback
        if (isset($json['error']['errors'][0]['message'])) {
            $this->apiError = Mage::helper('googletranslate')->__('GoogleTranslate message: %s', var_export($json['error']['errors'][0]['message'], true));
            $this->apiError .= ' [' . Mage::helper('googletranslate')->__('From %s to %s', $fromLang, $toLang) . ']';
            return false;
        }

        $this->apiError = Mage::helper('googletranslate')->__('Unknown data');
        $this->apiError .= ' [' . Mage::helper('googletranslate')->__('From %s to %s', $fromLang, $toLang) . ']';
        $this->apiError .= "\n" . var_export($json, true);
        return false;
    }

    /**
     * Method to check whether there has been an error in the API
     *
     * @return bool
     */
    public function hasApiError()
    {
        if (!empty($this->apiError)) {
            return true;
        }
        return false;
    }

    /**
     * Method to return the API error, if any
     *
     */
    public function getApiError()
    {
        return $this->apiError;
    }

    /**
     * Method to return the API translation
     *
     * @return string
     */
    public function getApiTranslation()
    {
        return $this->apiTranslation;
    }

    /**
     * Method to write some debugging to a log
     *
     * @param $string
     * @param $fromLang
     * @param $toLang
     * @return void
     */
    public function debugLog($string, $fromLang, $toLang)
    {
        if (!is_dir(BP . DS . 'var' . DS . 'log')) {
            mkdir(BP . DS . 'var' . DS . 'log');
        }
        
        $tmp_file = BP . DS . 'var' . DS . 'log' . DS . 'googletranslate.log';
        $tmp_string = $this->__('Translating from %s to %s', $fromLang, $toLang);
        
        file_put_contents($tmp_file, $tmp_string . "\n", FILE_APPEND);
        file_put_contents($tmp_file, $string . "\n", FILE_APPEND);
    }

    /**
     * Method to translate a certain text
     *
     * @param $string
     *  $variable1
     *  $variable2
     * @return string
     */
    public function __($string, $variable1 = null, $variable2 = null)
    {
        return Mage::helper('googletranslate')->__($string, $variable1, $variable2);
    }

    /**
     * Method to borkify a given text
     *
     * @param $text
     * @return mixed|string
     */
    public function bork($text)
    {
        $textBlocks = preg_split('/(%[^ ]+)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $newTextBlocks = array();

        foreach ($textBlocks as $text) {
            if (strlen($text) && $text[0] == '%') {
                $newTextBlocks[] = (string)$text;
                continue;
            }

            $originalText = $text;
            $searchMap = array(
                '/au/', '/\Bu/', '/\Btion/', '/an/', '/a\B/', '/en\b/',
                '/\Bew/', '/\Bf/', '/\Bir/', '/\Bi/', '/\bo/', '/ow/', '/ph/',
                '/th\b/', '/\bU/', '/y\b/', '/v/', '/w/', '/oo/', '/oe/'
            );
            $replaceMap = array(
                'oo', 'oo', 'shun', 'un', 'e', 'ee',
                'oo', 'ff', 'ur', 'ee', 'oo', 'oo', 'f',
                't', 'Oo', 'ai', 'f', 'v', 'ø', 'œ',
            );

            $text = preg_replace($searchMap, $replaceMap, $text);
            if ($originalText == $text && count($newTextBlocks)) {
                $text .= '-a';
            }

            if (empty($text)) {
                $text = $originalText;
            }

            $newTextBlocks[] = (string)$text;
        }

        $text = implode('', $newTextBlocks);
        $text = preg_replace('/([:.?!])(.*)/', '\\2\\1', $text);
        //$text .= '['.$this->getData('toLang').']';

        return $text;
    }
}
