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



class Mirasvit_SearchLandingPage_PageController extends Mage_Core_Controller_Front_Action
{
    public function viewAction()
    {
        $pageId = (int) $this->getRequest()->getParam('id');
        $page = Mage::getModel('searchlandingpage/page')->load($pageId);

        if (!$page->getId()) {
            $this->_forward('noRoute');
        } else {
            Mage::register('current_searchlandingpage', $page);

            // update layouts
            $update = $this->getLayout()->getUpdate();
            $update->addHandle('default');
            $update->addHandle('catalogsearch_result_index');
            $this->loadLayoutUpdates();
            $this->addActionLayoutHandles();

            $update->addUpdate($page->getLayout());

            $this->generateLayoutXml()->generateLayoutBlocks();
            $this->renderLayout();
        }
    }
}
