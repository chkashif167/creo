<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Product Feeds
 * @version   1.1.4
 * @build     702
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_MstCore_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard
{
    public function addUrlsRouter($observer)
    {
        $front = $observer->getEvent()->getFront();
        $urlsRouter = new Mirasvit_MstCore_Controller_Router();
        $front->addRouter('mstcore', $urlsRouter);
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        $pathInfo = $request->getPathInfo();
        $result = Mage::helper('mstcore/urlrewrite')->match($pathInfo);
        if ($result) {
            $params = array();
            if ($result->getEntityId()) {
                $params['id'] = $result->getEntityId();
            }
            $params = array_merge($params, $result->getActionParams());
            $request
                ->setRouteName($result->getRouteName())
                ->setModuleName($result->getModuleName())
                ->setControllerName($result->getControllerName())
                ->setActionName($result->getActionName())
                ->setParams($params)
                ->setAlias(
                    Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                    $result->getRouteName()
                );
            return true;
        }
        return false;
    }
}
