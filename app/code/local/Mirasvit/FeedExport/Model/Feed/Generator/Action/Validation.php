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


class Mirasvit_FeedExport_Model_Feed_Generator_Action_Validation extends Mirasvit_FeedExport_Model_Feed_Generator_Action
{
    public function process()
    {
        $this->start();

        $feed     = $this->getFeed();
        $io       = Mage::helper('feedexport/io');
        $tmpPath  = Mage::getSingleton('feedexport/config')->getTmpPath($feed->getTmpPathKey());

        $file = $tmpPath.DS.'result.dat';

        $dom2 = new DOMDocument;
        $dom = new Mirasvit_FeedExport_Model_Feed_Generator_Action_Validatar_DOMDoc($dom2);
        $dom->load($file);
        $dom->validate();
        if (count($dom->errors)) {
            // Mage::throwException(implode(PHP_EOL, $dom->errors));
        }

        $this->finish();

        return $this;
    }
}

class Mirasvit_FeedExport_Model_Feed_Generator_Action_Validatar_DOMDoc
{
    private $_delegate;
    private $_validationErrors;

    public function __construct ($dom)
    {
        $this->_delegate = $dom;
        $this->_validationErrors = array();
    }

    public function __call($pMethodName, $pArgs)
    {
        if ($pMethodName == "validate") {
            $eh = set_error_handler(array($this, "onValidateError"));
            $rv = $this->_delegate->validate();
            if ($eh) {
                set_error_handler($eh);
            }
            return $rv;
        } elseif ($pMethodName == "load") {
            $eh = set_error_handler(array($this, "onValidateError"));
            $rv = $this->_delegate->load($pArgs[0]);
            if ($eh) {
                set_error_handler($eh);
            }
            return $rv;
        } else {
            return call_user_func_array(array($this->_delegate, $pMethodName), $pArgs);
        }
    }

    public function __get($pMemberName)
    {
        if ($pMemberName == "errors") {
            return $this->_validationErrors;
        } else {
            return $this->_delegate->$pMemberName;
        }
    }

    public function __set($pMemberName, $pValue)
    {
        $this->_delegate->$pMemberName = $pValue;
    }

    public function onValidateError($pNo, $pString, $pFile = null, $pLine = null, $pContext = null)
    {
        $error = preg_replace("/^.+: */", "", $pString);
        if ($error != 'no DTD found!') {
            $this->_validationErrors[] = $error;
        }
    }
}