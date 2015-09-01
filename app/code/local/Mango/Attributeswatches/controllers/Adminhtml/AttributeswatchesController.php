<?php

class Mango_Attributeswatches_Adminhtml_AttributeswatchesController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('attributeswatches/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction() {



        //echo "refresh info";

        $refresh = Mage::getModel("attributeswatches/attributeswatches")->refresh();


        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('attributeswatches_id');
        $model = Mage::getModel('attributeswatches/attributeswatches')->load($id);


        //echo $id;

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('attributeswatches_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('attributeswatches/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('attributeswatches/adminhtml_attributeswatches_edit'))
                    ->_addLeft($this->getLayout()->createBlock('attributeswatches/adminhtml_attributeswatches_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('attributeswatches')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            
           //print_r($_FILES);
           // exit;
            

            if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('filename');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png', 'svg'));
                    $uploader->setAllowRenameFiles(false);

                    // Set the file upload mode
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders
                    //	(file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);

                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS . "attributeswatches" . DS;
                    $result = $uploader->save($path); //, $_FILES['mainimage']['name'] );
                    // We set media as the upload dir
                    //$path = Mage::getBaseDir('media') . DS . "attributeswatches" . DS;
                    //$uploader->save($path, $_FILES['filename']['name'] );

                    if ($result) {
                        $data['filename'] = $uploader->getUploadedFileName(); // . $_FILES['mainimage']['name'];
                        //$data["color"]["file"] = $data['filename'];
                    }
                } catch (Exception $e) {

                }

                //this way the name is saved in DB
                //$data['filename'] = $uploader->getUploadedFileName();
            }else{
                $_filename = $this->getRequest()->getParam("filename");
                if(isset($_filename["value"])){
                    $data['filename'] = $_filename["value"];
                }else{
                    $data['filename'] = "";
                }
                
            }


            $model = Mage::getModel('attributeswatches/attributeswatches');
            
           /* echo (int)$data["mode"]; 
            exit;*/
            
            $model->setData($data)
                    ->setMode((int)$data["mode"])
                    //->setColor( json_encode($data["color"]) )
                    ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('attributeswatches')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('attributeswatches')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('attributeswatches/attributeswatches');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

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
        $attributeswatchesIds = $this->getRequest()->getParam('attributeswatches');
        if (!is_array($attributeswatchesIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($attributeswatchesIds as $attributeswatchesId) {
                    $attributeswatches = Mage::getModel('attributeswatches/attributeswatches')->load($attributeswatchesId);
                    $attributeswatches->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($attributeswatchesIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $attributeswatchesIds = $this->getRequest()->getParam('attributeswatches');
        if (!is_array($attributeswatchesIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($attributeswatchesIds as $attributeswatchesId) {
                    $attributeswatches = Mage::getSingleton('attributeswatches/attributeswatches')
                                    ->load($attributeswatchesId)
                                    ->setStatus($this->getRequest()->getParam('status'))
                                    ->setIsMassupdate(true)
                                    ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($attributeswatchesIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'attributeswatches.csv';
        $content = $this->getLayout()->createBlock('attributeswatches/adminhtml_attributeswatches_grid')
                        ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'attributeswatches.xml';
        $content = $this->getLayout()->createBlock('attributeswatches/adminhtml_attributeswatches_grid')
                        ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

}