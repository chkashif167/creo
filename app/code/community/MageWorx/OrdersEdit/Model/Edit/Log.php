<?php
/**
 * MageWorx
 * Admin Order Editor extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersEdit
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_OrdersEdit_Model_Edit_Log extends Mage_Core_Model_Abstract
{
    /**
     * Possible fields that can bechanged
     *
     * @var array
     */
    protected $_possibleChanges = array(
        'created_at' => 'Order Date',
        'status' => 'Order Status',
        'customer_id' => 'Customer',
        'customer_firstname' => 'Customer First Name',
        'customer_lastname' => 'Customer Last Name',
        'customer_email' => 'Customer Email',
        'customer_group_id' => 'Customer Group',
        'method' => 'Payment Method',
        'shipping_description' => 'Shipping Method'
    );

    /**
     * Changes that were made to order items
     *
     * @var null
     */
    protected $_itemsChanges = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $addressAttributes = Mage::getModel('customer/address')->getAttributes();
        foreach ($addressAttributes as $attr) {
            if (!$attr->getFrontendLabel()) {
                continue;
            }

            $this->_possibleChanges[$attr->getAttributeCode()] = $attr->getFrontendLabel();
        }

        return parent::__construct();
    }

    /**
     * Get changes that were made to source model
     *
     * @param Varien_Object $source
     * @return array
     */
    public function getChanges(Varien_Object $source)
    {
        $changes = array();
        foreach ($this->_possibleChanges as $code => $label) {

            if ($source->getData($code) != $source->getOrigData($code)) {

                switch ($code) {

                    case 'customer_id' :

                        $fromCustomer =  Mage::getModel('customer/customer')->load($source->getOrigData($code));
                        $from = $fromCustomer->getFirstname() . ' ' . $fromCustomer->getLastname() . ' (ID: ' . $fromCustomer->getId() . ')';

                        $toCustomer = Mage::getModel('customer/customer')->load($source->getData($code));
                        $to = $toCustomer->getFirstname() . ' ' . $toCustomer->getLastname() . ' (ID: ' . $toCustomer->getId() . ')';

                        break;

                    case 'customer_group_id' :

                        $fromGroup = Mage::getModel('customer/group')->load($source->getOrigData($code));
                        $from = $fromGroup->getCode();

                        $toGroup = Mage::getModel('customer/group')->load($source->getData($code));
                        $to = $toGroup->getCode();

                        break;

                    case 'method' :

                        $from   = Mage::helper('payment')->getMethodInstance($source->getOrigData('method'))->getTitle();
                        $to     = Mage::helper('payment')->getMethodInstance($source->getData('method'))->getTitle();

                        break;

                    default :

                        $from = $source->getOrigData($code);
                        $to = $source->getData($code);

                }

                $changes[$code] = array(
                    'from' => $from,
                    'to' => $to
                );
            }
        }

        return $changes;
    }

    /**
     * Get list of source which can be changed in order
     *
     * @param $order
     * @return array
     */
    protected function _getAdditionalSources($order)
    {
        return array(
            'billing_address' => $order->getBillingAddress(),
            'shipping_address' => $order->getShippingAddress(),
            'payment' => $order->getPayment(),
        );
    }

    /**
     * Add order item changes to log
     *
     * @param $itemId
     * @param $change
     * @return $this
     */
    public function addItemChange($itemId, $change)
    {
        $this->_itemsChanges[$itemId] = $change;

        return $this;
    }

    /**
     * Create order comment with the changes that were made
     *
     * @param Mage_Sales_Model_Order $order
     * @return $this
     * @throws Exception
     */
    public function commitOrderChanges(Mage_Sales_Model_Order $order)
    {
        $text = '';
        $labelPrefix = '';
        $helper = $this->getHelper();

        $changes = $this->getChanges($order);
        foreach ($this->_getAdditionalSources($order) as $code => $source) {
            if (!($source instanceof Varien_Object)) {
                continue;
            }

            $addChanges = $this->getChanges($source);

            if ($source instanceof Mage_Sales_Model_Order_Address) {
                foreach ($addChanges as $k => $change) {
                    if ($source->getAddressType() == 'shipping') {
                        $addChanges[$k]['label_prefix'] = $helper->__('Shipping') . ' ';
                    } elseif ($source->getAddressType() == 'billing') {
                        $addChanges[$k]['label_prefix'] = $helper->__('Billing') . ' ';
                    }
                }
            }

            $changes = array_merge($changes, $addChanges);
        }

        foreach ($changes as $code => $diff) {
            $label = $labelPrefix . $this->_possibleChanges[$code];
            $labelPrefix = (isset($diff['label_prefix'])) ? $diff['label_prefix'] : '';

            $text .= $labelPrefix;
            $text .= $helper->__("%s has been changed from \"%s\" to \"%s\"", $label, $diff['from'], $diff['to']);
            $text .= '<br>';
        }

        if (count($this->_itemsChanges)) {
            foreach ($this->_itemsChanges as $item) {

                // Qty changes
                if (empty($item['qty_after'])) {
                    $text .= $helper->__("\"%s\" has been removed from the order", $item['name']) . '<br>';
                } elseif (empty($item['qty_before'])) {
                    $text .= $helper->__("\"%s\" has been added to the order (Qty: %s)", $item['name'], $item['qty_after']) . '<br>';
                } elseif ($item['qty_after'] > $item['qty_before']) {
                    $qtyDiff = $item['qty_after'] - $item['qty_before'];
                    $text .= $helper->__("%s item(s) of \"%s\" have been added to the order", $qtyDiff, $item['name']) . '<br>';
                } elseif ($item['qty_before'] > $item['qty_after']) {
                    $qtyDiff = $item['qty_before'] - $item['qty_after'];
                    $text .= $helper->__("%s item(s) of \"%s\" have been removed from the order", $qtyDiff, $item['name']) . '<br>';
                }

                // Price changes
                if (isset($item['price_after']) && isset($item['price_before']) && $item['price_after'] != $item['price_before'])
                {
                    $text .= $helper->__("Price of \"%s\" have been changed from %s to %s",
                            $item['name'],
                            Mage::helper('core')->formatPrice($item['price_before'], false),
                            Mage::helper('core')->formatPrice($item['price_after'], false))
                        . '<br>';
                }

                // Discount changes
                if (isset($item['discount']))
                {
                    if ($item['discount'] == 1)
                    {
                        $text .= $helper->__("Discount for \"%s\" have been applied", $item['name']) . '<br>';
                    } elseif ($item['discount'] == -1) {
                        $text .= $helper->__("Discount of \"%s\" have been removed", $item['name']) . '<br>';
                    }
                }
            }
        }

        if (empty($text)) {
            return $this;
        }

        // 0 - no one; 1 - only admin; 2 - notify all;
        /** @var int $notify */
        $notify = intval($this->getHelper()->isSendUpdateEmail());
        $order->addStatusHistoryComment($text, $order->getStatus())
            ->setIsVisibleOnFront(1)
            ->setIsCustomerNotified($notify > 1);

        if ($notify) {
            $order->sendOrderUpdateEmail($notify > 1, $text);
        }

        $order->save();

        return $this;
    }

    /**
     * @return MageWorx_OrdersEdit_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('mageworx_ordersedit');
    }
}