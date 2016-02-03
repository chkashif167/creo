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
 * @package   Sphinx Search Ultimate
 * @version   2.3.2
 * @build     1290
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_SearchLandingPage_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();
        $front->addRouter('searchlandingpage', $this);
    }

    /*
     * Do redirect, if search phase is landing page
     */
    public function controllerFrontSendResponseBefore($observer)
    {
        if (Mage::app()->getRequest()->getModuleName() == 'catalogsearch') {
            $page = Mage::getModel('searchlandingpage/page')->getCollection()
                ->addFieldToFilter('query_text', Mage::app()->getRequest()->getParam('q'))
                ->addFieldToFilter('is_active', 1)
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->getFirstItem();

            if ($page->getId()) {
                $search = array('.html/', '.htm/');
                $replace = array('.html', '.htm');
                $parsedUrl = Mage::getModel('core/url')->parseUrl(trim($page->getUrlKey(), '/'));
                $url = Mage::getUrl($parsedUrl->getPath(), array('_query' => $parsedUrl->getQueryParams()));
                $url = str_replace($search, $replace, $url);

                Mage::app()->getResponse()->clearHeaders()->setRedirect($url)->sendResponse();
            }
        }
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        $identifier = trim($request->getPathInfo(), '/');

        $condition = new Varien_Object(array(
            'identifier' => $identifier,
            'continue' => true,
        ));

        $identifier = $condition->getIdentifier();

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

        $page = Mage::getModel('searchlandingpage/page')->checkIdentifier($identifier);

        if (!$page) {
            return false;
        }

        $request->setModuleName('searchlandingpage')
            ->setControllerName('page')
            ->setActionName('view')
            ->setParam('q', $page->getQueryText())
            ->setParam('id', $page->getId());

        $request->setAlias(
            Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            $identifier
        );

        return true;
    }
}
