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
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Email_Model_Resource_Trigger extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('email/trigger', 'trigger_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }

        if (is_array($object->getData('store_ids'))) {
            $object->setData('store_ids', implode(',', $object->getData('store_ids')));
        }

        if (is_array($object->getData('cancellation_event'))) {
            $object->setData('cancellation_event', implode(',', $object->getData('cancellation_event')));
        }

        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());

        if ($object->hasData('rule')) {
            // pr($object->getRule());die();
            $this->_saveRule($object);
        }

        return parent::_beforeSave($object);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getIsMassAction()) { // Save chain only if a trigger saved from a trigger edit page
            $this->_saveChain($object);
        }
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getStoreIds()) {
            $object->setStoreIds(explode(',', $object->getStoreIds()));
        } else {
            $object->setStoreIds(array(0));
        }

        return parent::_afterLoad($object);
    }

    protected function _saveChain($object)
    {
        $collectionToDelete = Mage::getModel('email/trigger_chain')->getCollection()
            ->addFieldToFilter('trigger_id', $object->getId())
            ->addFieldToFilter('chain_id', ($object->hasData('chain')) ? array('nin' => array_keys($object->getChain())) : array('like' => '%'));

        foreach ($collectionToDelete as $item) {
            $item->delete();
        }

        if($object->hasData('chain')) {
            foreach ($object->getChain() as $chainId => $chainData) {
                if ($chainId === 0) {
                    // template (fake) row
                    continue;
                }
                $chain = Mage::getModel('email/trigger_chain')->load($chainId);
            
                $chainData['delay'] = serialize(array(
                    'days'          => abs($chainData['days']) * 24 * 60 * 60,
                    'hours'         => $chainData['hours'] * 60 * 60,
                    'minutes'       => $chainData['minutes'] * 60,
                    'type'          => $chainData['schedule_type'],
                    'exclude_days'  => isset($chainData['exclude_days']) ? $chainData['exclude_days'] : array()
                ));

                $chain->addData($chainData)
                    ->setTriggerId($object->getId())
                    ->save();
            }

        }
        return $this;
    }

    protected function _saveRule($object)
    {
        if ($object->getData('rule') && is_array($object->getData('rule'))) {
            $rule       = $object->getData('rule');

            $model = $object->getRunRule();
            $model->setIsActive(1)
                ->setIsSystem(1)
                ->loadPost($rule)
                ->setTitle('Run Rule')
                ->save()
                ;

            $object->setRunRuleId($model->getId());
        }

        return $this;
    }
}