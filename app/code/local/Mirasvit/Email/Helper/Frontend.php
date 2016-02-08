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


class Mirasvit_Email_Helper_Frontend
{
    public function loginCustomerByQueueCode($code)
    {
        $queue = Mage::getModel('email/queue')->loadByUniqKeyMd5($code);
        if ($queue) {
            $args = $queue->getArgs();

            $customer = Mage::getModel('customer/customer')
                ->load($args['customer_id']);
            if ($customer->getId()) {
                $session = Mage::getSingleton('customer/session');
                if ($session->isLoggedIn() && $customer->getId() != $session->getCustomerId()) {
                    $session->logout();
                    $session->setCustomerAsLoggedIn($customer);
                } elseif (!$session->isLoggedIn()) {
                    $session->setCustomerAsLoggedIn($customer);
                }
            }
        }

        return false;
    }

    public function restoreCartByQueueCode($code)
    {
        $queue = Mage::getModel('email/queue')->loadByUniqKeyMd5($code);
        if ($queue) {
            $args = $queue->getArgs();

            if (isset($args['order_id'])) {
                $order = Mage::getModel('sales/order')->load($args['order_id']);

                $cart = Mage::getSingleton('checkout/cart');
                $cart->truncate();

                $items = $order->getItemsCollection();
                foreach ($items as $item) {
                    try {
                        $cart->addOrderItem($item);
                    } catch (Mage_Core_Exception $e) {
                        Mage::getSingleton('checkout/session')->addError($e->getMessage());
                    } catch (Exception $e) {
                        Mage::getSingleton('checkout/session')->addException($e, 'Cannot add the item to shopping cart.');
                    }
                }

                $cart->saveQuote();
                $cart->save();

                Mage::getSingleton('checkout/session')->replaceQuote($cart->getQuote());

                return true;
            } elseif (isset($args['quote_id'])) {
                $quote = Mage::getModel('sales/quote')->setSharedStoreIds(array_keys(Mage::app()->getStores()))
                    ->load($args['quote_id']);

                $quote->setIsActive(true)->save();

                Mage::getSingleton('checkout/session')->replaceQuote($quote);

                return true;
            }
        }

        return false;
    }
}