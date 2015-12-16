<?php
class MST_Pdp_Adminhtml_DesignareaController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('pdp/pdp')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage PDP Group'), Mage::helper('adminhtml')->__('Manage PDP Group'));

        return $this;
    }
    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('pdp/adminhtml_designarea'));
        $this->renderLayout();
    }
	public function addAction()
	{
		$this->loadLayout()->_setActiveMenu('pdp/pdp');
        $this->renderLayout();
	}
	public function editAction()
    {
    	//error_reporting(E_ALL | E_STRICT);
    	//ini_set('display_errors',true);
    	//Mage::setIsDeveloperMode(true);
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('pdp/designarea')->load($id);
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
				
            }
            Mage::register('designarea_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('pdp/pdp');
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage PDP Design Area'), Mage::helper('adminhtml')->__('Manage PDP Design Area'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('pdp/adminhtml_designarea_edit'))
                ->_addLeft($this->getLayout()->createBlock('pdp/adminhtml_designarea_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pdp')->__('Design area does not exist'));
            $this->_redirect('*/*/');
        }
    }
    public function newAction()
    {
        $this->_forward('edit');
    }
	public function saveAction() {
        $data = $this->getRequest()->getPost();
		$data['id'] = $data['side_id'];
		$productId = $data['product_id'];
		if ($data) {
            $model = Mage::getModel('pdp/pdpside');
			$image = Mage::helper('pdp')->saveImage('filename', 'pdp/images/');
			if ($image != "") {
				$data['filename'] = $image;
			}
			$overlayImg = Mage::helper('pdp')->saveImage('overlay', 'pdp/images/');
			if ($overlayImg != "") {
				$data['overlay'] = $overlayImg;
			}
			//Zend_Debug::dump($data);die;
            try {
				// edit by david			
				$main_domain = Mage::helper('pdp')->get_domain( $_SERVER['SERVER_NAME'] );		
				if ( $main_domain != 'dev' ) { 
				$rakes = Mage::getModel('pdp/act')->getCollection();
				$rakes->addFieldToFilter('path', 'pdp/act/key' );
				$valid = false;
				if ( count($rakes) > 0 ) {
					foreach ( $rakes as $rake )  {
						if ( $rake->getExtensionCode() == md5($main_domain.trim(Mage::getStoreConfig('pdp/act/key')) ) ) {
							$valid = true;	
						}
					}
				}
				if ( $valid == false )  {
					Mage::getSingleton('adminhtml/session')->addError( base64_decode('UGxlYXNlIGVudGVyIGxpY2Vuc2Uga2V5ICE=') );
					Mage::getSingleton('adminhtml/session')->setFormData($data);
                    $url = Mage::helper("adminhtml")->getUrl("pdpadmin/adminhtml_designarea/addNewSide/", array ("productid" => $productId));
                    Mage::app()->getResponse()->setRedirect($url)->sendResponse();
					return;
				}
				}
				// end edit by david
                //Valid color code
                if($data['color_code'] !== "") {
                    $data['color_code'] = str_replace("#", "", $data['color_code']);
                }
                $sideId = $model->saveProductSide($data);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('pdp')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
				//$editUrl = Mage::helper("adminhtml")->getUrl("pdp/adminhtml_designarea/addNewSide/", array ("side_id" => $sideId, "productid" => $productId));
				//Mage::app()->getResponse()->setRedirect($editUrl)->sendResponse();
                $this->getResponse()->setBody("<script>window.top.Windows.closeAll();</script>");
            } catch (Exception $e) {
                //Mage::getSingleton('adminhtml/session')->addError( "Error " . $e->getMessage());
                //Mage::getSingleton('adminhtml/session')->setFormData($data);
				echo Mage::helper('pdp')->__('Unable to save item.');
                return;
            }
        }
        echo Mage::helper('pdp')->__('Unable to find item to save');
        //$this->_redirect('*/');
    }
	
    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('pdp/designarea')->load($this->getRequest()->getParam('id'));
                $model->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
	public function massDeleteAction() {
        $itemIds = $this->getRequest()->getParam('group');
        if (!is_array($itemIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($itemIds as $itemId) {
                    $model = Mage::getModel('pdp/designarea')->load($itemId);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($itemIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
	public function setupareaAction() {
		$request = $this->getRequest()->getParams();
		$areaId = $request['areaid'];
		$designArea = Mage::getModel('pdp/designarea')->load($areaId);
		$this->loadLayout();
		//$messages = Mage::getSingleton('adminhtml/session')->getMessages()->getLastAddedMessage();
		//Zend_Debug::dump(Mage::getSingleton('adminhtml/session')->getMessages()->getItems());
		$block =  $this->getLayout()->createBlock('core/template')->setTemplate('pdp/product/design_area.phtml')
				->setData('is_required', $designArea->getIsRequired())
				->setData('product_id', $request['productid'])
				//->setData('messages', array('text' => $messages->getText(), 'code' => $messages->getCode()))
				->setData('design_area', $designArea->getData())
				->toHtml();
		//$itemsModel = Mage::getModel('pdp/designareaitem')->getCollection();
		$this->getResponse()->setBody($block);
	}
	public function saveDesignInlayAction() {
		$data = $this->getRequest()->getPost();
		$helper = Mage::helper('pdp');
		$model = Mage::getModel('pdp/pdp');
		$inlayModel = Mage::getModel('pdp/printarea');
		$filename = $helper->saveImage('filename', 'pdp/images/');
		$designAreaData['canvas_w'] = $data['iwidth'];
		$designAreaData['canvas_h'] = $data['iheight'];
		$designAreaData['canvas_t'] = $data['itop'];
		$designAreaData['canvas_l'] = $data['ileft'];
		$designAreaData['filename'] = $filename;
		if ($data['inlay_id'] == "") {
			$designAreaData['id'] = NULL;
		} else {
			$designAreaData['id'] = $data['inlay_id'];
			//Get old image filename if filename not have any image
			if ($filename == "") {
				$filename = $inlayModel->load($data['inlay_id'])->getFilename();
				$designAreaData['filename'] = $filename;
			}
		}
		if ($filename != "") {
			$inlayId = $model->setDesignPrintarea($designAreaData);
			if ($inlayId != "") {
				$_productId = $data['current_product_id'];
				$_sideId = $data['area_id'];
				$itemId = $model->saveDesignAreaItem($_productId, $_sideId, $inlayId);
				if ($itemId != "") {
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('pdp')->__('Item was successfully saved'));
					$url = Mage::helper("adminhtml")->getUrl("pdpadmin/adminhtml_designarea/setuparea/",array("productid"=> $_productId, "areaid" => $_sideId ));
					Mage::app()->getResponse()->setRedirect($url)->sendResponse();
				}
			}
		}
	}
	public function addDesignColorAction() {
        $this->initLayoutMessages('adminhtml/session');
		$block = $this->getLayout()->createBlock('core/template')->setTemplate('pdp/product/add_design_color.phtml')->toHtml();
		$this->getResponse()->setBody($block);
	}
	public function viewDesignColorAction() {
        $this->initLayoutMessages('adminhtml/session');
		$block =  $this->getLayout()->createBlock('core/template')->setTemplate('pdp/product/view_design_color.phtml')->toHtml();
		$this->getResponse()->setBody($block);
	}
	public function saveDesignColorAction() {
		$data = $this->getRequest()->getPost();
		$colorModel = Mage::getModel('pdp/pdpcolor');
		$productColorInfo = $data['pdpcolor'];
		try {
            $colorThumbnail = Mage::helper('pdp')->saveImage('color_thumbnail', 'pdp/images/color-thumbnail/');
            $productColorInfo['color_thumbnail'] = $colorThumbnail;
            $productColorId = $colorModel->saveProductColor($productColorInfo);
			if ($productColorId) {
				//Product Color Images
				$productColorImageInfo['product_color_id'] = $productColorId;
				$colorImageModel = Mage::getModel('pdp/pdpcolorimage');
				foreach ($data['design_sides'] as $sideId) {
					$productColorImageInfo['side_id'] = $sideId;
					$filename = Mage::helper('pdp')->saveImage('color_image_' . $sideId, 'pdp/images/');
                    $overlayFilename = Mage::helper('pdp')->saveImage('overlay_image_' . $sideId, 'pdp/images/');
                    if($filename != "" && $overlayFilename != "") {
                        $productColorImageInfo['filename'] = $filename;
                        $productColorImageInfo['overlay'] = $overlayFilename;
				        $colorImageModel->saveProductColorImage($productColorImageInfo);
                    }
				}
			}
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('pdp')->__('Item was successfully saved'));
		} catch (Exception $e) {
			//Zend_Debug::dump($e);	
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pdp')->__('Can not save design color!'));
		}
		
		$url = Mage::helper("adminhtml")->getUrl("pdpadmin/adminhtml_designarea/viewdesigncolor/",array("productid"=> $productColorInfo['product_id'] ));
		Mage::app()->getResponse()->setRedirect($url)->sendResponse();
		/* $_colorNameArr = $data['color_name'];
		$_positionArr = $data['position'];
		$_helper = Mage::helper('pdp');
		$_model = Mage::getModel('pdp/designcolor');
		$_designColor = array();
		foreach ($_colorNameArr as $key => $value) {
			$_designColor['color_name'] = $value;
			$_designColor['filename'] = $_helper->saveImage("filename_" . $key, "pdp/images/");
			$_designColor['inlay_id'] = $data['inlay_id'];
			$_designColor['base_filename'] = $data['base_filename'];
			$_designColor['position'] = $_positionArr[$key];
			$_model->saveDesignColorImage($_designColor);
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('pdp')->__('Item was successfully saved'));
		//$url = Mage::helper("adminhtml")->getUrl("pdp/adminhtml_designarea/setuparea/",array("productid"=> $_productId, "areaid" => $_areaId ));
		//$this->getResponse()->setRedirect($url); */
	}
	public function updateDesignColorAction() {
		$data = $this->getRequest()->getPost();
		$pdpColorModel = Mage::getModel('pdp/pdpcolor');
        //Zend_Debug::dump($data); die;
		foreach ($data['status'] as $key => $value) {
			$productColor['status'] = $value;
			$productColor['position'] = $data['position'][$key];
            $productColor['color_name'] = $data['color_name'][$key];
            if(isset($data['color_thumbnail']) && isset($data['color_thumbnail'][$key])) {
                $productColor['color_thumbnail'] = $data['color_thumbnail'][$key];
            }
			$productColor['id'] = $key;
			//Zend_Debug::dump($productColor);
            if(isset($data['remove_thumbnail'])) {
                if(array_key_exists($key, $data['remove_thumbnail'])) {
                    $productColor['color_thumbnail'] = "";
                }
            }
            //Color thumbnail if exists
            if(isset($_FILES['color_thumbnail_' . $key]) && $_FILES['color_thumbnail_' . $key]['name']) {
                $colorThumbnail = Mage::helper('pdp')->saveImage('color_thumbnail_' . $key, 'pdp/images/color-thumbnail/');
                $productColor['color_thumbnail'] = $colorThumbnail;
            }
            //Zend_Debug::dump($productColor);
			$pdpColorModel->saveProductColor($productColor);
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('pdp')->__('Item was successfully saved'));
		$url = Mage::helper("adminhtml")->getUrl("pdpadmin/adminhtml_designarea/viewdesigncolor/",array("productid"=> $data['product_id'] ));
		Mage::app()->getResponse()->setRedirect($url)->sendResponse();
	}
	public function deleteProductColorAction() {
		$productColorId = $this->getRequest()->getParam('delete');
		Mage::getModel('pdp/pdpcolor')->deleteProductColor($productColorId);
	}
	public function previewDesignColorAction() {
		$request = $this->getRequest()->getParams();
		$areaId = $request['areaid'];
		$productId = $request['productid'];
		//$this->loadLayout();
		$inlayInfo = Mage::helper('pdp')->getPrintAreaInfo($productId, $areaId);
		$block =  $this->getLayout()->createBlock('core/template')->setTemplate('pdp/product/preview_design_color.phtml')
				  ->setData('inlay_info', $inlayInfo)
				  ->toHtml();
		$this->getResponse()->setBody($block);
	}
	public function getSideTableAction() {
		$productId = $this->getRequest()->getParam('productid');
		$block = $this->getLayout()->createBlock('core/template')
		->setTemplate('pdp/product/side_table.phtml')
		->setData('product_id', $productId)
		->toHtml();
		$this->getResponse()->setBody($block);
	}
	public function updateStatusAction() {
		$data = $this->getRequest()->getParams();
		Mage::getModel('pdp/productstatus')->setProductStatus($data);
	}
	public function addNewSideAction() {
		//$this->loadLayout();
        $this->initLayoutMessages('adminhtml/session');
		$block = $this->getLayout()->createBlock('core/template')
		->setTemplate('pdp/product/new_side.phtml')
		->toHtml();
		$this->getResponse()->setBody($block);
		//$this->renderLayout();
	}
	public function deleteSideAction() {
		$sideId = $this->getRequest()->getParam('side_id');
		Mage::getModel('pdp/pdpside')->load($sideId)->delete();
	}
	public function inlineUpdateAction() {
		$params = $this->getRequest()->getParams();
		Mage::getModel('pdp/pdpside')->inlineUpdate($params);
	}
	public function updateProductConfigAction() {
		$data = $this->getRequest()->getParams();
		Mage::getModel('pdp/productstatus')->setProductConfig($data);
	}
}