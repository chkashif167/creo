<?php
class MST_Pdp_UploadController extends Mage_Core_Controller_Front_Action
{
    protected $_helper;
    public function _construct() {
        $this->_helper = Mage::helper("pdp/upload");
    }
    public function testAction() {
        echo "Test Function </br>";
        echo "<br>Is Imagick Avaiable: " . $this->_helper->isImagickLoaded();
        echo "<br/>Is getimagesize function Avaiable: " . $this->_helper->isGetImageSizeLoaded();
        /*$size = $this->_helper->getUploadMaxFileSize();
        Zend_Debug::dump($size);
        $isRealImage = $this->_helper->getImageSize($this->_helper->uploadDir . "1426125678-customupload.jpg");
        Zend_Debug::dump($isRealImage);
        echo "<hr>Crop Image <br/>";
        $imgPath = $this->_helper->uploadDir . "artworkimage11414811752.svg";
        $cropData = [100, 100, 10, 20];
        $cropResult = $this->_helper->cropImage($imgPath, $cropData);
        Zend_Debug::dump($cropResult);*/
        Mage::getSingleton("core/session")->setCustomUploadImages("");
        //Zend_Debug::dump($this->_helper->getConfig());
        
        $isRealImage = $this->_helper->getImageSize($this->_helper->uploadDir . "1426125678-customupload.jpg");
        Zend_Debug::dump($isRealImage);
        echo "<hr>Crop Image <br/>";
        $imgPath = $this->_helper->uploadDir . "crop-img-1429583104_1429580067-customupload.jpg";
        $cropData = [10, 50, 300, 300];
        $cropResult = $this->_helper->cropImage($imgPath, $cropData);
        Zend_Debug::dump($cropResult);
    }
	public function uploadCustomImageAction() {
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES["uploads"])) {
			$uploads = $_FILES["uploads"];
            //SVG type : image/svg+xml
			if (count($uploads['name'])>0) {
				$baseDir = Mage::getBaseDir('media') . DS . "pdp" . DS . "images" . DS . "upload" . DS;
				if (!file_exists($baseDir)) {
					mkdir($baseDir, 0777);
				}
				if (file_exists($baseDir)) {
					$mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "pdp/images/upload/";
					$uploadedImages = array();
					foreach ($uploads['name'] as $key => $name) {
						if ($uploads['error'][$key] === UPLOAD_ERR_OK) {
							$filenameTemp = explode(".", $uploads["name"][$key]);
							$name = time() . '-customupload.' . end($filenameTemp);
							$size = $uploads["size"][$key];
							$type = $uploads["type"][$key]; // could be bogus!!! Users and browsers lie!!!
							$tmp  = $uploads["tmp_name"][$key];
							$result = move_uploaded_file( $tmp, $baseDir .$name);
							if ($result) {
                                //Check if image is real, if not, remove file for security reason.
                                if($uploads["type"][$key] == "image/svg+xml") {
                                    $uploadedImages[] = $mediaUrl . $name;
                                } else {
                                    $isRealImage = $this->_helper->isRealImage($baseDir . $name);
                                    if($isRealImage) {
                                        $uploadedImages[] = $mediaUrl . $name;
                                    } else {
                                        $response['status'] = 'error';
                                        $response['message'] = 'Please upload a valid file!';
                                        $this->getResponse()->setBody(json_encode($response))->sendResponse();
							            exit();
                                    }
                                }
							}
						} else if ($uploads['error'][$key] === UPLOAD_ERR_INI_SIZE) {
							$response['status'] = 'error';
							$response['message'] = 'The uploaded file exceeds the upload_max_filesize. Please check your server PHP settings!';
							$this->getResponse()->setBody(json_encode($response))->sendResponse();
							exit();
						}
					}
					$key++;
					if (isset($uploadedImages[0])) {
						$this->setCustomImageSession($uploadedImages[0]);
					}
					$this->getResponse()->setBody(json_encode($uploadedImages));
				}
			}
		}
	}
	public function setCustomImageSession($image) {
		$customImages = Mage::getSingleton("core/session")->getCustomUploadImages();
		$customImages[] = $image;
        $reOrderImages = array_reverse($customImages);
		Mage::getSingleton("core/session")->setCustomUploadImages($reOrderImages);
	}
    public function cropImageAction() {
        $request = $this->getRequest()->getPost();
        $response = array();
        if (isset($request['filename']) && $request['filename'] != "") {
            $imagePath = $this->_helper->uploadDir . $request['filename'];
            $croppedImage = $this->_helper->cropImage($imagePath, $request);
            if($croppedImage) {
                $response = $croppedImage;
                $this->setCustomImageSession($croppedImage['crop_image']);
            }
        } else {
            $response['status'] = "error";
            $response['message'] = "Image not found!";
        }
        $this->getResponse()->setBody(json_encode($response));
    }
}