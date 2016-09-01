<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */
class Amasty_Checkoutfees_IndexController extends Mage_Core_Controller_Front_Action
{

    public function saveFormDataAction()
    {
        if (!Mage::getStoreConfig('amcheckoutfees/general/enabled')) {
            echo 0;

            return false;
        }

        // prepare data
        $data = array();
        $params = Mage::app()->getRequest()->getParams();

        // extract params from filtered POST
        if (is_array($params) && count($params) > 0) {
            foreach ($params as $param => $value) {
                if (strpos($param, 'amcheckoutfees_') !== false) {
                    $paramData = explode('_', $param);
                    $paramId   = isset($paramData[1]) ? $paramData[1] : 0;
                    if ($paramId > 0) {
                        if (is_array($value)) {
                            $data[$paramId] = implode(',', array_values($value));
                        } else {
                            $data[$paramId] = $value;
                        }
                    }
                }
            }
            $data  = serialize($data);
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $quote->setData('amcheckoutfees_fees', $data);
            $quote->save();
            $quote->setTotalsCollectedFlag(false)->collectTotals();
        }

        if (!Mage::app()->getRequest()->getParam('ajax')) {
            $redirectUrl = $_SERVER['HTTP_REFERER'];
            Mage::app()->getResponse()->setRedirect($redirectUrl)->sendResponse();
        }
        return true;
    }
}