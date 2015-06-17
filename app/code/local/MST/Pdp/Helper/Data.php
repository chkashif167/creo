<?php
/**
* Magento Support Team.
* @category   MST
* @package    MST_Pdp
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
class MST_Pdp_Helper_Data extends Mage_Core_Helper_Abstract {
    protected static $_design = null;
	protected static $_imgPath = null;
    protected static $_artworkPath = null;
	protected static $_fontPath = null;
    public function __construct()
    {
        self::$_design = Mage::getModel('pdp/pdp');
		self::$_imgPath = Mage::getBaseDir('media') . DS .'pdp' . DS .'images' . DS;
        self::$_artworkPath = self::$_imgPath . "artworks" .DS;
		self::$_fontPath = Mage::getBaseDir('media').DS.'pdp' . DS . 'fonts' . DS;
    }
    public function getImagePath ()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'pdp/images/';
    }
    public function getFontPath()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'pdp/fonts/';
    }
    public function getDesignCollection()
    {
        $collection = self::$_design->getEnableDesignCollection();
        return $collection;
    }
    public function getCustomImages()
    {
        return self::$_design->getImageCollection();
    }
    public function getFonts()
    {
        return self::$_design->getFontCollection();
    }
	public function saveImage($inputName, $mediaPath) {
		//$mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . $mediaPath;
		if (isset($_FILES[$inputName]) && $_FILES[$inputName]['name']) {
            $imageName = $_FILES[$inputName]['name'];
			try {
				$ext = substr($imageName, strrpos($imageName, '.') + 1);
				$filename = $inputName . time() . '.' . $ext;
				$uploader = new Varien_File_Uploader($inputName);
				$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg+xml', 'svg')); // or pdf or anything
				$uploader->setAllowRenameFiles(false);
				$uploader->setFilesDispersion(false);
				$path = Mage::getBaseDir('media') . DS .$mediaPath;
                $validFilename1 = str_replace('_', '', $filename);
                $validFilename2 = str_replace('-', '', $validFilename1);
                $filename = $validFilename2;
				$uploader->save($path, $filename);
				return $filename;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				echo "Error while upload image";
				return;
			}
		}
		return "";
	}
	
	public function removeImageFile ($filename)
	{
        if (file_exists(self::$_imgPath . $filename)) {
            return unlink(self::$_imgPath . $filename);
        } else {
            if (file_exists(self::$_artworkPath . $filename)) {
                return unlink(self::$_artworkPath . $filename);
            }
        }

	}
	public function removeFontFile ($filename)
	{
		return unlink(self::$_fontPath . $filename);
	}
	public function getViewPerPage()
	{
		return array(20, 50, 100);
	}
	public function getCategoryFilterOptions()
	{
		$category = Mage::getModel('pdp/artworkcate')->getCategoryFilterOptions();
		return $category;
	}
	public function pagingCollection($current_page, $page_size, $view_per_page, $collection, $total, $url, $category){
		
		$collection_counter = $total;
		$collection->setCurPage($current_page);
	    $collection->setPageSize($page_size);
		
		$skin_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN);
		$arrowLeft = $skin_url . "adminhtml/default/default/images/pager_arrow_left.gif" ;
		$arrowRight = $skin_url . "adminhtml/default/default/images/pager_arrow_right.gif" ;
		$paging_text="<div class='paging-area'>";
       	# Get total pages
		$size = ceil($collection_counter/$page_size);
		$paging_text .= "Page ";
		# Previous button
		if($current_page != 1 ){
       		$page = $current_page - 1;
       		$paging_text.="<div id='previous_div'><a id='previous_page_btn' href='#' onclick='ImgItem.pagingCollection(this.id,\"".$url."\")'>".'<img class="arrow" alt="Go to Previous page" src="'. $arrowLeft .'">'."</a></div>";	
		}else{
			$paging_text .= '<img class="arrow" alt="Go to Previous page" src="'. $arrowLeft .'">';
		}
		# Input textbox enter page number
		$paging_text .= "<input class='span1' type='text' id='current_page_input' name='current_page_input' size='1' value='". $current_page ."'/>";
		# Next button
   		if($current_page != (int)$size){
       		$page = $current_page + 1;
       		$paging_text .= "<div id='next_div'><a id='next_page_btn' href='#' onclick='ImgItem.pagingCollection(this.id,\"". $url ."\")'>".'<img class="arrow" alt="Go to Next page" src="'.$arrowRight.'">'."</a></div>";	
		}else{
			$paging_text .= '<img class="arrow" alt="Go to Next page" src="' . $arrowRight . '">';
		}
		# View per page dropdown
		$view_dropdown = "<select id='view_per_page' class='span1' name='view_per_page' onchange='ImgItem.pagingCollection(this.id,\"". $url ."\")'>";
		foreach($view_per_page as $option){
			$view_dropdown .= "<option value='". $option ."' ".(($option == $page_size)? 'selected="selected"' : '').">$option</option>";
		}
		$view_dropdown .= "</select>";
		
		# Category
		$categorys = $this->getCategoryFilterOptions();
		$category_dropdown = "<select id='category_filter' name='category_filter' onchange='ImgItem.pagingCollection(this.id,\"". $url ."\")'>";
		foreach($categorys as $key => $value){
			$category_dropdown .= "<option value='". $key ."' ".(((string)$key === $category)? 'selected="selected"' : '').">$value</option>";
		}
		$category_dropdown .= "</select>";
		
		$paging_text .= " of ". $size ." pages | View $view_dropdown | Category $category_dropdown | Total ". $collection_counter ." records found."; 
		$paging_text.="</div>";//End paging-are div
		return array(
			'paging_text'=> $paging_text,
			'collection' => $collection,
		);
	}
	public function formatFee($amount) {
		return Mage::helper('pdp')->__('Fee');
	}
	
	public function getTotalDesignPrice () {
		$total = 0;
		$cartHelper = Mage::helper('checkout/cart');
		$items = $cartHelper->getCart()->getItems();
		foreach($items as $item){
			$qty = $item->getQty();
			$options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
			$customOption = $options['options'][0]['option_value'];
			$optionArr = explode('+', $customOption);
			$priceArr = explode(';', $optionArr[0]);
			$price = $priceArr[6] * $qty;
			$total += floatval($price);
		}
		return $total;
	}
	public function setCustomItemPrice($price)
	{
		Mage::getSingleton('core/session')->setCustomItemPrice($price);
	}
	public function getCustomItemPrice()
	{
		return Mage::getSingleton('core/session')->getCustomItemPrice();
	}
	public function resetCustomItemPrice($price)
	{
		Mage::getSingleton('core/session')->setCustomItemPrice('');
	}
	public function getAdminTemplates($productId) {
		$response = "";
		$jsonFilename = $this->getSampleJsonFile($productId);
		if ($jsonFilename != "") {
			$response = $this->getPDPJsonContent($jsonFilename);
		}
		return $response;
	}
	public function getAdminSampleImage($productId) {
		$jsonContent = $this->getAdminTemplates($productId);
		$jsonDecoded = json_decode($jsonContent, true);
		if(!$jsonDecoded) {
			return;
		}
		$images = array();
		foreach($jsonDecoded as $sides) {
			foreach($sides as $side) {
				//Zend_Debug::dump($side);
				$images[] = array(
						'side_name' => $side['side_name'],
						'image_result' => $side['image_result']
				);
			}
		}
		if (!empty($images)) {
			return $images;
		}
	}
	public function getSampleJsonFile($productId) {
		$jsonFilename = "";
		$collection = Mage::getModel('pdp/admintemplate')->getCollection();
		$collection->addFieldToFilter('product_id', $productId);
		if (count($collection) > 0) {
			$jsonFilename = $collection->getFirstItem()->getPdpDesign();
		}
		return $jsonFilename;
	}
	public function getFacebookSetting() {
		$isEnableFacebook = Mage::getStoreConfig('pdp/social/enable_facebook');
		if ($isEnableFacebook == 1) {
			$appId = Mage::getStoreConfig('pdp/social/facebook_app_id');
			$secretKey = Mage::getStoreConfig('pdp/social/facebook_secret_key');
			return array('facebook_app_id' => $appId, 'facebook_secret_key' => $secretKey);
		}
		return false;
	}
	/* edit by David */
	public function get_content_id($file,$id){
		$h1tags = preg_match_all("/(<div id=\"{$id}\">)(.*?)(<\/div>)/ismU",$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}
	public function get_div($file,$id){
	    $h1tags = preg_match_all("/(<div.*>)(\w.*)(<\/div>)/ismU",$file,$patterns);
	    $res = array();
	    array_push($res,$patterns[2]);
	    array_push($res,count($patterns[2]));
	    return $res;
	}
	public function get_domain($url)   {   
		//$dev = 'dev';
		$dev = $_SERVER['SERVER_NAME'];
		if ( !preg_match("/^http/", $url) )
			$url = 'http://' . $url;
		if ( $url[strlen($url)-1] != '/' )
			$url .= '/';
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : ''; 
		if ( preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs) ) { 
			$res = preg_replace('/^www\./', '', $regs['domain'] );
			return $res;
		}   
		return $dev;
	}
	/* end */
	public function decodePdpString($jsonString) {
		$json = str_replace('&quot;', '"', $jsonString);
		$pdpdesign = json_decode($json);
		return $pdpdesign;
	}
	public function getCartItemJsonString($itemId) {
		$cartHelper = Mage::helper('checkout/cart');
		$items = $cartHelper->getCart()->getItems();
		foreach ($items as $item) {
			if ($item->getItemId() == $itemId) {
				$buyRequest = $item->getBuyRequest();
				foreach ($buyRequest['options'] as $option) {
					$optionArr = json_decode($option);
					//Check current option is pdpdesign string
					if ($optionArr->inlay != "") {
						return $optionArr;
					}
				}
				break;
			}
		}
		return null;
	}
	public function getShareJsonString($shareId) {
		$pdpDesign = Mage::getModel('pdp/share')->load($shareId)->getPdpdesign();
		return $this->getPDPJsonContent($pdpDesign);
	}
	public function getDesignSides($productId,$w,$h) {
		$model = Mage::getModel('pdp/pdpside');
		$collection = $model->getActiveDesignSides($productId);
		$list = "<ul class='tab-sides'>";
        $baseImageUrl = $this->getImagePath();
        $i=0;
        //Get side info from sample json if exists sample design
        $sampleJsonDecode = array();
        $sampleJsonContent = $this->getAdminTemplates($productId);
        if($sampleJsonContent != "") {
            $sampleJsonDecode = json_decode($sampleJsonContent, true);
        }
		foreach ($collection as $side) {
			//Zend_Debug::dump($side->getData());
			$width = $side->getInlayW();
			$height = $side->getInlayH();
			$top = $side->getInlayT();
			$left = $side->getInlayL();
            //Side info from side table
            $_sideId = $side->getId();
            $_sideImg = $side->getFilename();
            $_sideOverlay = $side->getOverlay();
            $_sideColorCode = $side->getColorCode();
            $_backgroundType = $side->getBackgroundType();
            $_sizePrice = $side->getPrice();
            $_sizePriceFormated = "";
            if($_sizePrice > 0) {
                $_sizePriceFormated = " +" . Mage::helper('core')->currency($_sizePrice, true, false);
            }
            $_sizeName = $side->getLabel() . $_sizePriceFormated;
            //Get side info in sample json if exists
            if (!empty($sampleJsonDecode)) {
                foreach($sampleJsonDecode as $sideArr) {
                    if($sideArr['side_id'] == $_sideId) {
                        if(isset($sideArr['side_img']) && $sideArr['side_img'] != "") {
                            $_sideImg = $sideArr['side_img'];
                        }
                        if(isset($sideArr['side_overlay']) && $sideArr['side_overlay'] != "") {
                            $_sideOverlay = $sideArr['side_overlay'];
                        }
                        if(isset($sideArr['color_code']) && $sideArr['color_code'] != "") {
                            $_sideColorCode = $sideArr['color_code'];
                        }
                        //get side_code = color_code if side_color not empty - color from swap color
                        if(isset($sideArr['side_color']) && $sideArr['side_color'] != "") {
                            $_sideColorCode = $sideArr['side_color'];
                        }
                    }
                }
            }
            if($i++==0){$first_act = 'active';}else{$first_act = '';}
			$inlayString = "$width,$height,$top,$left";
			$list .= "<li pdc-data='pdc-btn' pdc-action='SWITCH_SIDE' pdc-side='" . $_sideId . "' class='pdp_side_item_content ". $first_act ."' inlay='" . $inlayString . "' tab='side_" . $side->getId() .
			"' side_img='" . $_sideImg . "' overlay='". $_sideOverlay ."' label='". $_sizeName ."' title='". $_sizeName ."' background_type='". $_backgroundType ."' color_code='". $_sideColorCode ."' price='". $_sizePrice ."'>";
                $list .= '<a href="javascript:void(0)">';
                $list .= "<span>". $this->__($_sizeName) ."</span>";
            if($_backgroundType == "image") {
                //show image
                $list .= "<img id='thumbnail_" . $_sideId . "' class='pdp-side-img' width='".$w."' height='".$h."' src='". $baseImageUrl . $_sideImg ."' alt=". $this->__($_sizeName) ." />";
            } else {
                // show color
                $list .= "<span style='background: #". $_sideColorCode ."; padding: 5px 10px; display: none;'></span>";
            }
                    
                $list .= '</a>';
            $list .= "</li>";
		}
		$list .= "</ul>";
		return $list;
	}
    public function getDesignSidesTotal($productId) {
		$model = Mage::getModel('pdp/pdpside');
		$collection = $model->getActiveDesignSides($productId);
        $nb = 0;
        $data = '';
        foreach ($collection as $side) {
            $data .= '
               <div data-block="pricing" tab="side_'.$side->getId().'" class="layer_side_'. $side->getId() .' prices prices_layer">
        			<input id="side-'. $side->getId() .'" name="toolbox" type="radio" />
        			<label for="side-'. $side->getId() .'">'.$this->__('SIDE '). ++$nb .'</label>
        			<article class="ac-medium pricetable">
        				<div class="layer-pricing-table">
        					<table class="layer_pricing">
        					  <thead>
        						<tr>
        						  <th>#</th>
        						  <th>Layer</th>
        						  <th>Price</th>
        						</tr>
        					  </thead>
        					  <tbody>
        						<tr>
        						  <td>2</td>
        						  <td>Lorem IpSum dolor sit amet</td>
        						  <td>0.10</td>
        						  <td><a class="item_delete" title="Remove"><i class="pi pi-trash-o"></i></a></td>
        						</tr>
        					  </tbody>
        					</table>
        				</div>
        			</article>
        		</div> 
                ';
        }
		return $data;
	}
	public function isItemHasPdpinfo($buyRequest) {
		foreach ($buyRequest['options'] as $option) {
			if ($option != "") {
				try {
					$jsonDecode = json_decode($option);
					foreach ($jsonDecode as $side) {
						if ($side->name != "" && $side->json != "") {
							return true;
						}
					}
				} catch(Exception $e) {
				
				}
			}
		}
		return false;
	}
	public function getOrderItemString($orderId, $itemId) {
		$order = Mage::getModel('sales/order')->load($orderId); 
		$item = $order->getItemById($itemId);
		$buyRequest = $item->getBuyRequest()->getData();
		if (isset($buyRequest['extra_options'])) {
			return $buyRequest['extra_options'];
		}
		return null;
	}
    public function getPDPJsonContent($filename) {
        $jsonBaseDir = Mage::getBaseDir('media') . DS . "pdp" . DS . "json" . DS;
        try {
            $data = file_get_contents($jsonBaseDir . $filename);
            if ($data) {
                return $data;
            }
        } catch (Exception $e) {

        }
    }
	public function getThumbnailImage($filename) {
		$content = $this->getPDPJsonContent($filename);
		$jsonData = json_decode($content, true);
		$thumbnails = array();
		foreach ($jsonData as $side) {
			$thumbnails[] = array (
                'name' => $side['side_name'], 
                'image' => $side['image_result'],
                'inlay' => $side['side_inlay'],
            );
		}
		return $thumbnails;
	}
	public function getShareThumbnail($shareId) {
		$pdpDesign = Mage::getModel('pdp/jsonfile')->load($shareId)->getFilename();
		return $this->getThumbnailImage($pdpDesign);
	}
	public function getDesignArtworks($filename) {
		$content = $this->getPDPJsonContent($filename);
		if(!$content) {
			return false;
		}
		$jsonData = json_decode($content, true);
		$artworks = array();
		if(isset($jsonData['items'])) {
			foreach($jsonData['items'] as $item) {
				$itemJsonDecodeds = json_decode($item['json'], true);
				if(!$itemJsonDecodeds) {
					return false;
				}
				foreach($itemJsonDecodeds as $objects) {
					if($objects) {
						foreach($objects as $object) {
							if ($object['type'] == "image") {
								$artworks[] = $object['src'];
							} else if($object['type'] == "path-group") {
								$artworks[] = $object['isrc'];
							}
						}	
					}
				}
			}	
		}
		return $artworks;
	}
	public function getDesignArtworksPrice($filename) {
		$allArtworkPrice = 0;
		$artworkInDesign = $this->getDesignArtworks($filename);
		if($artworkInDesign) {
			foreach($artworkInDesign as $artwork) {
				$temp = explode("/", $artwork);
				$artworkFilename = end($temp);
				$allArtworkPrice += $this->getArtworkPrice($artworkFilename);
			}	
		}
		return $allArtworkPrice;
	}
	public function getArtworkPrice($filename) {
		$artworkPrice = Mage::getModel("pdp/images")->getArtworkPrice($filename);
		return floatval($artworkPrice);
	}
	public function isSampleDesignHasText($jsonString) {
		$jsonContent = json_decode($jsonString, true);
		if(!$jsonContent) {
			return false;
		}
		$fonts = array();
		foreach ($jsonContent['items'] as $side) {
			$jsonDecoded = json_decode($side['json'], true);
			for ($j = 0; $j < count($jsonDecoded['objects']) ; $j++) {
				$objectType = $jsonDecoded['objects'][$j]['type'];
				if($objectType == "text") {
					return true;
				}
			}
		}
		return false;
	}
	public function isProductDesignAble($productId) {
		$supportDesignProducts = array (
				"simple",
				"configurable",
				"virtual"
				//"downloadable" 
		);
		$product = Mage::getModel("catalog/product")->load($productId);
		if(!in_array($product->getTypeId(), $supportDesignProducts)) {
			return false;
		}
		//If product that "Not Visible Individually" will unable to customize
		if(!$product->isVisibleInSiteVisibility()) {
			return false;
		}
		return true;
	}
    public function getPdpBaseUrl() {
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
		//Check if website using store code in url
		$isUseStoreCodeInUrl = $this->isUseStoreCodeInUrl();
		if($isUseStoreCodeInUrl) {
			$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . "index.php/";
			try {
				$defaultStore = $this->getDefaultStore();
				$code = $defaultStore->getCode();
				if($code && $code != "admin") {
					$url .= $code . "/";
				} else {
					$url .= "default/";
				}
			} catch(Exception $error) {
				
			}
		}
		$isSecure = Mage::app()->getStore()->isCurrentlySecure();
		if ($isSecure) {
			//If current page in secure mode, but menu url not in secure, => change menu to secure
			//secure mode your current URL is HTTPS
			if (!strpos($url, 'https://')) {
				$validUrl = str_replace('http://', 'https://', $url);
				$url = $validUrl;
			}
		} else {
			//page is in HTTP mode
			if (!strpos($url, 'http://')) {
				$validUrl = str_replace('https://', 'http://', $url);
				$url = $validUrl;
			}
		}
		return $url;
	}
	public function getDefaultStore() {
		$websites = Mage::app()->getWebsites();
		return $websites[1]->getDefaultStore();
	}
	public function isUseStoreCodeInUrl() {
		$isUseStoreCodeInUrl = Mage::getStoreConfig("web/url/use_store");
		if($isUseStoreCodeInUrl) {
			return true;
		}
		return false;
	}
	public function isShowPricePanel($productId) {
		$config = $this->getProductConfig($productId);
		if ($config["show_price"] == 1) {
			return true;
		}
		return false;
	}
	/**Return an array of pdc product config**/
	public function getProductConfig($productId) {
		return Mage::getModel('pdp/productstatus')->getProductConfig($productId);
	}
	public function getPDPExtraPrice($jsonFilename) {
		$extraPrice = 0;
		if ($jsonFilename) {
			$jsonContent = $this->getPDPJsonContent($jsonFilename);
			$jsonDecoded = json_decode($jsonContent, true);
            foreach ($jsonDecoded as $side) {
                $extraPrice += floatval($side['final_price']);
            }
		}
		return $extraPrice;
	}
    public function getImageQuality() {
        $quality = 0.7;
        $qualityInSystem = Mage::getStoreConfig("pdp/setting/image_quality");
        if($qualityInSystem > 0 && $qualityInSystem <= 1) {
           $quality = $qualityInSystem;
        }
        return $quality;
    }
    public function getWatermarkConfig() {
        $watermarkPath = Mage::getStoreConfig('pdp/watermark/image');
        if(!strpos($watermarkPath, DS)) {
            if(DS == '/') {
                $watermarkPath = str_replace('\\', DS, $watermarkPath);
            } else {
                $watermarkPath = str_replace('/', DS, $watermarkPath);
            }
        }
        $config = array(
            'active' => Mage::getStoreConfig("pdp/watermark/active"),
            'position' => Mage::getStoreConfig("pdp/watermark/position"),
            'watermark_url' => Mage::getBaseUrl('media') . "pdp/images/" . Mage::getStoreConfig('pdp/watermark/image'),
            'watermark_path' => Mage::getBaseDir('media') . DS . "pdp". DS . "images" . DS . $watermarkPath,
        );
        return $config;
    }
    public function getLinkedImagesFromSvg($imageString) {
        $xml = new SimpleXMLElement($imageString);
        $linkedImages = array();
        foreach($xml as $node) {
            try {
                if($node->image) {
                    $attributes = $node->image->attributes('xlink', true);
                    $linkedImages[] = (string) $attributes->href;
                }
            } catch(Exception $e) {

            }
        }
        return $linkedImages;
    }
    //Convert linked image to base 64 image, then can open and edit in AI Editor
    public function convertLinkedImageToBase64Image($url) {
        $fileNameArr = explode("/", $url);
        $filename = end($fileNameArr);
        //Search path of image
        $pdcImageBasePath = Mage::getBaseDir("media") . DS . "pdp" . DS . "images" . DS;
        $filePath = "";
        $pdpSubImgDirs = array("upload", "artworks", "color-thumbnail", "thumbnail"); // Need to add more folder if needed
        if(file_exists($pdcImageBasePath . $filename)) {
            $filePath = $pdcImageBasePath . $filename;
        } else {
            //Check sub folder
            foreach ($pdpSubImgDirs as $dir) {
                $tempPath = $pdcImageBasePath . $dir . DS . $filename;
                if(file_exists($tempPath)) {
                    $filePath = $tempPath;
                    break;
                }
            }
        }
        if($filePath != "") {
            try {
                $type = pathinfo($filePath, PATHINFO_EXTENSION);
                $data = file_get_contents($filePath); 
                $imageEncode = base64_encode($data);
                if($imageEncode) {
                    $base64 = 'data:image/' . $type . ';base64,' . $imageEncode;
                    return $base64;
                }
            } catch(Exception $error) {
            
            }
        }
        //Default will return old url
        return $url;
    }
    //Replace linked images in svg to embed image
    //Return svg string with image encoded
    public function convertLinkedToEmbedImage($svgString) {
        $linkedImages = $this->getLinkedImagesFromSvg($svgString);
        //If there is no linked image, return original svg string
        if(empty($linkedImages)) {
            return $svgString;
        }
        $base64Image = array();
        foreach($linkedImages as $url) {
            $base64Image[$url] = $this->convertLinkedImageToBase64Image($url);
        }
        //Replace here
        foreach($base64Image as $linkImg => $baseCodeImg) {
            $temp = str_replace($linkImg, $baseCodeImg, $svgString);
            $svgString = $temp;
        }
        return $svgString;
    }
}
