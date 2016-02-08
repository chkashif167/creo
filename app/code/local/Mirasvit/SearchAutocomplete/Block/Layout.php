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



class Mirasvit_SearchAutocomplete_Block_Layout extends Mage_Core_Block_Template
{
    protected $_css;
    protected $_template;

    public function __construct()
    {
        $this->defineTheme();
    }

    /**
     * Set css and template file for search box.
     */
    protected function defineTheme()
    {
        $theme = Mage::getSingleton('searchautocomplete/config')->getTheme();
        switch ($theme) {
            case 'default':
                $this->_css = Mirasvit_SearchAutocomplete_Model_Config::CSS_DEFAULT;
                $this->_template = Mirasvit_SearchAutocomplete_Model_Config::TPL_DEFAULT;
                break;
            case 'rwd':
                $this->_css = Mirasvit_SearchAutocomplete_Model_Config::CSS_RWD;
                $this->_template = Mirasvit_SearchAutocomplete_Model_Config::TPL_DEFAULT;
                break;
            default:
                $this->_css = Mirasvit_SearchAutocomplete_Model_Config::CSS_AMAZON;
                $this->_template = Mirasvit_SearchAutocomplete_Model_Config::TPL_AMAZON;
                break;
        }

        if (file_exists(Mage::getBaseDir('skin').'/frontend/base/default/'.Mirasvit_SearchAutocomplete_Model_Config::CSS_CUSTOM)) {
            $this->_css = Mirasvit_SearchAutocomplete_Model_Config::CSS_CUSTOM;
        }
    }

    public function addSearchAutocomplete()
    {
        $this->addSearchCss();
        $this->addForm();

        return $this;
    }

    /**
     * Add css file to layout.
     */
    public function addSearchCss()
    {
        $head = $this->getLayout()->getBlock('head');
        if ($head instanceof Mage_Core_Block_Template) {
            if (Mage::getStoreConfig('dev/css/merge_css_files', Mage::app()->getStore()->getId()) && strpos($this->_css, 'custom.css')) {
                $head->addCss(str_replace('custom.css', Mage::getSingleton('searchautocomplete/config')->getTheme().'.css', $this->_css));
            }
            $head->addCss($this->_css);
        }

        return $this;
    }

    /**
     * Add searchautocomplete block to layout.
     */
    public function addForm()
    {
        $searchBlock = $this->getLayout()->getBlock('top.search');
        if (!$searchBlock instanceof Mage_Core_Block_Template) {
            $searchParentBlock = $this->getLayout()->getBlock('header');
        } else {
            $searchParentBlock = $searchBlock->getParentBlock();
            $searchParentBlock->unsetChild($searchBlock->getBlockAlias());
        }

        $searchBlock = $this->getLayout()->createBlock('searchautocomplete/form')
            ->setTemplate($this->_template)
            ->setNameInLayout('top.search')
            ->setAlias('top.search');

        if ($searchParentBlock) {
            $searchParentBlock->setChild('topSearch', $searchBlock);
        }

        // assign for ultimo theme
        $this->getLayout()->setBlock('top.search', $searchBlock);
        $this->getLayout()->getBlock('top.search')->setNameInLayout('top.search');
    }
}
