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


class Mirasvit_Email_Helper_Variables_Coupon
{
    protected $_coupons = array();

    public function getCoupon($parent, $args)
    {
        if ($parent->getPreview()) {
            // in previem mode, we create fake coupon
            $expirationDate = time() + rand(1, 30) * 24 * 60 * 60;

            $coupon = Mage::getModel('salesrule/coupon');
            $coupon->setCode('EML#####')
                ->setExpirationDate(date(DateTime::ISO8601, $expirationDate))
                ->setType(1);

            return $coupon;

        } elseif ($parent->getChain()) {
            $chain = $parent->getChain();
            
            # if we already generated coupon for this chain
            if (isset($this->_coupons[$chain->getId()])) {
                return $this->_coupons[$chain->getId()];
            }

            if ($chain->getCouponEnabled()) {
                $rule = Mage::getModel('salesrule/rule')->load($chain->getCouponSalesRuleId());
                
                if ($rule->getId()) {

                    $generator = Mage::getSingleton('salesrule/coupon_codegenerator', array('length' => 5));
                    $rule->setCouponCodeGenerator($generator);
                    $code =  'EML'.$rule->getCouponCodeGenerator()->generateCode();

                    $expirationDate = false;
                    if ($chain->getCouponExpiresDays()) {
                        $expirationDate = time() + $chain->getCouponExpiresDays() * 24 * 60 * 60;
                    }

                    $coupon = Mage::getModel('salesrule/coupon');
                    $coupon->setRule($rule)
                        ->setCode($code)
                        ->setIsPrimary(false)
                        ->setUsageLimit(1)
                        ->setUsagePerCustomer(1)
                        ->setExpirationDate(date(DateTime::ISO8601, $expirationDate))
                        ->setType(1)
                        ->save();

                    $this->_coupons[$chain->getId()] = $coupon;

                    return $coupon;
                }
            }
        }

        return false;
    }
}