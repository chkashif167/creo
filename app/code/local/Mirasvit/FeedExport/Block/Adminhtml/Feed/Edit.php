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



class Mirasvit_FeedExport_Block_Adminhtml_Feed_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    const URL_PATH_GENERATE = 'feedexportfront/generate/run';
    const URL_PATH_STATE = '*/*/state';
    const URL_PATH_TEST = 'feedexportfront/generate/test';

    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'feed_id';
        $this->_blockGroup = 'feedexport';
        $this->_controller = 'adminhtml_feed';

        if ($this->getModel()->getId() > 0) {
            $this->_addButtons();
        } else {
            $this->_removeButton('save');
        }

        $this->_formScripts[] = "
            function saveAndContinueEdit()
            {
                editForm.submit($('edit_form').action + 'back/edit/');
            }

            function saveAndDelivery()
            {
                editForm.submit($('edit_form').action + 'back/delivery/');
            }

            function addRuleRow(obj)
            {
                var rulesContainer = $$('#' + obj.group + ' .form-list tbody')[0];
                Element.insert(rulesContainer, {
                    bottom : obj.rule
                });
            }
        ";
    }

    /**
     * Add a set of buttons
     */
    protected function _addButtons()
    {
        $feedId = $this->getModel()->getId();
        $onclick = "FeedGenerator.generate('%smode/%s', '%s', %d)";
        $generateUrl = $this->_getUrl(self::URL_PATH_GENERATE);
        $stateUrl = $this->_getUrl(self::URL_PATH_STATE);
        $testUrl = $this->_getUrl(self::URL_PATH_TEST, array('id' => $this->getModel()->getId(), 'skip' => 'rules'));

        $this->_addButton('saveandcontinue', array(
            'label'   => __('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class'   => 'save',
        ), -100);

        $this->_addButton('test', array(
            'label'   => $this->__('Generate Test Feed'),
            'onclick' => "FeedGenerator.generateTest(this, '" . $testUrl . "', false);",
            'class'   => 'go',
        ), -100);

        if ($this->getModel()->getGenerator()->getState()->isReady()) {
            $this->_addButton('generate', array(
                'label'   => __('Generate Feed'),
                'onclick' => sprintf($onclick, $generateUrl, 'new', $stateUrl, $feedId),
                'class'   => 'go',
                'id'      => 'btn_feed_generate',
            ), -100);
        } else {
            $this->_addButton('generate_continue', array(
                'label'   => __('Continue Feed Generation'),
                'onclick' => sprintf($onclick, $generateUrl, 'continue', $stateUrl, $feedId),
                'id'      => 'btn_feed_generate_continue',
            ), -100);

            $this->_addButton('generate_new', array(
                'label'   => __('New Feed Generation'),
                'onclick' => sprintf($onclick, $generateUrl, 'new', $stateUrl, $feedId),
                'id'      => 'btn_feed_generate_new',
            ), -100);
        }

        if ($this->getModel()->getFtp()) {
            $this->_addButton('delivery', array(
                'label'   => __('Delivery Feed'),
                'onclick' => 'saveAndDelivery()',
                'class'   => 'go',
                'id'      => 'btn_feed_delivery',
            ), -100);
        }

        if (Mage::app()->getRequest()->getParam('generate')) {
            $this->_formScripts[] = sprintf($onclick, $generateUrl . '', 'new/skip/rules/', $stateUrl, $feedId) . ";";
        }
    }

    /**
     * Get URL and form it with specific requirements
     *
     * @param  string $urlPath
     * @param  array  $options
     * @return string
     */
    protected function _getUrl($urlPath, array $options = array())
    {
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https:' : 'http:';

        switch ($urlPath) {
            case self::URL_PATH_GENERATE:
            case self::URL_PATH_TEST:
                $url = $this->getModel()->getStore()->getUrl($urlPath, $options);
                $parsedUrl = parse_url($url);
                if ($parsedUrl['host'] != $_SERVER['HTTP_HOST']) {
                    $url = Mage::helper('adminhtml')->getUrl($urlPath, $options);
                }
                break;

            case self::URL_PATH_STATE:
                $url = Mage::helper('adminhtml')->getUrl($urlPath, $options);
                break;
        }

        $url = strtok($url, '?');
        $url = preg_replace('/(http:|https:)/', $protocol, $url);

        return $url;
    }

    public function getHeaderText()
    {
        if ($this->getModel()->getId() > 0) {
            return __("Edit Feed '%s'", $this->htmlEscape(Mage::registry('current_model')->getName()));
        } else {
            return __('Add Feed');
        }
    }

    public function getModel()
    {
        return Mage::registry('current_model');
    }
}