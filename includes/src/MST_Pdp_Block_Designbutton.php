<?php
class MST_Pdp_Block_Designbutton extends Mage_Core_Block_Template
{
    protected $_currentAction;
	protected $_shareId;
    protected $_extraOption;
    protected $_params;
    public function __construct() {
        $this->_params = Mage::app()->getRequest()->getParams();
        if (isset($this->_params['action'])) {
            $this->_currentAction = $this->_params['action'];
        }
        if (isset($this->_params['share'])) {
            $this->_shareId = $this->_params['share'];
        }
    }
	public function getCurrentProduct() {
        $params = $this->_params;
        $currentProduct =  Mage::registry('current_product');
        if ($currentProduct != NULL) {
            return $currentProduct;
        } else {
            if (isset($params['product-id'])) {
                $productId = $params['product-id'];
                $currentProduct = Mage::getModel('catalog/product')->load($productId);
                return $currentProduct;
            }
        }
        return null;
    }
    public function getProductId() {
        $currentProduct = $this->getCurrentProduct();
        if ($currentProduct != null) {
            return $currentProduct->getId();
        }
    }
    public function getProductUrl() {
        return $this->getCurrentProduct()->getProductUrl();
    }
    public function isDesignAble() {
        if(!Mage::helper("pdp")->isProductDesignAble($this->getProductId())) {
        	return false;
        }
        $productStatus = Mage::getModel('pdp/productstatus')->getProductStatus($this->getProductId());
        if ($productStatus != 1) {
            return false;
        }
        return true;
    }
    public function getPdpDesignInfo() {
        $response = array();
		$helper = Mage::helper('pdp');
        $response['action'] = $this->_currentAction;
        $response['share_id'] = $this->_shareId;
        $response['cart_item_id'] = '';
        $response['wishlist_item_id'] = '';
        $response['extra_options'] = '';
		$response['extra_options_value'] = '';
        $moduleName = Mage::app()->getRequest()->getModuleName();
        if (Mage::app()->getRequest()->getActionName() == "configure") {
            if ($moduleName == "checkout") {
                $itemCartId = $this->_params['id'];
                $response['cart_item_id'] = $itemCartId;
                $cart = Mage::getModel('checkout/cart')->getQuote();
                $item = $cart->getItemById($itemCartId);
                $buyRequest = $item->getBuyRequest()->getData();
                if (isset($buyRequest['extra_options'])) {
                    $response['extra_options'] = $buyRequest['extra_options'];
					$response['extra_options_value'] = $helper->getPDPJsonContent($response['extra_options']);
                }
            } else if ($moduleName == "wishlist") {
                $itemWishlistId = $this->_params['id'];
                $response['wishlist_item_id'] = $itemWishlistId;
                $wishlist = Mage::getModel('wishlist/item_option');
                $item = $wishlist->load($itemWishlistId);
                $optionValue = $item->getValue();
                $buyRequest = unserialize($optionValue);
                if (isset($buyRequest['extra_options'])) {
                    $response['extra_options'] = $buyRequest['extra_options'];
					$response['extra_options_value'] = $helper->getPDPJsonContent($response['extra_options']);
                }
            }
        } else if ($this->_shareId != null) {
			$response['extra_options'] = Mage::getModel('pdp/jsonfile')->load($this->_shareId)->getFilename();
			$response['extra_options_value'] = $helper->getPDPJsonContent($response['extra_options']);
        } else if(isset($this->_params['redesign']) && $this->_params['redesign']) {
            //echo "Redesign Request";
            if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                $customerDesign = Mage::getModel("pdp/customerdesign")->load($this->_params['redesign']);
                if($customerDesign->getId()) {
                    //Zend_Debug::dump($customerDesign->getData());
                    $response['extra_options'] = $customerDesign->getFilename();
                    $response['extra_options_value'] = $helper->getPDPJsonContent($response['extra_options']);
                }
            }
        } else {
        	//Get sample design if exists
        	$sampleDesignJsonFile = $helper->getSampleJsonFile($this->getProductId());
        	if ($sampleDesignJsonFile != "") {
        		$response['extra_options'] = $sampleDesignJsonFile;
        		$response['extra_options_value'] = $helper->getPDPJsonContent($response['extra_options']);
        	}
        }
        return $response;
    }
    public function getButtonLabel() {
    	$buttonLabel = Mage::getStoreConfig("pdp/setting/design_button_label");
    	if ($buttonLabel == "") {
    		$buttonLabel = "Customize it!";
    	}
    	return $buttonLabel;
    }
	public function getCurrentDesignResultImage($jsonContent) {
    	$jsonDecoded = json_decode($jsonContent, true);
    	if(!$jsonDecoded) {
    		return;
    	}
    	$images = array();
    	foreach($jsonDecoded as $side) {
            $images[] = array(
                    'side_name' => $side['side_name'],
                    'image_result' => $side['image_result']
            );
    	}
    	if (!empty($images)) {
    		return $images;
    	}
    }
}