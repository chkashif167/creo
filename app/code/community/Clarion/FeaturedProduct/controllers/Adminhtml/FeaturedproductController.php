<?php
/**
 * Admin manage featured product controller
 * 
 * @category    Clarion
 * @package     Clarion_FeaturedProduct
 * @author      Clarion Magento Team <magento@clariontechnologies.co.in>
 * 
 */
class Clarion_FeaturedProduct_Adminhtml_FeaturedproductController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init actions
     *
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->_title($this->__('Featured Product'));
        
        $this->loadLayout()
            ->_setActiveMenu('catalog/featured_product')
            ->_addBreadcrumb(Mage::helper('clarion_featuredproduct')->__('Featured Product')
                    , Mage::helper('clarion_featuredproduct')->__('Featured Product'));
        return $this;
    }
    
    /**
     * Index action method
     */
    public function indexAction() 
    {
        $this->_initAction();
        $this->renderLayout();
    }
    
    /**
     * Used for Ajax Based Grid
     *
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('clarion_featuredproduct/adminhtml_featuredproduct_grid')->toHtml()
        );
    }
    
    /**
     * Unset featured product
     *
     */
    public function massUnsetFeaturedAction()
    {
        //get selected store ids
        $productIds = (array)$this->getRequest()->getParam('product');
        $storeId    = (int)$this->getRequest()->getParam('store', 0);
        
        $isFeaturedProduct = 0;
        
        if (!is_array($productIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select product(s)'));
        } else{
            try {
                //$this->_validateMassStatus($productIds, $status);
                Mage::getSingleton('catalog/product_action')
                    ->updateAttributes($productIds, array('is_featured_product' => $isFeaturedProduct), $storeId);

                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been updated.', count($productIds))
                );
            }
            catch (Mage_Core_Model_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()
                    ->addException($e, $this->__('An error occurred while unmarking featued product(s).'));
            }
        }
        

        $this->_redirect('*/*/', array('store'=> $storeId));
    }
    
    /**
     * Set featured product
     *
     */
    public function massSetFeaturedAction()
    {
        //get selected store ids
        $productIds = (array)$this->getRequest()->getParam('product');
        $storeId    = (int)$this->getRequest()->getParam('store', 0);
        
        $isFeaturedProduct = 1;
        
        if (!is_array($productIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select product(s)'));
        } else{
            try {
                //$this->_validateMassStatus($productIds, $status);
                Mage::getSingleton('catalog/product_action')
                    ->updateAttributes($productIds, array('is_featured_product' => $isFeaturedProduct), $storeId);

                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been updated.', count($productIds))
                );
            }
            catch (Mage_Core_Model_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()
                    ->addException($e, $this->__('An error occurred while unmarking featued product(s).'));
            }
        }
        $this->_redirect('*/*/', array('store'=> $storeId));
    }
}