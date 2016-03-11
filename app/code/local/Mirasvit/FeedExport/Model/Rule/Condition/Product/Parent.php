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



class Mirasvit_FeedExport_Model_Rule_Condition_Product_Parent extends Mirasvit_FeedExport_Model_Rule_Condition_Product
{
    /**
     * Rewrite parent method validate
     * to load and verifying parent product.
     *
     * @param Mage_Catalog_Model_Product $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $parentObject = $this->getProductPattern()->getParentProduct($object, true);

        if ($parentObject->getId() == $object->getId()) {
            $parentObject->setData(array());
        }

        return parent::validate($parentObject);
    }
}
