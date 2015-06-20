<?php
class MST_Pdp_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();
        $front->addRouter('pdp', $this);
    }
    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::app()->isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
		$identifier = trim($request->getPathInfo(), '/');
        $condition = new Varien_Object(array(
            'identifier' => $identifier,
            'continue'   => true
        ));
		if ($condition->getRedirectUrl()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($condition->getRedirectUrl())
                ->sendResponse();
            $request->setDispatched(true);
            return true;
        }
	
        if (!$condition->getContinue()) {
            return false;
        }
		
		$pdpDefaultUrlKey = Mage::getStoreConfig('pdp/setting/urlkey');
        if ($identifier != $pdpDefaultUrlKey || $pdpDefaultUrlKey == "") {
            return false;
        }

        $request->setModuleName('pdp')
            ->setControllerName('view')
            ->setActionName('index');
           
        $request->setAlias(
            Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            $identifier
        );        
        return true;
    }
}
