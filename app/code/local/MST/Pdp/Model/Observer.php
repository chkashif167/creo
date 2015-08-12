<?php
/**
 * Magento Support Team.
 * @category   MST
 * @package    MST_Pdp
 * @version    2.0
 * @author     Magebay Developer Team <info@magebay.com>
 * @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
 */
class MST_Pdp_Model_Observer extends Varien_Object
{
    public function catalogProductLoadAfter(Varien_Event_Observer $observer)
    {
        // set the additional options on the product
        $action = Mage::app()->getFrontController()->getAction();
        if ($action->getFullActionName() == 'checkout_cart_add')
        {
            // assuming you are posting your custom form values in an array called extra_options...
            $extraOption = $action->getRequest()->getParam('extra_options');
            if ($extraOption != "")
            {
                $product = $observer->getProduct();
                // add to the additional options array
                $additionalOptions = array();
                if ($additionalOption = $product->getCustomOption('additional_options'))
                {
                    $additionalOptions = (array) unserialize($additionalOption->getValue());
                }
                $additionalOptions[] = array(
                    'code' => 'pdpinfo',
                    'label' => '',
                    'value' => '',
                    'json' => $extraOption,
                	'time' => microtime()
                );
                // add the additional options array with the option code additional_options
                $observer->getProduct()
                    ->addCustomOption('additional_options', serialize($additionalOptions));
            }
        }
    }
    public function quoteProductAddAfter(Varien_Event_Observer $observer) {
        /*$action = Mage::app()->getFrontController()->getAction();
        $params = $action->getRequest()->getParams();
        $items = $observer->getItems();
        foreach ($items as $item) {
            if ($item->getItemId() == $params['id']) {
                $options = $item->getOptions();
                foreach ($options as $option){
                    if($option->getCode()=='info_buyRequest'){
                        $buyRequestInfo = unserialize($option->getValue());
                        $buyRequestInfo['extra_options'] = $params['extra_options'];
                        $option->setValue(serialize($buyRequestInfo));
                    }
                }
                $additionalOptions[] = array(
                    'code' => 'pdpinfo',
                    'label' => 'Update Option',
                    'value' => 'lalala',
                    'json' => $buyRequestInfo['extra_options']
                );
                $item->setProductOptions($additionalOptions);
                $item->save();
            }
        }*/
        //die('Cart Will Not Update Right Now');
    }
    /**Update extra_option value in buy request Æ°hen edit cart item**/
    public function quoteItemSetProduct(Varien_Event_Observer $observer) {
        $action = Mage::app()->getFrontController()->getAction();
        $extraOption = $action->getRequest()->getParam('extra_options');
        $itemId = $action->getRequest()->getParam('id');
        $moduleName = Mage::app()->getRequest()->getModuleName();
        $actionName = Mage::app()->getRequest()->getActionName();
        if ($moduleName == "checkout" && $actionName == "updateItemOptions") {
            $item = $observer->getQuoteItem();
            Zend_Debug::dump(get_class_methods($item));
            die;
        }
    }
    public function salesConvertQuoteItemToOrderItem(Varien_Event_Observer $observer)
    {
        $quoteItem = $observer->getItem();
        if ($additionalOptions = $quoteItem->getOptionByCode('additional_options')) {
            $orderItem = $observer->getOrderItem();
            $options = $orderItem->getProductOptions();
            $options['additional_options'] = unserialize($additionalOptions->getValue());
            $orderItem->setProductOptions($options);
        }
    }
    public function checkoutCartProductAddAfter(Varien_Event_Observer $observer)
    {
        $action = Mage::app()->getFrontController()->getAction();
        if ($action->getFullActionName() == 'sales_order_reorder')
        {
            $item = $observer->getQuoteItem();
            $buyInfo = $item->getBuyRequest();
            if ($options = $buyInfo->getExtraOptions())
            {
                $additionalOptions = array();
                if ($additionalOption = $item->getOptionByCode('additional_options'))
                {
                    $additionalOptions = (array) unserialize($additionalOption->getValue());
                }
                foreach ($options as $key => $value)
                {
                    $additionalOptions[] = array(
                        'label' => $key,
                        'value' => $value,
                    );
                }
                $item->addOption(array(
                    'code' => 'additional_options',
                    'value' => serialize($additionalOptions)
                ));
            }
        }
    }
    public function updatePrice($observer) {
    	$event = $observer->getEvent();
    	$quote_item = $event->getQuoteItem();
		//Should follow json
        /*$isShowPrice = Mage::helper("pdp")->isShowPricePanel($quote_item->getProduct()->getId());
		if (!$isShowPrice) {
			return;
		}*/
		$_product = $quote_item->getProduct();
		$buyInfo = $quote_item->getBuyRequest();
		$options = $buyInfo->getData();
    	$extraPrice = 0;
    	if (isset($options['extra_options'])) {
    		$extraPrice = Mage::helper("pdp")->getPDPExtraPrice($options['extra_options']);
    		if ($extraPrice != 0) {
    			//Cal final price of product include special price, tier price, group price and custom option price
    			//Group price, special price will same as final price, but tier price is different
    			$itemQty = (int) $quote_item->getBuyRequest()->getData('original_qty');
    			$finalPrice = $_product->getFinalPrice();
    			if ($itemQty > 0 && $_product->getTierPriceCount() > 0) {
    				$tierPrices = $_product->getTierPrice();
    				foreach ($tierPrices as $_tierPrice) {
    					//Make sure item has enought qty to cal tier price
    					if($itemQty >= (int) $_tierPrice['price_qty']) {
    						$tierPrices = $_product->getTierPrice($itemQty);
    						break;
    					}
    				}
    				if (!is_array($tierPrices) && $tierPrices < $finalPrice) {
    					$customOptionPrice = $this->getOptionPrice($_product, $itemQty, $options);
    					$finalPrice = $tierPrices + $customOptionPrice;
    				}
    			}
    			$quote_item->setOriginalCustomPrice($finalPrice + $extraPrice);
    			//$quote_item->save();
    		}
    	}
    	return;
    }
    public function getOptionPrice($_product, $itemQty, $options) {
    	$customOptionsPrice = 0;
    	$tierPrices = $_product->getTierPrice();
    	$finalPrice = $_product->getFinalPrice();
    	foreach ($tierPrices as $_tierPrice) {
    		//Make sure item has enought qty to cal tier price
    		if($itemQty >= (int) $_tierPrice['price_qty']) {
    			$tierPrices = $_product->getTierPrice($itemQty);
    			break;
    		}
    	}
    	if (!is_array($tierPrices) && $tierPrices < $finalPrice) {
    		$basePricesForPercent = array();
    		$basePricesForPercent[] = floatval($_product->getPrice());
    		if ($_product->getGroupPrice()) {
    			$basePricesForPercent[] = floatval($_product->getGroupPrice());
    		}
    		if ($_product->getSpecialPrice()) {
    			$basePricesForPercent[] = floatval($_product->getSpecialPrice());
    		}
    		$_finalPriceForPercent = min($basePricesForPercent);
    		//Tier price not include custom option price, so, need to add custom option price
    		if (isset($options['super_attribute'])) {
    			$_attributes = $_product->getTypeInstance(true)->getConfigurableAttributes($_product);
    			foreach($_attributes as $_attribute){
    				if (isset($_attribute['prices'])) {
    					foreach($_attribute['prices'] as $_priceOption) {
    						if (in_array($_priceOption['value_index'], $options['super_attribute'])) {
    							if ($_priceOption['pricing_value']) {
    								if ($_priceOption['is_percent'] == 0) {
    									$customOptionsPrice += floatval($_priceOption['pricing_value']);
    								} else {
    									$realPrice = ($_priceOption['pricing_value'] * $_finalPriceForPercent) / 100;
    									$customOptionsPrice += floatval($realPrice);
    								}
    							}
    						}
    					}
    				}
    			}
    		} elseif (isset($options['options'])) {
    			if ($_product->hasCustomOptions()) {
    				$customOptions = $_product->getOptions();
    				foreach($customOptions as $_option) {
    					foreach ($_option->getValues() as $option) {
    						if (in_array($option['option_type_id'], $options['options'])) {
    							if ($option['price_type'] == "fixed") {
    								$customOptionsPrice += floatval($option['price']);
    							} else {
    								$realPrice = ($option['price'] * $_finalPriceForPercent) / 100;
    								$customOptionsPrice += floatval($realPrice);
    							}
    						}
    					}
    				}
    			}
    			$customOptionsPrice;
    		}
    	}
    	return $customOptionsPrice;
    }
	public function duplicate($observer) {
		$currentProduct = $observer->getCurrentProduct();
		//$newProductData = $observer->getNewProduct()->getData();
		$duplicateData = array(
			'original_product_id' => $currentProduct->getId()
		);
		Mage::getSingleton("core/session")->setDuplicatePdcConfig($duplicateData);
	}
}