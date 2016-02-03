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
 * @package   Sphinx Search Ultimate
 * @version   2.3.2
 * @build     1290
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



/**
 * @category Mirasvit
 */
class Mirasvit_SearchAutocomplete_Helper_Config extends Mage_Core_Helper_Data
{
    public function isShowPrice()
    {
        return Mage::getStoreConfig('searchautocomplete/general/show_price');
    }

    public function isShowImage()
    {
        return Mage::getStoreConfig('searchautocomplete/general/show_image');
    }

    public function isShowShortDescription()
    {
        return Mage::getStoreConfig('searchautocomplete/general/show_short_description');
    }

    public function getShortDescriptionLen()
    {
        return Mage::getStoreConfig('searchautocomplete/general/short_description_len');
    }

    public function getImageSize()
    {
        $width = 70;
        $height = 50;
        $size = Mage::getStoreConfig('searchautocomplete/general/image_size');
        $size = explode('x', $size);
        if (isset($size[0]) && intval($size[0]) > 0) {
            $width = intval($size[0]);
        }

        if (isset($size[1]) && intval($size[1]) > 0) {
            $height = intval($size[1]);
        }

        return array($width, $height);
    }

    public function getImageWidth()
    {
        $size = $this->getImageSize();

        return $size[0];
    }

    public function getImageHeight()
    {
        $size = $this->getImageSize();

        return $size[1];
    }

    public function isShowRating()
    {
        return Mage::getStoreConfig('searchautocomplete/general/show_rating');
    }

    /**
     * Getting average of ratings/reviews.
     *
     * @param int $productId
     *
     * @return Mage_Review_Model_Review_Summary
     */
    public function getReviewSummary($productId)
    {
        $reviewSummary = Mage::getModel('review/review_summary')
            ->setStoreId(Mage::app()->getStore()->getStoreId())
            ->load($productId);

        return $reviewSummary;
    }
}
