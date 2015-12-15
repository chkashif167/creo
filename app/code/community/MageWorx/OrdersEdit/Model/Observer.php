<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_Observer
{

    /** Before edit order set old price
     *
     * @param $observer
     * @return $this
     */
    public function convertOrderItemToQuoteItem($observer)
    {
        $helper = $this->getMwHelper();
        if (!$helper->isEnabled()) {
            return $this;
        }

        /** @var Mage_Sales_Model_Quote_Item $quoteItem */
        $quoteItem = $observer->getEvent()->getQuoteItem();

        // fix for magento 1620-1700:
        $shippingAddress = $quoteItem->getQuote()->getShippingAddress();
        if ($shippingAddress) {
            $shippingAddress->setSameAsBilling(0);
        }

        // KeepPurchasePrice
        /** @var Mage_Sales_Model_Order_Item $orderItem */
        $orderItem = $observer->getEvent()->getOrderItem();
        $storeId = $orderItem->getOrder()->getStoreId();
        $store = Mage::app()->getStore($storeId);


        $oldQuoteItemId = $orderItem->getQuoteItemId();

        $oldPrice = $orderItem->getPrice();
        if (Mage::helper('tax')->priceIncludesTax($store)) {
            $oldPrice = $orderItem->getOriginalPrice();
        }

        /** @var Mage_Core_Model_Resource $coreResource */
        $coreResource = Mage::getSingleton('core/resource');
        $read = $coreResource->getConnection('core_read');

        if ($orderItem->getProductType() != 'bundle' && $oldQuoteItemId > 0) {
            $select = $read->select()
                ->from($coreResource->getTableName('sales_flat_quote_item'), 'original_custom_price')
                ->where('item_id = ?', $oldQuoteItemId);
            $originalCustomPrice = $read->fetchOne($select);
            if ($originalCustomPrice) {
                $oldPrice = $originalCustomPrice;
            }
        }

        if ($orderItem->getProductType() == 'configurable') {
            $productId = $orderItem->getProductId();
            $itemPrice = $quoteItem->getParentItem()->getProduct()->getPriceModel()->getFinalPrice(1, $quoteItem->getParentItem()->getProduct());
            $items = $quoteItem->getQuote()->getItemsCollection();
            foreach ($items as $item) {
                if ($item->getProduct()->getId() == $productId && !$item->getApplyPriceFlag()) {
                    if ($oldPrice != $itemPrice) {
                        $item->setCustomPrice($oldPrice)->setOriginalCustomPrice($oldPrice);
                    }
                    $item->setApplyPriceFlag(true); // mark item
                }
            }
            return $this;
        } elseif ($orderItem->getProductType() == 'bundle') {
            // prepare bundle old price
            if (!$oldQuoteItemId) {
                return $this;
            }
            if ($quoteItem->getParentItem()) $quoteItem = $quoteItem->getParentItem();
            $select = $read->select()
                ->from($coreResource->getTableName('sales_flat_quote_item'), array('product_id', 'price', 'original_custom_price', 'price_incl_tax'))
                ->where('parent_item_id = ?', $oldQuoteItemId);
            $children = $read->fetchAll($select);
            if (!$children) {
                return $this;
            }
            $orderChildren = array();
            foreach ($children as $child) {
                $orderChildren[$child['product_id']] = $child;
            }

            // foreach all children and apply old price
            $children = $quoteItem->getChildren();
            if (!$children) {
                return $this;
            }
            foreach ($children as $child) {
                if (isset($orderChildren[$child->getProductId()])) {
                    $orderChild = $orderChildren[$child->getProductId()];
                    if (Mage::helper('tax')->priceIncludesTax($store)) {
                        $oldPrice = $orderChild['price_incl_tax'];
                    } else {
                        $oldPrice = $orderChild['price'];
                    }
                    $oldPrice = $orderChild['original_custom_price'] ? $orderChild['original_custom_price'] : $oldPrice;
                    if ($oldPrice != $child->getProduct()->getFinalPrice()) {
                        $child->setCustomPrice($oldPrice)->setOriginalCustomPrice($oldPrice);
                    }
                }
            }
            return $this;
        }

        // simple
        if ($oldPrice != $quoteItem->getProduct()->getFinalPrice()) {
            $quoteItem->setCustomPrice($oldPrice)->setOriginalCustomPrice($oldPrice);
        }

    }

    /** Before edit order collectShippingRates
     *
     * @param $observer
     * @return $this
     */
    public function convertOrderToQuote($observer)
    {
        $helper = $this->getMwHelper();
        if (!$helper->isEnabled()) {
            return $this;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        $billing = $order->getBillingAddress();
        $shipping = $order->getShippingAddress();

        // set same_as_billing = yes/no
        if ($shipping) {
            if ($billing->getFirstname() == $shipping->getFirstname()
                && $billing->getMiddlename() == $shipping->getMiddlename()
                && $billing->getSuffix() == $shipping->getSuffix()
                && $billing->getCompany() == $shipping->getCompany()
                && $billing->getStreet() == $shipping->getStreet()
                && $billing->getCity() == $shipping->getCity()
                && $billing->getRegion() == $shipping->getRegion()
                && $billing->getRegionId() == $shipping->getRegionId()
                && $billing->getPostcode() == $shipping->getPostcode()
                && $billing->getCountryId() == $shipping->getCountryId()
                && $billing->getTelephone() == $shipping->getTelephone()
                && $billing->getFax() == $shipping->getFax()
            ) {
                $shipping->setSameAsBilling(1);
                Mage::getSingleton('adminhtml/sales_order_create')->getShippingAddress()->setSameAsBilling(1);
            } else {
                Mage::getSingleton('adminhtml/sales_order_create')->setShippingAsBilling(0);
            }
        }

        $store = Mage::getSingleton('adminhtml/session_quote')->getStore();
        if (Mage::helper('tax')->shippingPriceIncludesTax($store)) {
            $baseShippingAmount = $order->getBaseShippingInclTax();
        } else {
            $baseShippingAmount = $order->getBaseShippingAmount();
        }
        Mage::getSingleton('adminhtml/session_quote')->setBaseShippingCustomPrice($baseShippingAmount);

        // for collectShippingRates
        $quote->setTotalsCollectedFlag(false);
    }


    /**
     * @param $observer
     */
    public function orderCreateProcessData($observer)
    {
        $request = $observer->getEvent()->getRequest();
        if (isset($request['order']['shipping_price'])) {
            $shippingPrice = $request['order']['shipping_price'];
            if ($shippingPrice == 'null') {
                $shippingPrice = null;
            } else {
                $shippingPrice = floatval($shippingPrice);
            }
            Mage::getSingleton('adminhtml/session_quote')->setBaseShippingCustomPrice($shippingPrice);
        }
        // if no cancel reset_shipping - recollectShippingRates
        Mage::getSingleton('adminhtml/sales_order_create')->collectShippingRates();
    }

    /** Edit order set old coupone
     *
     * @param $observer
     * @return $this
     */
    public function quoteCollectTotalsAfter($observer)
    {
        if (!$this->getMwHelper()->isEnabled()) {
            return $this;
        }

        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        // apply custom shipping price
        if ($this->getMwHelper()->isShippingPriceEditEnabled() && Mage::app()->getStore()->isAdmin()) {
            $address = $quote->getShippingAddress();
            $baseShippingCustomPrice = Mage::getSingleton('adminhtml/session_quote')->getBaseShippingCustomPrice();
            if ($address && !is_null($baseShippingCustomPrice)) {
                if ($address->getShippingMethod()) {

                    $origBaseShippingInclTax = $address->getBaseShippingInclTax();
                    $origShippingInclTax = $address->getShippingInclTax();

                    $address->setBaseTotalAmount('shipping', $baseShippingCustomPrice);
                    $shippingCustomPrice = $quote->getStore()->convertPrice($baseShippingCustomPrice);
                    $address->setTotalAmount('shipping', $shippingCustomPrice);

                    $creditModel = null;
                    $address->setAppliedTaxesReset(false);

                    foreach ($address->getTotalCollector()->getCollectors() as $code => $model) {
                        // for calculate shipping tax
                        if ($code == 'tax_shipping' || $code == 'tax') {
                            $model->collect($address);
                        }
                        if ($code == 'customercredit') {
                            $creditModel = $model;
                        }
                    }

                    $address->setGrandTotal((float)$address->getGrandTotal() + ($address->getShippingInclTax() - $origShippingInclTax));
                    $address->setBaseGrandTotal((float)$address->getBaseGrandTotal() + ($address->getBaseShippingInclTax() - $origBaseShippingInclTax));

                    // for recollect customer credit and authorizenet in admin
                    if ($creditModel && $address->getBaseCustomerCreditAmount() > 0) {
                        $baseCreditLeft = $address->getBaseCustomerCreditAmount();
                        $creditLeft = $address->getCustomerCreditAmount();
                        $address->setBaseGrandTotal($address->getBaseGrandTotal() + $baseCreditLeft);
                        $address->setGrandTotal($address->getGrandTotal() + $creditLeft);
                        $creditModel->collect($address);
                    }

                } else {
                    Mage::getSingleton('adminhtml/session_quote')->setBaseShippingCustomPrice(null);
                }
            }
        }

        // apply old coupon_code
        $orderId = Mage::getSingleton('adminhtml/session_quote')->getOrderId();
        if (!$orderId) {
            return $this;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);
        if (!$order->getId()) {
            return $this;
        }
        if (!$order->getAppliedRuleIds()) {
            return $this;
        }

        if (!$quote->getCouponCode() && !Mage::getSingleton('adminhtml/session_quote')->getCouponCodeIsDeleted() && $order->getCouponCode()) {
            $quote->setCouponCode($order->getCouponCode());

            /** @var Mage_Sales_Model_Quote_Address $address */
            foreach ($quote->getAllAddresses() as $address) {
                $amount = $address->getDiscountAmount();
                if ($amount != 0) {
                    $description = $order->getDiscountDescription();
                    // WTF?!
                    if ($description) {
                        $title = Mage::helper('sales')->__('Discount (%s)', $description);
                    } else {
                        $title = Mage::helper('sales')->__('Discount');
                    }
                    $address->setCouponCode($order->getCouponCode())->setDiscountDescription($description);
                }
            }
        }

        return $this;
    }

    /** Add coupon block after order items block
     *  (for order view page)
     *
     * @param Varien_Event_Observer $observer
     */
    public function insertCouponBlock($observer)
    {

        /** @var Varien_Object $transport */
        $transport = $observer->getTransport();
        /** @var Mage_Core_Block_Abstract $block */
        $block = $observer->getBlock();
        if($block->getType() == 'adminhtml/sales_order_view_items' && $block->getNameInLayout() == 'order_items')
        {
            /** @var string $oldHtml */
            $oldHtml = $transport->getHtml();

            /** @var string $couponsBlockHtml */
            $couponsBlockHtml = Mage::getSingleton('core/layout')
                ->createBlock('mageworx_ordersedit/adminhtml_sales_order_coupons', 'coupons')
                ->toHtml();

            /** @var string $newHtml */
            $newHtml = $oldHtml . $couponsBlockHtml; // append coupon block html
            $transport->setHtml($newHtml);
        }

        return;
    }

    /**
     * @return MageWorx_OrdersEdit_Helper_Data
     */
    protected function getMwHelper()
    {
        return Mage::helper('mageworx_ordersedit');
    }
}