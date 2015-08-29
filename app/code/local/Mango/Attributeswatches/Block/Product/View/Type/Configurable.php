<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Catalog super product configurable part block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mango_Attributeswatches_Block_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable {
     public function getAllowProducts()
    {
       $_show_out_of_stock = Mage::getStoreConfig("attributeswatches/settings/outofstock");
         if (!$this->hasAllowProducts()) {
            $products = array();
            $allProducts = $this->getProduct()->getTypeInstance(true)
                ->getUsedProducts(null, $this->getProduct());
            foreach ($allProducts as $product) {
				if (($product->isSaleable() || $_show_out_of_stock)  &&  $product->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_ENABLED ) {
                    $products[] = $product;
                }
            }
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }
    
    
    public function getJsonConfig() {
        $attributes = array();
        $options    = array();
        //$optionsSaleable = array();
        $saleableProducts = array();
        $store      = $this->getCurrentStore();
        $taxHelper  = Mage::helper('tax');
        $currentProduct = $this->getProduct();
        $preconfiguredFlag = $currentProduct->hasPreconfiguredValues();
        if ($preconfiguredFlag) {
            $preconfiguredValues = $currentProduct->getPreconfiguredValues();
            $defaultValues       = array();
        }
        /*$total_attributes =  count($this->getAllowAttributes());
        $_att_counter = 0;*/
        /* load product attributes to be reloaded. */
        /*  */
        $_attributes_to_reload = trim(Mage::getStoreConfig("attributeswatches/settings/reload_attributes"));
        $_attributes_to_reload =  ($_attributes_to_reload )?  explode(",", $_attributes_to_reload): false;
        // use observer to add attributes catalog_product_collection_load_before * Mage_Catalog_Model_Resource_Product_Collection
        $_product_attributes = array();
        
        foreach ($this->getAllowProducts() as $product) {
            $productId  = $product->getId();
            //$saleable = ;
            $saleableProducts[$productId] =  $product->isSaleable() ;
            foreach ($this->getAllowAttributes() as $attribute) {
                $productAttribute   = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue     = $product->getData($productAttribute->getAttributeCode());
                if (!isset($options[$productAttributeId])) {
                    $options[$productAttributeId] = array();
                }
                if (!isset($options[$productAttributeId][$attributeValue])) {
                    $options[$productAttributeId][$attributeValue] = array();
                }
                $options[$productAttributeId][$attributeValue][] = $productId;
                //$optionsSaleable[$productAttributeId][$attributeValue][] = array( "id"=> $productId, "saleable" => $saleable );
                
            }
            
            if($_attributes_to_reload){
                foreach ($_attributes_to_reload as $attribute_code) {
                    $attributeValue     = $product->getData($attribute_code);
                    if($attributeValue) $_product_attributes[$productId][$attribute_code]['value'] = $attributeValue;
                    /* for select attributes */
                    $attributeText     = $product->getAttributeText($attribute_code);
                    if($attributeText) $_product_attributes[$productId][$attribute_code]['text'] = $attributeText;
                }
            }
        }
        
        if(!count($_product_attributes)){
            $_attributes_to_reload = false;
        }
        $this->_resPrices = array(
            $this->_preparePrice($currentProduct->getFinalPrice())
        );
        foreach ($this->getAllowAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
            $info = array(
               'id'        => $productAttribute->getId(),
               'code'      => $productAttribute->getAttributeCode(),
               'label'     => $attribute->getLabel(),
               'options'   => array()
            );
            $optionPrices = array();
            $prices = $attribute->getPrices();
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if(!$this->_validateAttributeValue($attributeId, $value, $options)) {
                        continue;
                    }
                    $currentProduct->setConfigurablePrice($this->_preparePrice($value['pricing_value'], $value['is_percent']));
                    $currentProduct->setParentId(true);
                    Mage::dispatchEvent(
                        'catalog_product_type_configurable_price',
                        array('product' => $currentProduct)
                    );
                    $configurablePrice = $currentProduct->getConfigurablePrice();
                    /*$_saleable = false;
                    foreach($optionsSaleable[$attributeId][$value['value_index']] as $sindex=>$svalue){
                        if($svalue["saleable"]) {
                            $_saleable = true;
                            //break;
                        }
                    }*/
                    if (isset($options[$attributeId][$value['value_index']])) {
                        $productsIndex = $options[$attributeId][$value['value_index']];
                    } else {
                        $productsIndex = array();
                    }
                    $info['options'][] = array(
                        'id'        => $value['value_index'],
                        'label'     => $value['label'] ,
                        'price'     => $configurablePrice,
                        'oldPrice'  => $this->_prepareOldPrice($value['pricing_value'], $value['is_percent']),
                        'products'  => $productsIndex,
                        //'productsSaleable'  => isset($optionsSaleable[$attributeId][$value['value_index']]) ? $optionsSaleable[$attributeId][$value['value_index']] : array(),
                    );
                    $optionPrices[] = $configurablePrice;
                }
            }
            
             // CALL SORT ORDER FIX
            $info['options'] = $this->_sortOptions($info['options']);
            
            
            /**
             * Prepare formated values for options choose
             */
            foreach ($optionPrices as $optionPrice) {
                foreach ($optionPrices as $additional) {
                    $this->_preparePrice(abs($additional-$optionPrice));
                }
            }
            if($this->_validateAttributeInfo($info)) {
               $attributes[$attributeId] = $info;
            }
            // Add attribute default value (if set)
            if ($preconfiguredFlag) {
                $configValue = $preconfiguredValues->getData('super_attribute/' . $attributeId);
                if ($configValue) {
                    $defaultValues[$attributeId] = $configValue;
                }
            }
        }
        $taxCalculation = Mage::getSingleton('tax/calculation');
        if (!$taxCalculation->getCustomer() && Mage::registry('current_customer')) {
            $taxCalculation->setCustomer(Mage::registry('current_customer'));
        }
        $_request = $taxCalculation->getRateRequest(false, false, false);
        $_request->setProductClassId($currentProduct->getTaxClassId());
        $defaultTax = $taxCalculation->getRate($_request);
        $_request = $taxCalculation->getRateRequest();
        $_request->setProductClassId($currentProduct->getTaxClassId());
        $currentTax = $taxCalculation->getRate($_request);
        $taxConfig = array(
            'includeTax'        => $taxHelper->priceIncludesTax(),
            'showIncludeTax'    => $taxHelper->displayPriceIncludingTax(),
            'showBothPrices'    => $taxHelper->displayBothPrices(),
            'defaultTax'        => $defaultTax,
            'currentTax'        => $currentTax,
            'inclTaxTitle'      => Mage::helper('catalog')->__('Incl. Tax')
        );
        $config = array(
            'attributes'        => $attributes,
            'template'          => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
            'basePrice'         => $this->_registerJsPrice($this->_convertPrice($currentProduct->getFinalPrice())),
            'oldPrice'          => $this->_registerJsPrice($this->_convertPrice($currentProduct->getPrice())),
            'productId'         => $currentProduct->getId(),
            'chooseText'        => Mage::helper('catalog')->__('Choose an Option...'),
            'taxConfig'         => $taxConfig,
            'saleableProducts'  => $saleableProducts,
            'attributesToReload'=> $_attributes_to_reload,
            'productAttributes' => $_product_attributes
        );
        if ($preconfiguredFlag && !empty($defaultValues)) {
            $config['defaultValues'] = $defaultValues;
        }elseif( Mage::getStoreConfig("attributeswatches/settings/defaultselect")){
            /* set default for first attribute 
             * if setting is yes 
             * and default values (cart/buyrequest) is empty */
            /* get first attribute and first value */
            foreach($attributes as $_default_attibute_id => $_attribute_info){
                foreach($_attribute_info['options'] as $_option_index => $_option_info){
                    $defaultValues[$_default_attibute_id] = $_option_info['id'];
                    break;
                }
                break;
            }
            $config['defaultValues'] = $defaultValues;
        }
        $config = array_merge($config, $this->_getAdditionalConfig());
        return Mage::helper('core')->jsonEncode($config);

    }        
    
     /**
     * Sort the options based off their position.
     *
     * @param array $options
     * @return array
     */
    protected function _sortOptions($options)
    {
        if (count($options)) {
            if (!$this->_read || !$this->_tbl_eav_attribute_option) {
                $resource = Mage::getSingleton('core/resource');
                $this->_read = $resource->getConnection('core_read');
                $this->_tbl_eav_attribute_option = $resource->getTableName('eav_attribute_option');
            }
            // Gather the option_id for all our current options
            $option_ids = array();
            foreach ($options as $option) {
                $option_ids[] = $option['id'];
                $var_name  = 'option_id_'.$option['id'];
                $$var_name = $option;
            }
            $sql    = "SELECT `option_id` FROM `{$this->_tbl_eav_attribute_option}` WHERE `option_id` IN('".implode('\',\'', $option_ids)."') ORDER BY `sort_order`";
            $result = $this->_read->fetchCol($sql);
            $options = array();
            foreach ($result as $option_id) {
                $var_name  = 'option_id_'.$option_id;
                $options[] = $$var_name;
            }
        }
        return $options;
    }
    
}