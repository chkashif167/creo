<?php
class MST_Pdp_PrintController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $pdf = new Zend_Pdf();
		$page = $this->newPdfPage();
		//$pageHeight = $page->getHeight();//842
		//$pageWidth = $page->getWidth();//595
		$params = $this->getRequest()->getParams();
		if (isset($params['jsonfile'])) {
			$images = Mage::helper('pdp')->getThumbnailImage($params['jsonfile']);
			for ($i = 0; $i < count($images); $i++) {
				$page = $this->newPdfPage();
				if ($i == 0) {
					$this->insertLogo($page);
				}
				$imgFilename = explode("/", $images[$i]['image']);
				$imgFilename = end($imgFilename);
				$imgPath = Mage::getBaseDir("media") . DS . "pdp" . DS . "sides" . DS . $imgFilename;
				$this->insertSideImage($page, $imgPath);
				/* if (count($images) > 1) {
					$this->insertHeader($page, $i + 1);
				}
				$this->insertFooter($page); */
				$pdf->pages[] = $page;
			}
			//$this->insertSideImage($page, $imgPath);
			/* $designImage = Zend_Pdf_Image::imageWithPath($imgPath);
			$imgHinPixel = $designImage->getPixelHeight();
			$imgWinPixel =  $designImage->getPixelWidth();
			$imageHeight = ($imgHinPixel / 96) * 72;//1 inch = 72 point
			$imageWidth = ($imgWinPixel / 96) * 72;
			$topPos = $pageHeight - 72*2;
			$leftPos = 72;
			$bottomPos = $topPos - $imageHeight;
			$rightPos = $leftPos + $imageWidth;
			
			$page->drawImage($designImage, $leftPos, $bottomPos, $rightPos, $topPos); */
			//Draw text;
			//$startPos = $topPos - 120;
			//array_push($pdf->pages, $page);
			//header('Content-type: application/pdf');
			//echo $pdf->render();
			$this->_prepareDownloadResponse('Design-'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(), 'application/pdf');
			//$this->savePDFFile($pdf->render());
			// It will be called downloaded.pdf
			//$filename = "PDP_Design_" . time();
			//header("Content-Disposition:attachment;filename=pdp_design.pdf");
		}
    }
    private function savePDFFile($content) {
    	$baseDir = Mage::getBaseDir("media") . "/pdp/export/";
    	try {
    		file_put_contents($baseDir . "test-pdf.pdf", $content);
    		echo "Done";
    	} catch(Exception $e) {
    		Zend_Debug::dump($e);
    	}
    }
    public function renderPdfAction() {
    	$pdf = new TCPDF_TCPDF();
    	
    }
	private function newPdfPage(){
		//$page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_LETTER);
		$page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
		$style = new Zend_Pdf_Style();
		//$page->setLineColor(new Zend_Pdf_Color_RGB(0.9, 0, 0));
		//$style->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
		//$style->setLineWidth(3);
		$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 14);
		$page->setStyle($style);
		$pageHeight = $page->getHeight();
		$pageWidth = $page->getWidth();
		//$page->drawRectangle(18, $pageHeight - 18, $pageWidth - 18, 18, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
		return $page;
	}
	protected function _prepareDownloadResponse(
        $fileName,
        $content,
        $contentType = 'application/octet-stream',
        $contentLength = null)
    {
        $isFile = false;
        $file   = null;
        if (is_array($content)) {
            if (!isset($content['type']) || !isset($content['value'])) {
                return $this;
            }
            if ($content['type'] == 'filename') {
                $isFile         = true;
                $file           = $content['value'];
                $contentLength  = filesize($file);
            }
        }

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength, true)
            ->setHeader('Content-Disposition', 'attachment; filename="'.$fileName.'"', true)
            ->setHeader('Last-Modified', date('r'), true);

        if (!is_null($content)) {
            if ($isFile) {
                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();

                $ioAdapter = new Varien_Io_File();
                $ioAdapter->open(array('path' => $ioAdapter->dirname($file)));
                $ioAdapter->streamOpen($file, 'r');
                while ($buffer = $ioAdapter->streamRead()) {
                    print $buffer;
                }
                $ioAdapter->streamClose();
                if (!empty($content['rm'])) {
                    $ioAdapter->rm($file);
                }

                exit(0);
            } else {
                $this->getResponse()->setBody($content);
            }
        }
        return $this;
    }
	protected function insertLogo($page, $store = null)
    {
        $image = Mage::getStoreConfig('sales/identity/logo', $store);
        if ($image) {
            $image = Mage::getBaseDir('media') . '/sales/store/logo/' . $image;
            if (is_file($image)) {
                $image       = Zend_Pdf_Image::imageWithPath($image);
                $top         = 830; //top border of the page
                $widthLimit  = 270; //half of the page width
                $heightLimit = 270; //assuming the image is not a "skyscraper"
                $width       = $image->getPixelWidth();
                $height      = $image->getPixelHeight();
                //preserving aspect ratio (proportions)
                $ratio = $width / $height;
                if ($ratio > 1 && $width > $widthLimit) {
                    $width  = $widthLimit;
                    $height = $width / $ratio;
                } elseif ($ratio < 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $height * $ratio;
                } elseif ($ratio == 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $widthLimit;
                }
                $y1 = $top - $height;
                $y2 = $top;
                $x1 = 25;
                $x2 = $x1 + $width;
                //coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);
            }
        }
    }
	protected function insertSideImage($page, $imgPath)
    {
            if (is_file($imgPath)) {
                $image       = Zend_Pdf_Image::imageWithPath($imgPath);
                $top         = 830 - (72*2); //top border of the page
                $widthLimit  = 415; //half of the page width
                $heightLimit = 415; //assuming the image is not a "skyscraper"
                $width       = $image->getPixelWidth();
                $height      = $image->getPixelHeight();
                //preserving aspect ratio (proportions)
                $ratio = $width / $height;
                if ($ratio > 1 && $width > $widthLimit) {
                    $width  = $widthLimit;
                    $height = $width / $ratio;
                } elseif ($ratio < 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $height * $ratio;
                } elseif ($ratio == 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $widthLimit;
                }
                $y1 = $top - $height;
                $y2 = $top;
                $x1 = ($page->getWidth() - $width) / 2;
                $x2 = $x1 + $width;
				//echo "($x1:$y1) - ($x2:$y2)";//(25:380) - (459.50819672131:830)
				//								 (97.143442622951:271) - (497.85655737705:686)
				//coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);
            }
    }
	protected function insertHeader($page, $pageNumber) {
		$page->drawText("Page | $pageNumber", 520, 810);
	}
	protected function insertFooter($page) {
		$website = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		$page->drawText($website , 20, 20);
	}
	public function generalAction() {
		$data = $this->getRequest()->getParams();
		$exportString = array();
		//Order Info 
		$bl = "\r\n";
		$tab = "\t";
		$order = Mage::getModel("sales/order")->load($data['order-id']);
		//Zend_Debug::dump($order->getData());
		$exportString[] = "Order # " . $order->getRealOrderId();
		$exportString[] = "Created at: " . $order->getData('created_at');
		//Design Info
		$fileContent = Mage::helper('pdp')->getPDPJsonContent($data['jsonfile']);
		$jsonContent = json_decode($fileContent, true);
		foreach ($jsonContent['items'] as $side) {
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
	protected function getZipPath($data) {
		$exportPath = Mage::getBaseDir('media') . '/pdp/export/';
		$orderFolderPath = $exportPath . "order_" . $data['order_id'];
		$_zipFilename = $this->getZipFilename($data);
		$_zipPath = $orderFolderPath . "/" . $_zipFilename;
		return $_zipPath;
	}
	protected function getZipDownloadLink($data) {
		$exportPath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/pdp/export/';
		$orderFolderPath = $exportPath . "order_" . $data['order_id'];
		$_zipFilename = $this->getZipFilename($data);
		$_zipPath = $orderFolderPath . "/" . $_zipFilename;
		return $_zipPath;
	}
	protected function getZipFilename($data) {
		$_zipFilename = "order_" . $data['order_id'] . "_" . $data['type'] . "_files.zip";
		return $_zipFilename;
	}
	public function exportDesignAction() {
		$response = array();
		$data = $this->getRequest()->getPost();
		if ($data['type'] == "pdf") {
			//Render svg and then add to pdf later
			$data['type'] = "svg";
		}
		//Check zip file exists or not, if not create zip file
		$_zipPath = $this->getZipPath($data);
		$_zipFilename = $this->getZipFilename($data);
		if(file_exists($_zipPath)) {
			//Always create new file. Ghi de file cu de font duoc update neu font cua pdf duoc admin upload sau
			/* $response = array(
				'status' => "success",
				'message' => "zip file exists"
			);
			$this->getResponse()->setBody(json_encode($response))->sendResponse();
			exit(); */
		}
		$exportPath = Mage::getBaseDir('media') . '/pdp/export/';
		if(!file_exists($exportPath)) {
			mkdir($exportPath, 0777, true);
		}
		if(file_exists($exportPath)) {
			$orderFolderPath = $exportPath . "order_" . $data['order_id'] . "/";
			if(!file_exists($orderFolderPath)) {
				mkdir($orderFolderPath, 0777, true);
			}
			if(file_exists($orderFolderPath)) {
				$filename = $data['side'] . "_product_" . $data['product_id'] . "_item_" . $data['item_id'] . "." . $data['type'];
				if ($data['type'] == "svg") {
					$content = $data['image_string'];
					//Replace & => &amp;
					$tempContent = str_replace("&", "&amp;", $content);
					$content = $tempContent;
				} elseif($data['type'] == "png") {
					$uri =  substr($data['image_string'],strpos($data['image_string'],",")+1);
					$content = base64_decode($uri);
				}
				$result = file_put_contents($orderFolderPath . $filename, $content);
				if($result) {
					$response = array(
						'status' => 'success'
					);
				}
			} else {
				$response = array(
					'status' => "error",
					'message' => "Can not create folder for order: #ID: " . $data['order_id']
				);
			}
		} else {
			$response = array(
				'status' => "error",
				'message' => "Can not create export folder!"
			);
		}
		$this->getResponse()->setBody(json_encode($response));
	}
	public function downloadDesignAction() {
		$data = $this->getRequest()->getPost();
		$response = array();
		if (empty($data)) {
			$response = array(
					"status" => "error",
					"message" => "Invalid request"
			);
			$this->getResponse()->setBody(json_encode($response));
			return;
		}
		$exportPath = Mage::getBaseDir('media') . '/pdp/export/';
		$orderFolderPath = $exportPath . "order_" . $data['order_id'];
		$zip = new ZipArchive();
		$_zipPath = $this->getZipPath($data);
		$_zipFilename = $this->getZipFilename($data);
		if(file_exists($_zipPath)) {
			$downloadLink = $this->getZipDownloadLink($data);
			$this->download($downloadLink);
		}
		$zip->open( $_zipPath, ZipArchive::CREATE );
		//Read order folder
		$directory = $orderFolderPath;
		if( is_dir( $directory ) && $handle = opendir( $directory ) )
		{
			while( ( $file = readdir( $handle ) ) !== false )
			{
				$temp = explode(".", $file);
				if(end($temp) == $data['type']) {
					$zip->addFile($directory . "/" . $file, $file);
				}
			}
		}
		$zip->close();
		if(file_exists($_zipPath)) {
			$downloadLink = $this->getZipDownloadLink($data);
			$this->download($downloadLink);
		} else {
			$response = array(
				"status" => "error",
				"message" => "Zip file not found!"
			);
		}
		$this->getResponse()->setBody(json_encode($response));
	}
	public function downloadPdfAction() {
		$data = $this->getRequest()->getPost();
		$_zipPath = $this->getZipPath($data);
		$_zipFilename = $this->getZipFilename($data);
		//Create new svg and pdf from any export request => Refresh font
		/* if(file_exists($_zipPath)) {
			$downloadLink = $this->getZipDownloadLink($data);
			$this->download($downloadLink);
		} */
		$exportPath = Mage::getBaseDir('media') . '/pdp/export/';
		$orderFolderPath = $exportPath . "order_" . $data['order_id'];
		$directory = $orderFolderPath;
		$counter = 1;
		if( is_dir( $directory ) && $handle = opendir( $directory ) )
		{
			while( ( $file = readdir( $handle ) ) !== false )
			{
				$temp = explode(".", $file);
				if(end($temp) == "svg") {
					//Render Pdf
					$filename = $temp[0] . ".pdf";
					$this->createPDF($orderFolderPath . DS . $file, $filename);
				}
				$counter++;
			}
		}
		//Time to create pdf zip file to download
		$zip = new ZipArchive();
		$_zipPath = $this->getZipPath($data);
		$_zipFilename = $this->getZipFilename($data);
		//Create new svg and pdf from any export request => Refresh font
		/* if(file_exists($_zipPath)) {
			$downloadLink = $this->getZipDownloadLink($data);
			$this->download($downloadLink);
		} */
		$zip->open( $_zipPath, ZipArchive::CREATE );
		//Read order folder
		$directory = $orderFolderPath;
		if( is_dir( $directory ) && $handle = opendir( $directory ) )
		{
			while( ( $file = readdir( $handle ) ) !== false )
			{
				$temp = explode(".", $file);
				if(end($temp) == $data['type']) {
					$zip->addFile($directory . "/" . $file, $file);
				}
			}
		}
		$zip->close();
		if(file_exists($_zipPath)) {
			$downloadLink = $this->getZipDownloadLink($data);
			$this->download($downloadLink);
		} else {
			$response = array(
					"status" => "error",
					"message" => "Zip file not found!"
			);
		}
		$this->getResponse()->setBody(json_encode($response));
	}
	protected function createPDF($svgFile, $filename) {
		
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
		$pdf = new TCPDF_TCPDF("P", "pt", $pdfSize, true, 'UTF-8', false, false);
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
		$pdfPathTemp = explode(DS, $svgFile);
		array_pop($pdfPathTemp);
		$pdfPath = join(DS, $pdfPathTemp) . DS . $filename;
		$pdf->Output($pdfPath, 'F');
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
	protected function download($downloadLInk) {
		/* //Download zip file
		header( "Pragma: public" );
		header( "Expires: 0" );
		header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header( "Cache-Control: public" );
		header( "Content-Description: File Transfer" );
		header( "Content-type: application/zip" );
		header( "Content-Disposition: attachment; filename=\"" . $filename . "\"" );
		header( "Content-Transfer-Encoding: binary" );
		readfile( $filepath );
		exit(); */
		$response = array(
			'status' => "download",
			'path' => $downloadLInk
		);
		echo json_encode($response);
		exit();
	}
	public function downloadPngBgAction() {
		$data = $this->getRequest()->getPost();
		$images = json_decode($data['images'], true);
		$resultPngPath = Mage::getBaseDir("media") . DS . "pdp" . DS . "sides" . DS; 
		$exportPath = Mage::getBaseDir('media') . '/pdp/export/';
		if(!file_exists($exportPath)) {
			mkdir($exportPath, 0777, true);
		}
		$orderFolderPath = $exportPath . "order_" . $data['order_id'] . "/";
		if(!file_exists($orderFolderPath)) {
			mkdir($orderFolderPath, 0777, true);
		}
		$_zipPath = $this->getZipPath($data);
		$_zipFilename = $this->getZipFilename($data);
		if(file_exists($_zipPath)) {
			$downloadLink = $this->getZipDownloadLink($data);
			$this->download($downloadLink);
		}
		$zip = new ZipArchive();
		$zip->open( $_zipPath, ZipArchive::CREATE );
		foreach ($images as $file) {
			$zip->addFile($resultPngPath . $file, $file);
		}
		$zip->close();
		if(file_exists($_zipPath)) {
			$downloadLink = $this->getZipDownloadLink($data);
			$this->download($downloadLink);
		} else {
			$response = array(
					"status" => "error",
					"message" => "Zip file not found!"
			);
		}
		$this->getResponse()->setBody(json_encode($response));
	}
}