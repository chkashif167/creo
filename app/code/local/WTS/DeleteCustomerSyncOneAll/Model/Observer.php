<?php

/**
 * Description of Observer
 *
 * @author imran
 */
class WTS_DeleteCustomerSyncOneAll_Model_Observer
{

    public function deleteCustSyncOneAll($observer)
    {
        $custId = $observer->getCustomer()->getId();
        Mage::log("+++"."Now deleting the customer entry (#$custId) for social login in WTS_DeleteCustomerSyncOneAll_Model_Observer.");

        //delete user from social login as well.
        try {
            $model = Mage::getModel('oneall_sociallogin/entity')->getCollection()->addFieldToFilter('customer_id',array('eq'=>$custId));
            $model->walk('delete');
            Mage::log("Deleted entry from social login via WTS_DeleteCustomerSyncOneAll_Model_Observer.");
        } catch (Exception $e) {
            //throw Mage::throwException($e->getMessage());//no need to show any message, just log it.
            Mage::logException("ERROR:".$e->getMessage()." in WTS_DeleteCustomerSyncOneAll_Model_Observer for cust_id:".$custId);
        }
    }

}
