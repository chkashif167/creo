<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_Sliders_Adminhtml_SlidersController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init actions
     *
     * @return Mage_Adminhtml_Cms_BlockController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('cms/sliders')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        return $this;
    }

    /**
     * Sliders list
     *
     * @return void
     */
    public function indexAction()
    {
		$this->_initAction()
			->renderLayout();
    }

    /**
     * Create new slide
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit action
     *
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('auguria_sliders/sliders')->load($id);
		
        if ($model->getId() || $id == 0) {
        	$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			
			Mage::register('sliders_data', $model);
			
			$this->loadLayout();
			$this->_setActiveMenu('cms/sliders');
			
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
			
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			
			$this->_addContent($this->getLayout()->createBlock('auguria_sliders/adminhtml_sliders_edit'))
					->_addLeft($this->getLayout()->createBlock('auguria_sliders/adminhtml_sliders_edit_tabs'));
			
			$this->renderLayout();
		}
		else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('auguria_sliders')->__('This slider no longer exists.'));
			$this->_redirect('*/*/');
		}
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('auguria_sliders/sliders')->load($id);
            if (!$model->getId() && $id) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('auguria_sliders')->__('This slider no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            
            // Set categories
            if (isset($data['category_ids'])) {
            	$categoryIds = array_unique(explode(',', $data['category_ids']));
            	$data['category_ids'] = $categoryIds;
            }
            
            // Set new image
            if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '')
            {
            	try
            	{
            		/* Starting upload */
            		$uploader = new Varien_File_Uploader('image');
            		 
            		// Any extention would work
            		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            		$uploader->setFilesDispersion(false);
            		 
            		// Upload image and copy into product dir
            		$path = Mage::getBaseDir('media') . DS . 'auguria' . DS . 'sliders' .DS;
            		$fileName = $_FILES['image']['name'];
            		$uploader->save($path, $fileName );
            		 
            	}
            	catch (Exception $e)
            	{
            		Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            		Mage::getSingleton('adminhtml/session')->setFormData($data);
            		$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            		return;
            	}
            	 
            	//this way the name is saved in DB
            	$data['image'] = 'auguria/sliders/'.$_FILES['image']['name'];
            }            
            // Delete old image
            elseif (isset($data['image']['delete'])) {
            	$image = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . $model->getImage();
            	if (file_exists($image)) {
            		unlink($image);
            	}            	
            	$data['image'] = '';
            }
            // Remove null values from data
            elseif (isset($data['image'])) {
            	unset($data['image']);
            }
            
			// Set all data
			if (is_array($data) && count($data)>0) {
				foreach ($data as $key=>$value) {
					$model->setData($key,$value);
				}
			}
            
            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('auguria_sliders')->__('The slider has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     *
     */
    public function deleteAction()
    {    	
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                // init model and delete
                $model = Mage::getModel('auguria_sliders/sliders');
                $model->load($id);
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('auguria_sliders')->__('The slider has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('auguria_sliders')->__('Unable to find the slider to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Delete specified banners using grid massaction
     *
     */
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('sliders');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select items.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getSingleton('auguria_sliders/sliders')->load($id);
                    $model->delete();
                }

                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('auguria_contact')->__('An error occurred while mass deleting contacts. Please review log and try again.'));
                Mage::logException($e);
                return;
            }
        }
        $this->_redirect('*/*/index');
    }


    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/auguria_sliders');
    }
    
    public function categoriesJsonAction()
    {
    	$sliderId = $this->getRequest()->getParam('id');
    	$model = Mage::getModel('auguria_sliders/sliders')->load($sliderId);
    	
    	Mage::register('sliders_data', $model);
    	Mage::register('current_sliders', $model);
    
    	$this->getResponse()->setBody(
    		$this->getLayout()->createBlock('auguria_sliders/adminhtml_sliders_edit_tab_categories')
    			->getCategoryChildrenJson($this->getRequest()->getParam('category'))
    	);
    }
}
