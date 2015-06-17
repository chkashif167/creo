<?php
class MST_Pdp_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
	public function testAction()
    {
		echo "Convert Link image to base64 <pre>";
        $url = "http://localhost/GitHub/pdcnew/media//pdp/images/upload/crop-img-1431503001_1431502990-customupload.jpg";
        //$helper = Mage::helper("pdp")->convertLinkedImageToBase64Image($url);
	}
	public function deleteImageAction()
	{
		$filename = $_POST['filename'];
		if ($filename != "") {
			Mage::getModel('pdp/pdp')->deleteImageByFilename($filename);
		}
	}
	public function deleteImageByIdAction()
	{
		$img_list = $_POST['img_list'];
		if ($img_list != "") {
			$imgArr = explode(',', $img_list);
			foreach ($imgArr as $img) {
				$temp = explode('_', $img);
				$id = $temp[1];
				Mage::getModel('pdp/pdp')->deleteImageById($id);
			}
		}
	}
	public function deleteFontByIdAction()
	{
		$font_list = $_POST['font_list'];
		if ($font_list != "") {
			$fontArr = explode(',', $font_list);
			foreach ($fontArr as $font) {
				$temp = explode('_', $font);
				$id = $temp[1];
				Mage::getModel('pdp/pdp')->deleteFontById($id);
			}
		}
	}
	public function getImageInfoAction()
	{
		$imageId = $_POST['image_id'];
		if ($imageId != "") {
			$info = Mage::getModel('pdp/pdp')->getImageInfo($imageId);
			$this->getResponse()->setBody($info);
		}
	}
	public function updateImageInfoAction()
	{
		$data = $this->getRequest()->getParams();
		Mage::getModel('pdp/pdp')->updateImageInfo($data);
	}
	public function editColorAction()
	{
		$image_id = $_POST['image_id'];
		if ($image_id != "") {
			$model = Mage::getModel('pdp/images')->load($image_id);
			$options = $model->getColor();
			$this->getResponse()->setBody($options);
		}
	}
	public function deleteColorAction()
	{
		$imgColorId = $_POST['imagecolor_id'];
		if ($imgColorId != "") {
			echo Mage::getModel('pdp/pdp')->deleteColorImage($imgColorId);
		}
	}
	public function deleteDesignColorAction()
	{
		$designId = $_POST['design_id'];
		if ($designId != "") {
			echo Mage::getModel('pdp/pdp')->deleteDesignColor($designId);
		}
	}
	
	public function getImagePagingAction(){
		$page_size = $_POST['page_size'];
		$current_page = $_POST['current_page'];
		$url = $_POST['url'];
		$category = $_POST['category'];
		$collection = Mage::getModel('pdp/pdp')->getImageCollectionByCategory($category);
		$collection_counter = Mage::getModel('pdp/pdp')->getImageCollectionByCategory($category);
		$total = count($collection_counter);
		$viewPerPage = Mage::helper('pdp')->getViewPerPage();
		
		$data = Mage::helper('pdp')->pagingCollection($current_page, $page_size, $viewPerPage, $collection, $total, $url, $category);
		
		$new_data=array();
		$new_data['paging_text'] = $data['paging_text'];
		foreach ($data['collection'] as $item){
			$new_data['collection'][] = array($item->getData());
		}
		$this->getResponse()->setBody(json_encode($new_data));
	}
	
	public function loadMoreImageAction()
	{
		$current_page = $_POST['current_page'];
		$category = $_POST['category'];
		$pageSize = $_POST['page_size'];
		$pdpObject = new MST_Pdp_Block_Pdp();
		//$size = ceil($collection_counter/$page_size);
		$collection = $pdpObject->pagingCollection($current_page, $category, $pageSize);
		if ( count($collection) > 0) {
			$data = array();
			$pdpObject = Mage::getModel('pdp/pdp');
			foreach ($collection as $image) {
				$colorImg = $pdpObject->getColorImageFrontend($image->getImageId());
				$image->setColorImg($colorImg);
				$data[] = $image->getData();
			}
			$this->getResponse()->setBody(json_encode($data));
		} else {
			$this->getResponse()->setBody("nomore");
		}
	}
	
	public function getColorListAction() {
		$designId = $_POST['design_id'];
		if ($designId != "") {
			echo Mage::getModel('pdp/pdp')->getDesignColor($designId);
		}
	}

	public function updateDesignColorPositionAction() {
		$position = $_POST['position'];
		if ($position != "") {
			echo Mage::getModel('pdp/pdp')->updateDesignColorPosition($position);
		}
	}
	public function updateDesignColorPriceAction() {
		$price = $_POST['price'];
		if ($price != "") {
			echo Mage::getModel('pdp/pdp')->updateDesignColorPrice($price);
		}
	}
	public function updateDesignColorNameAction() {
		$colorName = $_POST['color_name'];
		if ($colorName != "") {
			echo Mage::getModel('pdp/pdp')->updateDesignColorName($colorName);
		}
	}
	public function updateDesignStyleAction() {
		$position = $_POST['position'];
		$price = $_POST['price'];
		$colorName = $_POST['color_name'];
		Mage::getModel('pdp/pdp')->updateDesignStyle($colorName, $price, $position);
	}
	public function saveAdminTemplateAction() {
        $response = array(
            'status' => 'error',
            'message' => 'Unable to save sample data!'
        );
		$data = $this->getRequest()->getPost();
		$result = Mage::getModel('pdp/admintemplate')->saveAdminTemplate($data);
        if($result->getId()) {
            //add product media images
            try {
                //$this->addProductBaseImage($data['product_id'], $data['pdp_design']);
            } catch(Exception $e) {
            
            }
            $response = $result->getData();
            $response['status'] = 'success';
            $response['message'] = Mage::helper('pdp')->__('Sample design have been saved!');
        }
        $this->getResponse()->setBody(json_encode($response));
	}
    public function saveJsonfileAction() {
        $postData = $this->getRequest()->getPost();
        $response = array(
            'status' => 'error',
            'message' => 'Unable to save json file!'
        );
        if(!isset($postData['json_content'])) {
            $this->getResponse()->setBody(json_encode($response));
            return;
        }
        //Side Info
        $sides = array();
        $jsonContent = $postData['json_content'];
        $jsonBaseDir = Mage::getBaseDir('media') . DS . "pdp" . DS . "json" . DS;
		$response = array();
		if(!file_exists($jsonBaseDir)) {
			mkdir($jsonBaseDir, 0777, true);
		}
		if (file_exists($jsonBaseDir)) {
			$jsonBaseUrl = Mage::getBaseUrl('media') . 'pdp/json/';
			$filename = "CustomOption" . time() . '.json';
			try {
				$result = file_put_contents($jsonBaseDir . $filename, $jsonContent);
				if ($result) {
					$jsonFileModel = Mage::getModel('pdp/jsonfile');
					$jsonFileModel->setFilename($filename);
					$jsonFileModel->save();
					if ($jsonFileModel->getId()) {
                        $response['status'] = "success";
						$response['message'] = "Item saved successfully!";
						$response['filename']= $filename;
						$response['id'] = $jsonFileModel->getId();
						$response['full_path'] = $jsonBaseUrl . $filename;
					}
				}
			} catch(Exception $e) {
				$response['message'] = "Can not save json file to database!";
				//Zend_Debug::dump($e);
			}
		} else {
			$response['message'] = "Folder not exists!";
		}
        $this->getResponse()->setBody(json_encode($response));
    }
    protected function addProductBaseImage($productId, $jsonFilename) {
        $baseDir = Mage::getBaseDir("media") . DS . "pdp" . DS . "images" . DS . "thumbnail" . DS;
        $product = Mage::getModel("catalog/product")->load($productId);
        //Check has media or not
        if($product->getId() && ($product->getImage() == "no_selection" || $product->getImage() == null)) {
            try {
                // Add Image To Media Gallery	
                Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID); // Important
                $thumbnails = Mage::helper("pdp")->getThumbnailImage($jsonFilename);
                foreach($thumbnails as $key => $thumbnail) {
                    $temp = explode("/", $thumbnail['image']);
                    $thumbnailFilename = end($temp);
                    if($key == 0) {
                        $product->addImageToMediaGallery($baseDir . $thumbnailFilename, array('image','thumbnail','small_image'), false, false); 
                    } else {
                        $product->addImageToMediaGallery($baseDir . $thumbnailFilename, array(), false, false);
                    }
                }
                $product->save();
            } catch(Exception $e) {
            
            }
        }
    }
	public function uploadCustomImageAction() {
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES["uploads"])) {
			$uploads=$_FILES["uploads"];
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
								$uploadedImages[] = $mediaUrl . $name;
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
		Mage::getSingleton("core/session")->setCustomUploadImages($customImages);
	} 
	public function loadFontsAction() {
		$fonts = $this->getLayout()->createBlock("core/template")->setTemplate("pdp/design/load_font_after.phtml");
		$this->getResponse()->setBody($fonts->toHtml());
	}
    public function uploadClipartAction() {
        $data = $this->getRequest()->getPost();
        //Zend_Debug::dump($data);
        //die;
        //Create artworks folder if not exists
        $artworkPath = Mage::getBaseDir('media') . '/pdp/images/artworks/';
        if (!is_dir($artworkPath)) {
            mkdir($mediaPath, 0755, true);
        }
        //move image to server
        $mediaPath = 'pdp' . DS . 'images' . DS . 'artworks' . DS;
        $filename = Mage::helper("pdp")->saveImage("artwork", $mediaPath);
        //Save image filename to database
        if($filename != "") {
            $pdp = Mage::getModel('pdp/pdp');
            $data['filename'] = $filename;
            $data['image_type'] = 'custom';
            $dataInfo = $pdp->setDesignImage($data);
            if($dataInfo != "") {
                $mediaImageLink = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'pdp/images/artworks/';
                $dataInfo['preview_path'] = $mediaImageLink . $dataInfo['filename'];
                $dataInfo['status'] = "success";
                echo json_encode($dataInfo);
            }
        }
    }
    public function uploadFontAction() {
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $fontPath = Mage::getBaseDir('media') . '/pdp/fonts/';
            $data = $_REQUEST;
            $allowFonts = array("ttf", "otf", "fnt", "fon", "woff", "dfont");
            if (!is_dir($fontPath)) {
                mkdir($fontPath, 0755, true);
            }
            //Font or image
            $uploadPath = $fontPath;
            $filename = $_FILES['file']['name'];
            $ext = substr($filename, strrpos($filename, '.') + 1);
            $filename = str_replace(" ", "_", strtolower($_FILES['file']['name']));
            $data['filename'] = $filename;
            if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath . $filename)){
                //Insert data to db
                $pdp = Mage::getModel('pdp/pdp');
                $filename = explode('.', $data['filename']);
                $name = $filename[0];
                $ext = $filename[1];
                if (in_array(strtolower($ext), $allowFonts)) {
                    $data['name'] = str_replace(" ", "_", strtolower($name));
                    $data['ext'] = $ext;
                    $dataInfo = $pdp->setDesignFont($data);
                }
                //To remove an element after done updating
                if ($dataInfo != "") {
                    $dataInfo['index'] = $_POST['index'];
                    echo json_encode($dataInfo);
                }
            }
            exit;
        }
    }
    public function saveBase64ImageAction() {
        $data = $this->getRequest()->getPost();
        $response = array(
            'status' => 'error',
            'message' => 'Unable to save base 64 image!'
        );
        if(isset($data['base_code_image'])) {
            $baseCode = $data['base_code_image'];
            $thumbnailDir = Mage::getBaseDir("media") . DS . "pdp" . DS . "images" . DS . "thumbnail" . DS;
            $thumbnailUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "pdp/images/thumbnail/";
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0777);
            } 
            if($data['format'] === "jpeg") {
                $data['format'] = "jpg";
            }
            $filename = "thumbnail_image_" . time() . '.' . $data['format'];
            $file = $thumbnailDir . $filename;
            if(substr($baseCode,0,4)=='data'){
                $uri =  substr($baseCode,strpos($baseCode,",")+1);
                // save to file
                file_put_contents($file, base64_decode($uri));
                if(file_exists($file)) {
                    //$thumbnailUrl
                    $response = array(
                        'status' => 'success',
                        'message' => 'Image have been successfully saved!',
                        'thumbnail_path' => $thumbnailUrl . $filename
                    );
                    $this->getResponse()->setBody(json_encode($response));
                }
            }
        }
    }
    // Response for download png request
    public function saveBase64ImageExportAction() {
        $data = $this->getRequest()->getPost();
        $orderInfo = array();
        if(isset($data['options']['order_info'])) {
            $orderInfo = $data['options']['order_info'];
        }
        $response = array(
            'status' => 'error',
            'message' => 'Unable to save base 64 image!'
        );
        if(isset($data['base_code_image'])) {
            $baseCode = $data['base_code_image'];
            if(substr($baseCode,0,4)=='data'){
                $uri =  substr($baseCode,strpos($baseCode,",")+1);
                $pngString = base64_decode($uri);
                $isBackend = 0;
                if(isset($data['options']['is_backend'])) {
                    $isBackend = $data['options']['is_backend'];
                }
                
                $response = $this->createImageFromString($pngString, $orderInfo, "png", $isBackend);
            }
        }
        $this->getResponse()->setBody(json_encode($response));
    }
    //Create image from a string, string might a svg string or base64 string
    protected function createImageFromString($imageString, $orderInfo, $imgExt = 'png', $isBackend = 0) {
        $response = array(
            'status' => 'error',
            'message' => 'Unable to create image from string!'
        );
        if($imageString) {
            $this->setDownloadableWhenExport();
            $thumbnailDir = Mage::getBaseDir("media") . DS . "pdp" . DS . "export" . DS . $imgExt . DS;
            $thumbnailUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "pdp/export/" . $imgExt . "/";
            $filename = $this->getDownloadFilename($orderInfo, $imgExt);
            $file = $thumbnailDir . $filename;
            //If image is svg image, then replace linked image to base64 image
            if($imgExt == "svg") {
                $imageString = Mage::helper("pdp")->convertLinkedToEmbedImage($imageString);
            }
            file_put_contents($file, $imageString);
            if(file_exists($file)) {
                //Add watermark if active, only add watermark to customer design.
                if(!isset($orderInfo['order_id']) && $isBackend == 0) {
                    Mage::helper("pdp/upload")->addWatermark($file);    
                }
                //$thumbnailUrl
                $response = array(
                    'status' => 'success',
                    'message' => 'Image have been successfully saved!',
                    'thumbnail_path' => $thumbnailUrl . $filename,
                    'file_location' => $file
                );
            }
        }
        return $response;
    }
    protected function getDownloadFilename($orderInfo, $fileExt) {
        if(!empty($orderInfo)) {
            $filename = "Design-" . $orderInfo['side_label'] . '-Order-' . $orderInfo['increment_id'] . "-Item-" . $orderInfo['item_id'] . '-' . time() . '.' . $fileExt;
        } else {
            $filename = "Design-" . time() . "." . $fileExt;
        }
        
        return $filename;
    }
    public function saveAndCreateSvgAction() {
        $data = $this->getRequest()->getPost();
        $orderInfo = $data['order_info'];
        $response = array(
            'status' => 'error',
            'message' => 'Unable to save and download svg file!'
        );
        if(isset($data['svg_string'])) { 
            $response = $this->createImageFromString($data['svg_string'], $orderInfo, "svg");
        }
        $this->getResponse()->setBody(json_encode($response));
    }
    public function createPdfFromSvgAction() {
        $data = $this->getRequest()->getPost();
        $orderInfo = $data["order_info"];
        $response = array(
            'status' => 'error',
            'message' => 'Unable to save and download pdf file from svg!'
        );
        if(isset($data['svg_string'])) { 
            $result = $this->createImageFromString($data['svg_string'], $orderInfo, "svg");
            if($result['file_location']) {
                $svgFile = $result['file_location'];
                $filename = $this->getDownloadFilename($orderInfo, "pdf");
                $response = $this->createPDFFromSVG($svgFile, $filename);
            }
        }
        $this->getResponse()->setBody(json_encode($response));
    }
    public function createPdfFromPngAction() {
        $data = $this->getRequest()->getPost();
        $orderInfo = array();
        if(isset($data['order_info'])) {
            $orderInfo = $data['order_info'];
        }
        $response = array(
            'status' => 'error',
            'message' => 'Unable to save and download pdf file from png!'
        );
        if(isset($data['png_string'])) { 
            $baseCode = $data['png_string'];
            if(substr($baseCode,0,4)=='data'){
                $uri =  substr($baseCode,strpos($baseCode,",")+1);
                $pngString = base64_decode($uri);
                $result = $this->createImageFromString($pngString, $orderInfo, "png");
                if($result['file_location']) {
                    $pngFile = $result['file_location'];
                    $filename = $this->getDownloadFilename($orderInfo, "pdf");
                    $response = $this->createPDFFromPng($pngFile, $filename);
                }
            }
        }
        $this->getResponse()->setBody(json_encode($response));
    }
    protected function setDownloadableWhenExport() {
        $exportFolder = Mage::getBaseDir("media") . DS . "pdp" . DS . "export" . DS;
        if (!file_exists($exportFolder)) {
            mkdir($exportFolder, 0777);
        }
        $fileTypes = array("pdf", "png", "svg");
        foreach($fileTypes as $type) {
            $thumbnailDir = Mage::getBaseDir("media") . DS . "pdp" . DS . "export" . DS . $type . DS;
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0777);
            }
            //Check if htaccess file exists or not, this file for downloadable purpose
            if (!file_exists($thumbnailDir . ".htaccess")) {
                $htaccessInfo = "AddType application/octet-stream .pdf \n";
                $htaccessInfo .= "AddType application/octet-stream .svg \n";
                $htaccessInfo .= "AddType application/octet-stream .png \n";
                file_put_contents($thumbnailDir . ".htaccess", $htaccessInfo);
            }
        }
    }
    protected function createPDFFromSVG($svgFile, $filename) {
		 $response = array(
            'status' => 'error',
            'message' => 'Unable to create pdf file!'
        );
		if(!file_exists($svgFile)) {
			return;
		}
		$xml = simplexml_load_file($svgFile);
		$svgFonts = array();
		foreach($xml as $node) {
			try {
				if ($node->text) {
					$textAttr = $node->text->attributes();
					$fontFamily = (string)$textAttr['font-family'];
					if(!in_array($fontFamily, $svgFonts)) {
						$svgFonts[] = $fontFamily;
					}
				}
			} catch(Exception $e) {
	
			}
		}
		if(!empty($svgFonts)) {
			$svgFonts = $this->filterSvgFont($svgFonts);
		}
		$attrs = $xml->attributes();
		$pdfSize = array(floatval($attrs->width), floatval($attrs->height));
        if($pdfSize[0] > 0 && $pdfSize[1] > 0) {
            $svgSizeInMM = $this->getSVGSizeInMM($pdfSize[0], $pdfSize[1]);
            $pdfSize = $svgSizeInMM;
        }
		$pdf = new TCPDF_TCPDF("", "mm", $pdfSize, true, 'UTF-8', false, false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetLeftMargin(0);
		$pdf->SetRightMargin(0);
		$pdf->SetHeaderMargin(0);
		$pdf->SetFooterMargin(0);
		$pdf->setPrintFooter(false);
		$pdf->setPrintHeader(false);
		$pdf->SetAutoPageBreak(TRUE, -$pdf->getBreakMargin());
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		//Set Font
		foreach($svgFonts as $font) {
			$pdf->SetFont($font);
		}
		$pdf->AddPage();
		$pdf->ImageSVG($svgFile, $x=0, $y=0, $w=$pdf->getPageWidth(), $h=0, $link='', $align='', $palign='', $border=0, $fitonpage=true);
		$pdf->close();
		//Close and output PDF document
		//header('Content-type: application/pdf');
		//echo $pdf->Output($filename, 'S');
        
        $pdfDir = Mage::getBaseDir("media") . DS . "pdp" . DS . "export" . DS . "pdf" . DS;
        $pdfUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "pdp/export/pdf/";
        if (!file_exists($pdfDir)) {
            mkdir($pdfDir, 0777);
        } 
        $this->setDownloadableWhenExport();
        $pdfPath = $pdfDir . $filename;
		$pdf->Output($pdfPath, 'F');
        if(file_exists($pdfPath)) {
			$response = array(
                'status' => 'success',
                'message' => 'Pdf have been successfully created!',
                'pdf_url' => $pdfUrl . $filename
            );
		}
        return $response;
	}
    
    //Get DPI to get exactly size of pdf
    private function getPngDPI($pngFile) {
        //Some code to get dpi here
        return 96;
    }
    private function pixelToMM ($pngFile, $pixel) {
        //mm = (pixels * 25.4) / dpi
        //pixels = (mm * dpi) / 25.4
        //there are 25.4 millimeters in an inch
        //Reference Link: http://www.dallinjones.com/2008/07/how-to-convert-from-pixels-to-millimeters/
        $dpi = $this->getPngDPI($pngFile);
        $mm = ($pixel * 25.4) / $dpi;
        return $mm;
    }
    private function getSVGSizeInMM($svgWidthInPixel, $svgHeightInPixel) {
        $dpi = $this->getPngDPI(null);
        return array(
            ($svgWidthInPixel * 25.4) / $dpi,
            ($svgHeightInPixel * 25.4) / $dpi
        );
    }
    private function getImageSize($pngFile) {
        //Return mm
        $pngSize = Mage::helper("pdp/upload")->getImageSize($pngFile);
        if(is_array($pngSize)) {
            $pngInMM = array();
            $pngInMM[0] = $this->pixelToMM($pngFile, $pngSize[0]);
            $pngInMM[1] = $this->pixelToMM($pngFile, $pngSize[1]);
            return $pngInMM;
        }
        return false;
    }
    protected function createPDFFromPng($pngFile, $filename) {
		 $response = array(
            'status' => 'error',
            'message' => 'Unable to create pdf file!'
        );
		if(!file_exists($pngFile)) {
			return;
		}
		$pdfSize = array();//array(floatval($attrs->width), floatval($attrs->height));
        //png size 
        $pngSize = $this->getImageSize($pngFile);
        if(is_array($pngSize)) {
            $pdfSize[0] = $pngSize[0];
            $pdfSize[1] = $pngSize[1];
        }
		$pdf = new TCPDF_TCPDF("", "mm", $pdfSize, true, 'UTF-8', false, false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetLeftMargin(0);
		$pdf->SetRightMargin(0);
		$pdf->SetHeaderMargin(0);
		$pdf->SetFooterMargin(0);
		$pdf->setPrintFooter(false);
		$pdf->setPrintHeader(false);
		$pdf->SetAutoPageBreak(TRUE, -$pdf->getBreakMargin());
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->AddPage();
		$pdf->Image($pngFile, $x=0, $y=0, $w=$pdf->getPageWidth(), $h=0, $link='', $align='', $palign='', $border=0, $fitonpage=true);
		$pdf->close();
		//Close and output PDF document
		//header('Content-type: application/pdf');
		//echo $pdf->Output($filename, 'S');
        
        $pdfDir = Mage::getBaseDir("media") . DS . "pdp" . DS . "export" . DS . "pdf" . DS;
        $pdfUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "pdp/export/pdf/";
        if (!file_exists($pdfDir)) {
            mkdir($pdfDir, 0777);
        } 
        $this->setDownloadableWhenExport();
        $pdfPath = $pdfDir . $filename;
		$pdf->Output($pdfPath, 'F');
        if(file_exists($pdfPath)) {
			$response = array(
                'status' => 'success',
                'message' => 'Pdf have been successfully created!',
                'pdf_url' => $pdfUrl . $filename
            );
		}
        return $response;
	}
    public function downloadTxtAction() {
		$data = $this->getRequest()->getParams();
		$exportString = array();
		//Order Info 
		$bl = "\r\n";
		$tab = "\t";
		$order = Mage::getModel("sales/order")->load($data['order-id']);
        $exportString[] = "Order # " . $order->getRealOrderId();
		$exportString[] = "Created at: " . $order->getData('created_at');
		//Design Info
		$fileContent = Mage::helper('pdp')->getPDPJsonContent($data['jsonfile']);
		$jsonContent = json_decode($fileContent, true);
		foreach ($jsonContent as $side) {
			$clipartString = array();
			$textString = array();
			//Zend_Debug::dump($side);//image_result
			$exportString[] = "****************************************************************************************************";
			$exportString[] = "Side: " . $side['side_name'] . $bl;
			$jsonDecoded = json_decode($side['json'], true);
			for ($j = 0; $j < count($jsonDecoded['objects']) ; $j++) {
				$itemNum = $j + 1;
				$objectType = $jsonDecoded['objects'][$j]['type'];
				if($objectType == "image") {
					$clipartString[] = "$tab $itemNum. " . $jsonDecoded['objects'][$j]['src'];
				} elseif ($objectType == "path-group") {
					$clipartString[] = "$tab $itemNum. " . $jsonDecoded['objects'][$j]['isrc'];
				} else {
					//Zend_Debug::dump($jsonDecoded['objects'][$j]);
					$textString[] = "$tab ----------------------------------------";
					$textString[] = "$tab + text: " . $jsonDecoded['objects'][$j]['text'];
					$textString[] = "$tab + font-size: " . $jsonDecoded['objects'][$j]['fontSize'];
					$textString[] = "$tab + font-family: " . $jsonDecoded['objects'][$j]['fontFamily'];
					$textString[] = "$tab + color: " . $jsonDecoded['objects'][$j]['fill'];
					$textString[] = "$tab ----------------------------------------";
				}
			}
			$exportString[] = "Cliparts items:";
			$exportString[] = join($bl, $clipartString);
			$exportString[] = $bl . "Text items:";
			$exportString[] = join($bl, $textString);
		}
		header("Content-type: text/plain");
		$filename = 'General-Info-'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.txt';
		header("Content-Disposition: attachment; filename=$filename");
		echo join($bl, $exportString);
	}
	protected function filterSvgFont($fonts) {
		$validFont = array();
		$tcpdfFontPath = Mage::getBaseDir("lib") . DS . "TCPDF" . DS . "fonts" . DS;
		$tcpdfFonts = array();
		$directory = $tcpdfFontPath;
		if( is_dir( $directory ) && $handle = opendir( $directory ) )
		{
			while( ( $file = readdir( $handle ) ) !== false )
			{
				$temp = explode(".", $file);
				if(end($temp) == "php") {
					$tcpdfFonts[] = str_replace(".php", "", $file);
				}
			}
		}
		//Compare font
		foreach($fonts as $font) {
			$fontName = trim(strtolower($font)); 
			if(in_array($fontName, $tcpdfFonts)) {
				$validFont[] = $fontName;
			}
		}
		return $validFont;
	}
    public function removeSampleDataAction() {
        $params = $this->getRequest()->getParams();
        $response = array(
            'status' => 'error',
            'message' => 'Can not remove sample design.'
        );
        if(isset($params['product-id']) && $params['product-id']) {
            $collection = Mage::getModel('pdp/admintemplate')->getCollection();
            $collection->addFieldToFilter('product_id', $params['product-id']);
            if (count($collection) > 0) {
                $collection->getFirstItem()->delete();
                $response = array(
                    'status' => 'success',
                    'message' => 'Sample design had been removed.'
                );
            }
        }
        echo json_encode($response);
    }
}