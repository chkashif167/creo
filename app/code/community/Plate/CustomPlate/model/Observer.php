<?php
/**
 * Description of Observer
 *
 * @author uzair
 */
class Plate_CustomPlate_Model_Observer {


public function addPostData(Varien_Event_Observer $observer) {

      $action = Mage::app()->getFrontController()->getAction();
      if ($action->getFullActionName() == 'checkout_cart_add') {
         if($action->getRequest()->getParam('customplate')) {
             // ID IS PRESENT, SO LETS ADD IT
             $item = $observer->getProduct();
             $additionalOptions = array();
             $additionalOptions[] = array(
                'label' => 'Saudi Plate Number',
                'value' => $action->getRequest()->getParam('customplate')
            );
             $item->addCustomOption('additional_options', serialize($additionalOptions));
         }
      }

   }


}