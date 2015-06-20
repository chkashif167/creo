<?php



class MST_Pdp_Adminhtml_ColorController extends Mage_Adminhtml_Controller_Action

{



    protected function _initAction()

    {

        $this->loadLayout()

            ->_setActiveMenu('pdp/pdp');



        return $this;

    }

    public function indexAction()

    {

        $this->_initAction();

        $this->_addContent($this->getLayout()->createBlock('pdp/adminhtml_color'));

        $this->renderLayout();

    }

    public function newAction()

    {

        $this->loadLayout()

            ->_setActiveMenu('pdp/pdp');

        $this->_addContent($this->getLayout()->createBlock('pdp/adminhtml_color_edit'))

        ->_addLeft($this->getLayout()->createBlock('pdp/adminhtml_color_edit_tabs'));

        

        $this->renderLayout();

    }

    public function editAction()

    {

    	$this->_forward("new");

    }

    public function saveAction() {
	
		//david
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
				$this->_redirect('pdp/adminhtml_color/index');
				return;
			}
		}

        $data = $this->getRequest()->getPost();

        $hexcode = $data['color_code'];

        $data['color_code'] = strtoupper($hexcode);

		/* echo "<pre>";

		print_r($data);

		die(); */

		if ($data) {

            $model = Mage::getModel('pdp/color');

            $model->setData($data)->setId($this->getRequest()->getParam('id'));

            try {

                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('pdp')->__('Color category was successfully saved'));

                Mage::getSingleton('adminhtml/session')->setFormData(false);



                if ($this->getRequest()->getParam('back')) {

                    $this->_redirect('*/*/edit', array('id' => $model->getId()));

                    return;

                }

                $this->_redirect('*/*/');

                return;

            } catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

                Mage::getSingleton('adminhtml/session')->setFormData($data);

                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

                return;

            }

        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('pdp')->__('Unable to find item to save'));

        $this->_redirect('*/*/');

    }

    public function deleteAction() {

        if ($this->getRequest()->getParam('id') > 0) {

            try {

                $model = Mage::getModel('pdp/color')->load($this->getRequest()->getParam('id'));

                $model->delete();



                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Color was successfully deleted'));

                $this->_redirect('*/*/');

            } catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

            }

        }

        $this->_redirect('*/*/');

    }

    public function massDeleteAction() {

        $itemIds = $this->getRequest()->getParam('color');

        if (!is_array($itemIds)) {

            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));

        } else {

            try {

                foreach ($itemIds as $itemId) {

                    $model = Mage::getModel('pdp/color')->load($itemId);

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

    public function massStatusAction()

    {

        $menuIds = $this->getRequest()->getParam('color');

        if (!is_array($menuIds)) {

            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select menu(s)'));

        } else {

            try {

                foreach ($menuIds as $menuId) {

                    $seatcover = Mage::getSingleton('pdp/color')

                            ->load($menuId)

                            ->setStatus($this->getRequest()->getParam('status'))

                            ->setIsMassupdate(true)

                            ->save();

                }

                $this->_getSession()->addSuccess(

                    $this->__('Total of %d record(s) were successfully updated', count($menuIds))

                );

            } catch (Exception $e) {

                $this->_getSession()->addError($e->getMessage());

            }

        }

        $this->_redirect('*/*/index');

    }
    public function exportCsvAction()
    {
        $fileName   = 'color.csv';
        $content    = $this->getLayout()->createBlock('pdp/adminhtml_color_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }
      public function exportXmlAction()
    {
        $fileName   = 'color.xml';
        $content    = $this->getLayout()->createBlock('pdp/adminhtml_color_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    
}