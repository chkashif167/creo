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



class Mirasvit_SearchIndex_Block_Adminhtml_Validation_Tab_Speed extends Mage_Adminhtml_Block_Template
{
    private $collection;
    private $searchTime;
    private $resultSize;

    public function _prepareLayout()
    {
        $this->setTemplate('searchindex/validation/tab/speed.phtml');
        $this->collection = $this->getSearchResultCollection();

        return parent::_prepareLayout();
    }

    public function getQ()
    {
        return Mage::app()->getRequest()->getParam('q');
    }

    public function getGridHtml()
    {
        $grid = $this->getLayout()->createBlock('searchindex/adminhtml_validation_tab_speed_grid');
        $grid->setCollection($this->collection);

        return $grid->toHtml();
    }

    private function getSearchResultCollection()
    {
        $collection = new Varien_Data_Collection();
        $index = Mage::helper('searchindex/index')->getIndex('mage_catalog_product');
        $storeId = $this->getStoreId();

        if (($query = $this->getRequest()->getParam('q')) && $index) {
            $engine = Mage::helper('searchindex')->getSearchEngine();

            $start = microtime(true);
            try {
                $result = $engine->query($query, $storeId, $index);
            } catch (Exception $e) {
                try {
                    $engine = Mage::getModel('searchsphinx/engine_fulltext');
                    $result = $engine->query($query, $storeId, $index);
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    $result = array();
                }
            }
            $end = microtime(true);
            $this->searchTime = round($end - $start, 4);

            foreach ($result as $productId => $relevance) {
                $varienObject = new Varien_Object();
                $varienObject->addData(array(
                    'id' => $productId,
                    'relevance' => $relevance,
                ));
                $collection->addItem($varienObject);
            }

            $this->resultSize = $collection->getSize();
        }

        return $collection;
    }

    public function getStoreId()
    {
        $storeId = $this->getRequest()->getParam('store_id');
        if (!$storeId) {
            foreach (Mage::app()->getWebsites(true) as $webSite) {
                if ($webSite->getIsDefault()) {
                    $storeId = $webSite->getDefaultStore()->getId();
                }
            }
        }

        return $storeId;
    }

    public function getSearchTime()
    {
        return $this->searchTime;
    }

    public function getResultCount()
    {
        return $this->resultSize;
    }

    public function getSearchEngineStatus()
    {
        $notice = '';
        $msgClass = 'success-msg';
        $message = $this->__('You use "Built-in Sphinx Search Engine".');
        $engine = Mage::getSingleton('searchsphinx/config')->getSearchEngine();
        if (in_array($engine, array('sphinx', 'sphinx_external'))) {
            $message = $this->__('You use "External Sphinx Engine".');
            if ($engine == 'sphinx' && Mage::helper('searchindex')->getSearchEngine()->isSearchdRunning()) {
                $notice = $this->__('Sphinx successfully running on the server.');
            } elseif ($engine == 'sphinx' && !Mage::helper('searchindex')->getSearchEngine()->isSearchdRunning()) {
                $msgClass = 'notice-msg';
                $url = Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit', array('section' => 'searchsphinx'));
                $notice = $this->__('Sphinx is not running on the server. Please, navigate to the <a href="'.$url.'">Sphinx Search settings</a> and launch it.');
            }
        }

        return '<li class="'.$msgClass.'">'.$message.'<br/>'.$notice.'</li>';
    }

    public function getStoreHtmlSelect()
    {
        $select = Mage::app()->getLayout()->createBlock('core/html_select')
            ->setName('store_id')
            ->setValue($this->getStoreId())
            ->setOptions(Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm());

        return $select->toHtml();
    }
}
