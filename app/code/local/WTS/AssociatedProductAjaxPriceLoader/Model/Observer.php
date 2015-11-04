<?php

class WTS_AssociatedProductAjaxPriceLoader_Model_Observer
{

    public function changePrice(Varien_Event_Observer $observer)
    {
        $sku = $observer->getEvent()->getQuoteItem()->getProduct()->getData('sku');
        $_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
        $new_price = $_product->getPrice();

        // Get the quote item
        $item = $observer->getQuoteItem();
        // Ensure we have the parent item, if it has one
        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
        //apply the payment logic on in configurable products only
        if($item->getProduct()->isConfigurable()) {
            // Load the custom price
            $price = $new_price;
            // Set the custom price
            //Set price by subtracting base price from the passed price and 
            //then adding the product price to tackle any extra price attached with custom options :)
            //identify if there is some extra cost, final price - base price
            $extra_price = $item->getProduct()->getFinalPrice() - $item->getProduct()->getPrice(); //add this extra price
            $extra_price = ($extra_price > 0) ? $extra_price : 0;
            /* echo "Got:". $price;
              echo " Extra: ".$extra_price;
              echo "final price:".$item->getProduct()->getFinalPrice().", price:".$item->getProduct()->getPrice().", Passed:".($extra_price + $price);die(); */
            //only apply the associated price if extra_price don't exist (means values are not being set with attributes)
            if($extra_price == 0) {
                $item->setCustomPrice($extra_price + $price);
                $item->setOriginalCustomPrice($extra_price + $price);
            }else {
                //if the attributes are defined, use the parent product price instead (base product price)
                $item->setCustomPrice($item->getProduct()->getFinalPrice());
                $item->setOriginalCustomPrice($item->getProduct()->getFinalPrice());
            }
            // Enable super mode on the product.
            $item->getProduct()->setIsSuperMode(true);
        }
    }

}
