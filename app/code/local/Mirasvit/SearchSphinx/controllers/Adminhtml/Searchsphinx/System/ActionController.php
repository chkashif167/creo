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



/**
 * @category Mirasvit
 */
class Mirasvit_SearchSphinx_Adminhtml_Searchsphinx_System_ActionController extends Mage_Adminhtml_Controller_Action
{
    protected function _getNativeEngine()
    {
        return Mage::getSingleton('searchsphinx/engine_sphinx_native');
    }

    protected function _getExternalEngine()
    {
        return Mage::getSingleton('searchsphinx/engine_sphinx');
    }

    public function reindexAction()
    {
        $result = array();
        try {
            $message = $this->_getNativeEngine()->reindex();

            $result['message'] = $message;
        } catch (Exception $e) {
            $result['message'] = nl2br($e->getMessage());
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function stopstartAction()
    {
        try {
            $result = array();
            if ($this->_getNativeEngine()->isSearchdRunning()) {
                $this->_getNativeEngine()->stop();
                $result['message'] = Mage::helper('searchsphinx')->__('Stopped');
                $result['btn_label'] = Mage::helper('searchsphinx')->__('Start Sphinx daemon');
            } else {
                $this->_getNativeEngine()->start();
                $result['message'] = Mage::helper('searchsphinx')->__('Launched');
                $result['btn_label'] = Mage::helper('searchsphinx')->__('Stop Sphinx daemon');
            }
        } catch (Exception $e) {
            $result['message'] = nl2br($e->getMessage());
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function generateAction()
    {
        $result = array();
        try {
            $path = $this->_getExternalEngine()->makeConfigFile();

            $result['message'] = Mage::helper('searchsphinx')->__('Sphinx configuration file: '.$path);
        } catch (Exception $e) {
            $result['message'] = nl2br($e->getMessage());
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('search');
    }
}
