<?php
class MST_Pdp_UploadController extends Mage_Core_Controller_Front_Action
{
    protected $_helper;
    public function _construct() {
        $this->_helper = Mage::helper("pdp/upload");
    }
    public function testAction() {
        echo "Test Function </br>";
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
                            $result = move_uploaded_file( $tmp, $baseDir . $name);
                            if ($result) {
                                //Check upload file types
                                $applicationFileTypes = Mage::helper("pdp/upload")->getApplicationFileTypes();
                                if(in_array($type, $applicationFileTypes)) {
                                    //Using imagick to convert application file to png file
                                    $convertResult = Mage::helper("pdp/upload")->convertFileToImage($baseDir . $name);
                                    if(isset($convertResult['status']) && $convertResult['status'] == "success") {
                                        //$uploadedImages[] = $mediaUrl . $convertResult['filename'];
                                        $orignalFile = $baseDir . $convertResult['filename'];//code modified for resizing
                                        $uploadedImages[] = $this->resizeImage($orignalFile, '', ['width' => 240, 'height' => 330]);
                                    } else {
                                        $this->getResponse()->setBody(json_encode($convertResult))->sendResponse();
                                        exit();
                                    }
                                } else {
                                    //Check if image is real, if not, remove file for security reason.
                                    if($uploads["type"][$key] == "image/svg+xml") {
                                        //$uploadedImages[] = $mediaUrl . $name;
                                        $orignalFile = $baseDir . $name;//code modified for resizing
                                        $uploadedImages[] = $this->resizeImage($orignalFile, '', ['width' => 240, 'height' => 330]);
                                    } else {
                                        $isRealImage = $this->_helper->isRealImage($baseDir . $name);
                                        if($isRealImage) {
                                            //$uploadedImages[] = $mediaUrl . $name;
                                            $orignalFile = $baseDir . $name;//code modified for resizing
                                            $uploadedImages[] = $this->resizeImage($orignalFile, '', ['width' => 240, 'height' => 330]);
                                        } else {
                                            $response['status'] = 'error';
                                            $response['message'] = 'Please upload a valid file!';
                                            //unlink($baseDir . $name);
                                            $this->getResponse()->setBody(json_encode($response))->sendResponse();
                                            exit();
                                        }
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

    /**
    $options[width]
    $options[height]
    $options[media-url]
     **/
    public function resizeImage($basePath, $newPath = '', $options = array()) {
        if(!file_exists($basePath)) {
            return false;
        }
        $extTemp = explode(".", $basePath);
        if(end($extTemp) == "svg") {
            return false;
        }
        //Image name
        $width = 150;
        $height = 150;
        if(isset($options['width'])) {
            $width = $options['width'];
        }
        if(isset($options['height'])) {
            $height = $options['height'];
        }
        $nameTemp = explode(DS, $basePath);
        $newFilename = "resize_" . end($nameTemp);
        if($newPath == "") {
            $newPath = Mage::getBaseDir('media') . DS . "pdp" . DS . "images" . DS . "upload" . DS . "resize" . DS;
        }
        //Create new folder if not exists
        if(!file_exists($newPath)) {
            mkdir($newPath, 0777, true);
            if(!file_exists($newPath)) {
                return false;
            }
        }
        $imageObj = new Varien_Image($basePath);
        $imageObj->constrainOnly(TRUE);
        $imageObj->keepAspectRatio(TRUE);
        $imageObj->keepFrame(false);
        $imageObj->keepTransparency(true);
        $imageObj->backgroundColor(array(255,255,255));
        $imageObj->resize($width, $height);
        $imageObj->save($newPath . $newFilename);
        if(file_exists($newPath . $newFilename)) {
            $mediaUrl = Mage::getBaseUrl("media") . "pdp/images/upload/" . "resize/";
            if(isset($options['media-url'])) {
                $mediaUrl = $options['media-url'];
            }
            return $mediaUrl . $newFilename;
        }
        return false;
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
    public function deleteImageAction() {
        $response = array(
            'status' => 'error',
            'message' => 'Can not remove image! Something went wrong!'
        );
        $request = $this->getRequest()->getPost();
        if(isset($request['image']) && $request['image']) {
            $customImages = Mage::getSingleton("core/session")->getCustomUploadImages();
            $newCustomImages = array_diff($customImages, array($request['image']));
            Mage::getSingleton("core/session")->setCustomUploadImages($newCustomImages);
            $response = array(
                'status' => 'success',
                'message' => 'Image had been successfully removed!'
            );
        }
        $this->getResponse()->setBody(json_encode($response));
    }
}