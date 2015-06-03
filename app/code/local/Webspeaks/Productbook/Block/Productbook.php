<?php
class Webspeaks_Productbook_Block_Productbook extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
    public function getProductbook()     
    { 
        if (!$this->hasData('productbook')) {
            $this->setData('productbook', Mage::registry('productbook'));
        }
        return $this->getData('productbook');
        
    }

	public function getPBConfigByName($key) {
		$config = Mage::getStoreConfig('pbconfig/pb_group/'.$key);
		return $config;
	}

	public function getCartTotal()
	{
		$quote = Mage::getModel('checkout/session')->getQuote();
		$total = $quote->getGrandTotal();
		$total = !empty($total) ? $total : 0;
		return Mage::helper('checkout')->formatPrice($total);
	}

	public function getCartTotalQty()
	{
		$qty = Mage::helper('checkout/cart')->getCart()->getSummaryQty();
		$qty = !empty($qty) ? $qty : 0;
		return $qty;
	}

	public function getProductPrice($product)
	{
		$type = $product->getTypeId();
		$price = Mage::helper('checkout')->formatPrice($product->getPrice());
		if ($type == 'grouped') {
			$aProductIds = $product->getTypeInstance()->getChildrenIds($product->getId());

			$prices = array();
			foreach ($aProductIds as $ids) {
				foreach ($ids as $id) {
					$aProduct = Mage::getModel('catalog/product')->load($id);
					$prices[] = $aProduct->getPriceModel()->getPrice($aProduct);
				}
			}
			sort($prices, SORT_NUMERIC);
			$price = isset($prices[0]) ? $prices[0] : $price;
			$price = 'Starting at: ' . Mage::helper('checkout')->formatPrice($price);
		} else if ($type == 'grouped') {
			$price = Mage::helper('checkout')->formatPrice($product->getPrice());
		}
		return $price;
	}

	public function addToCart($product, $qty=1)
	{
		$result = array();
		$type = $product->getTypeId();
		if ($type == 'grouped') {
			$res = Webspeaks_Productbook_Block_Productbook::addGroupedToCart($product, $qty);
			return $res;
		} else if ($type == 'configurable') {
			$result['status'] = "redirect";
			$result['message'] = "Redirecting...";
			$result['url'] = $product->getProductUrl();
			return $result;
		}
		try{
			$session = Mage::getSingleton('core/session', array('name'=>'frontend'));
			$cart = Mage::helper('checkout/cart')->getCart();
			$cart->addProduct($product, $qty);
		 
			$session->setLastAddedProductId($product->getId());
			$session->setCartWasUpdated(true);
			$cart->save();
			Mage::dispatchEvent('checkout_cart_add_product_complete',
				array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
			);

			$result = array();
			$result['status'] = "success";
			$result['message'] = "Added!";
			$result['cart_total'] = Webspeaks_Productbook_Block_Productbook::getCartTotal();
			$result['cart_qty'] = Webspeaks_Productbook_Block_Productbook::getCartTotalQty();
			return $result;
		} catch (Exception $e) {
			$result['status'] = 'error';
			$result['message'] =  $e->getMessage();
			return $result;
		}
	}

	public function addGroupedToCart($product, $qty)
	{
		$result = array();
		$super_group = array();
		$parentId = $product->getId();
		$children = Mage::getModel('catalog/product_type_grouped')->getChildrenIds($product->getId());
		$children = array_shift($children);
		foreach($children as $key=>$val) {
			$super_group[$key] = $qty;
		}
		try {
			$cart = Mage::helper('checkout/cart')->getCart();
			$params = array('super_group' => $super_group);
			$cart->addProduct($product, $params)->save();
			Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

			$result['status'] = "success";
			$result['message'] = "Added!";
			$result['cart_total'] = Webspeaks_Productbook_Block_Productbook::getCartTotal();
			$result['cart_qty'] = Webspeaks_Productbook_Block_Productbook::getCartTotalQty();
			return $result;
		} catch (Mage_Core_Exception $e) {
			$result['status'] = 'error';
			$result['message'] =  $e->getMessage();
			return $result;
		}
	}
}