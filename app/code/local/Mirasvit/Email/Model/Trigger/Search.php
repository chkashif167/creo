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


class Mirasvit_Email_Model_Trigger_Search extends Varien_Object
{
	public function load()
    {
        $arr = array();
        $searchText = $this->getQuery();
        $searchableFields = array('title', 'description');
        $collection = Mage::getModel('email/trigger')->getCollection()
            ->addFieldToFilter(
                $searchableFields,
                array(
                    array('like' => '%' . $searchText . '%'),
                    array('like' => '%' . $searchText . '%')
                    )
                )
            ->load();

        foreach ($collection as $model) {
            $description = strip_tags($model->getDescription());
            $arr[] = array(
                'id'            => 'email/search/'.$model->getId(),
                'type'          => Mage::helper('email')->__('Trigger'),
                'name'          => $model->getTitle(),
                'description'   => Mage::helper('core/string')->substr($description, 0, 30),
                'url' 			=> Mage::helper('adminhtml')->getUrl('adminhtml/email_trigger/edit', array('id' => $model->getId())),
            );
        }

        $this->setResults($arr);

        return $this;
    }
}