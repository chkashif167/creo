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


class Mirasvit_MstCore_Helper_Parsevariables extends Mage_Core_Helper_Abstract
{
  	//e.g. of str
    //[product_name][, model: {product_model}!] [product_nonexists]  [buy it {product_nonexists} !]
	public function parse($str, $objects, $additional = array(), $storeId = false)
    {
		if (trim($str) == '') {
			return null;
		}

        $b1Open  = '[ZZZZZ';
        $b1Close = 'ZZZZZ]';
        $b2Open  = '{WWWWW';
        $b2Close = 'WWWWW}';

        $str = str_replace('[', $b1Open, $str);
        $str = str_replace(']', $b1Close, $str);
        $str = str_replace('{', $b2Open, $str);
        $str = str_replace('}', $b2Close, $str);

        $pattern = '/\[ZZZZZ[^ZZZZZ\]]*ZZZZZ\]/';

        preg_match_all($pattern, $str, $matches, PREG_SET_ORDER);

        $vars = array();
        foreach ($matches as $matche) {
            $vars[$matche[0]] = $matche[0];
        }

		foreach ($objects as $key => $object) {
            $data = $object->getData();
            if (isset($additional[$key])) {
                $data = array_merge($data, $additional[$key]);
            }

			foreach ($data as $dataKey => $value) {
				if (is_array($value) || is_object($value)) {
					continue;
				}

                $k1   = $b2Open.$key.'_'.$dataKey.$b2Close;
                $k2   = $b1Open.$key.'_'.$dataKey.$b1Close;
                $skip = true;

                foreach ($vars as $k =>$v) {
                    if (stripos($v, $k1) !== false || stripos($v, $k2) !== false) {
                        $skip = false;
                        break;
                    }
                }

                if ($skip) {
                    continue;
                }

                $value = $this->checkForConvert($object, $key, $dataKey, $value, $storeId);
                foreach ($vars as $k =>$v) {
                    if ($value == '') {
                        if (stripos($v, $k1) !== false || stripos($v, $k2) !== false) {
                            $vars[$k] = '';
                            continue;
                        }
                    }

    				$v = str_replace($k1, $value, $v);
    				$v = str_replace($k2, $value, $v);
    				$vars[$k] = $v;
                }
			}
        }

		foreach ($vars as $k => $v) {
			//if no attibute like [product_nonexists]
			if ($v == $k) {
				$v = '';
            }

            //remove start and end symbols from the string (trim)
            if (substr($v, 0, strlen($b1Open)) == $b1Open) {
                    $v = substr($v, strlen($b1Open), strlen($v));
            }

            if (strpos($v, $b1Close) === strlen($v)-strlen($b1Close)) {
                $v = substr($v, 0, strlen($v)-strlen($b1Close));
            }

		    //if no attibute like [buy it {product_nonexists} !]
		    if (stripos($v, $b2Open) !== false || stripos($v, $b1Open) !== false) {
                $v = '';
		    }

            $str = str_replace($k, $v, $str);
		}

		return $str;
	}

	protected function checkForConvert($object, $key, $dataKey, $value, $storeId)
    {
        if ($key == 'product' || $key == 'category') {
            if ($key == 'product') {
                $attribute = Mage::getSingleton('catalog/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, $dataKey);
            } else {
                $attribute = Mage::getSingleton('catalog/config')->getAttribute(Mage_Catalog_Model_Category::ENTITY, $dataKey);
            }

            if ($storeId) {
                $attribute->setStoreId($storeId);
            }

            if ($attribute->getId() > 0) {
                try {
                    $valueId = $object->getDataUsingMethod($dataKey);
                    $value = $attribute->getFrontend()->getValue($object);
                } catch(Exception $e) {//possible that some extension is removed, but we have it attribute with source in database
                    $value = '';
                }

                if ($value == 'No' && $valueId == '') {
                    $value = '';
                }

                switch ($dataKey) {
                    case 'price':
                        $value = Mage::helper('core')->currency($value, true, false);
                        break;
                    case 'special_price':
                        $value = Mage::helper('core')->currency($value, true, false);
                        break;
                }
	        } else {
                switch ($dataKey) {
                    case 'final_price':
                        $value = Mage::helper('core')->currency($value, true, false);
                        break;
                }
            }
        }

        if (is_array($value)) {
           if (isset($value['label'])) {
               $value = $value['label'];
           } else {
               $value = '';
           }
        }

	    return $value;
	}

}
