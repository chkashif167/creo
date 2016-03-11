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



class Mirasvit_MstCore_Adminhtml_Mstcore_ValidatorController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()
            ->_addContent($this->getLayout()->createBlock('mstcore/adminhtml_validator'))
            ->renderLayout();
    }

    public function clearCacheAction()
    {
        $clearedCaches = array();
        $usedCaches = Mage::helper('mstcore')->getUsedCaches();
        if ($method = $this->getRequest()->getParam('cache_method')) {
            $usedCaches = array_intersect($usedCaches, array($method));
        }

        try {
            foreach ($usedCaches as $name => $method) {
                $method();
                $clearedCaches[] = $name;
            }
            $this->_getSession()->addSuccess($this->__('The external cache storage has been flushed (%s).', implode(', ', $clearedCaches)));
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirectUrl($this->_getRefererUrl());
    }

    public function compilerAction()
    {
        $compiler = Mage::getModel('compiler/process');
        $msg = '';
        try {
            switch ($this->getRequest()->getParam('action')) {
                case 'disable':
                    $compiler->registerIncludePath(false);
                    $msg = 'Compiler include path disabled';
                    break;
                case 'recompile':
                    $compiler->clear();
                    $compiler->run();
                    $msg = 'Compilation successfully finished';
                    break;
            }
            $this->_getSession()->addSuccess($msg);
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirectUrl($this->_getRefererUrl());
    }

    /*
     * Admin ACL fix to work with SUPEE-6482 security patch. Reads permission from Role Resource: System -> Tools -> Mirasvit Extensions Validator
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/tools/mst_validator');
    }
}
