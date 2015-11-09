<?php

/**
 * Description of AjaxController
 *
 * @author imran
 */
class WTS_AssociatedProductAjaxPriceLoader_AjaxController extends Mage_Core_Controller_Front_Action
{

    public function getAssociatedProductPriceAction()
    {
        $data = $this->getRequest()->getPost();
        /*echo "<pre>";
        print_r($data);
        echo "</pre>";*/
        $_product = Mage::getModel('catalog/product')->load($data['product']);

        //get sum of price against each custom option
        $options = $data['options'];
        $custOptionPrice = 0.00;
        foreach ($options as $option) {
            foreach ($_product->getOptions() as $o) {
                $optionType = $o->getType();
                $values = $o->getValues();
                foreach ($values as $k => $v) {
                    if ($option == $v->option_type_id) {
                        $custOptionPrice += $v->price;
                        break;
                    }
                }
            }
        }

        //we are going to break the above passed parameters and going to get the product based on the selection.
        $selectedAttrs = array_filter($data['super_attribute']);//by removing empty entries
        $childProduct = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($selectedAttrs, $_product);
        $finalPrice = $custOptionPrice;
        if (!empty($childProduct)) {
            $childPrice = Mage::getModel('catalog/product')->load($childProduct->getEntityId())->getPrice();
            $finalPrice += $childPrice;
        }
        $finalPrice = Mage::app()->getStore()->getCurrentCurrencyCode() . number_format($finalPrice, 2, '.', ',');//change in this format 1234.54
        $priceArray = array('price' => $finalPrice);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($priceArray));
    }

}
