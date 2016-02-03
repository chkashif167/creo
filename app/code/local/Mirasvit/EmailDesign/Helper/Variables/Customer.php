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


class Mirasvit_EmailDesign_Helper_Variables_Customer
{
    public function getCustomerName($parent, $args)
    {
        $name = $parent->getData('customer_name');
        $name = ucwords($name); 

        return $name;
    }

    public function getCustomer($parent, $args)
    {
        $id = intval($parent->getData('customer_id'));
        $customer = Mage::getModel('customer/customer');
        if ($id) {
            $customer = $customer->load($id);
        }

        return $customer;
    }
}
?>