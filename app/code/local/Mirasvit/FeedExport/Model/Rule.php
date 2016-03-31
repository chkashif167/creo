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


class Mirasvit_FeedExport_Model_Rule extends Mage_Rule_Model_Rule
{
    const TYPE_ATTRIBUTE   = 'attribute';
    const TYPE_PERFORMANCE = 'performance';

    protected $_productIds;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('feedexport/rule');
        $this->setIdFieldName('rule_id');
    }

    public function getConditionsInstance()
    {
        return Mage::getModel('feedexport/rule_condition_combine');
    }

    public function getActionsInstance()
    {
        return Mage::getModel('feedexport/rule_action_collection');
    }

    protected function _afterSave()
    {
        parent::_afterSave();
    }


    public function getProductIds()
    {
        return $this->_getResource()->getRuleProductIds($this->getId());
    }

    public function toString($format = '')
    {
        $this->load($this->getId());
        $string = $this->getConditions()->asStringRecursive();

        $string = nl2br(preg_replace('/ /', '&nbsp;', $string));

        return $string;
    }

    /**
     * @todo need create typical interface
     */
    public function export()
    {
        $xml = $this->toXml(array('name', 'type', 'conditions_serialized', 'actions_serialized'));
        $path = Mage::getSingleton('feedexport/config')->getRulePath().DS.$this->getName().'.xml';

        file_put_contents($path, $xml);

        return $path;
    }

    /**
     * @todo need create typical interface
     */
    public function import($filePath)
    {
        $content = file_get_contents($filePath);

        $xml      = new Varien_Simplexml_Element($content);
        $template = $xml->asArray();

        $model = $this->getCollection()
            ->addFieldToFilter('name', $template['name'])
            ->getFirstItem();

        $model->addData($template)
            ->save();

        return $model;
    }

    public function duplicate()
    {
        $ruleCopy = Mage::getModel('feedexport/rule')
            ->addData($this->getData())
            ->setRuleId(null)
            ->setName($this->getName().' (copy)')
            ->setIsActive(1)
            ->setCreatedAt(null)
            ->setUpdatedAt(null)
            ->setFeedIds(null)
            ->save();

        return $this;
    }
}
