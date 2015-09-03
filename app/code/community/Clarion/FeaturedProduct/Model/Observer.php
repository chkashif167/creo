<?php
/**
 * Featured Product Observer
 * 
 * @category    Clarion
 * @package     Clarion_FeaturedProduct
 * @author      Clarion Magento Team <magento@clariontechnologies.co.in>
 */
class Clarion_FeaturedProduct_Model_Observer
{
    /**
     * Stop default redirect and redirect to current url
     *
     * @param   Varien_Event_Observer $observer
     * @return  Clarion_FeaturedProduct_Model_Observer
     */
     public function stopRedirect(Varien_Event_Observer $observer){
        //get the real referrer from server var
        $referrer = Mage::app()->getRequest()->getServer('HTTP_REFERER');
        if ($referrer){
            //set your new redirect
            Mage::app()->getResponse()->setRedirect($referrer);
        }
        return $this;
    }
}