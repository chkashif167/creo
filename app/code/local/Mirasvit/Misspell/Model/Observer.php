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
class Mirasvit_Misspell_Model_Observer
{
    public function onPostdispatchCatalogsearchResultIndex($observer)
    {
        $queryHelper = Mage::helper('misspell/query');

        $messageRoute404 = Mage::getSingleton('core/session')->getData('route404');

        if (!empty($messageRoute404)) {
            Mage::getSingleton('core/session')->unsetData('route404');
            Mage::getSingleton('core/session')->addNotice($messageRoute404);
        }

        if ($queryHelper->getCountResult($queryHelper->getCurrentPhase()) == 0) {
            $result = $this->doSpellCorrection();

            // if spell correction return false
            if (!$result) {
                $result = $this->doFallbackCorrection();
            }
        }
    }

    public function doSpellCorrection()
    {
        if (!Mage::getStoreConfig('misspell/general/misspell')) {
            return false;
        }
        $queryHelper = Mage::helper('misspell/query');
        $currentPhase = $queryHelper->getCurrentPhase();

        $suggestedPhase = $queryHelper->suggestMisspellPhase($currentPhase);

        if ($suggestedPhase
            && $suggestedPhase != $queryHelper->getCurrentPhase()
            && $suggestedPhase != $queryHelper->getMisspellPhase()) {

            //do redirect
            if ($queryHelper->getCountResult($suggestedPhase)) {
                $url = $queryHelper->getMisspellUrl($queryHelper->getCurrentPhase(), $suggestedPhase);

                Mage::app()->getFrontController()->getResponse()->setRedirect($url);

                return true;
            }
        }

        return false;
    }

    public function doFallbackCorrection()
    {
        if (!Mage::getStoreConfig('misspell/general/fallback')) {
            return false;
        }

        $queryHelper = Mage::helper('misspell/query');
        $currentPhase = $queryHelper->getCurrentPhase();

        $suggestedPhase = $queryHelper->suggestFallbackPhase($currentPhase);

        if ($suggestedPhase
            && $suggestedPhase != $queryHelper->getCurrentPhase()
            && $suggestedPhase != $queryHelper->getFallbackPhase()
            ) {
            $url = $queryHelper->getFallbackUrl($currentPhase, $suggestedPhase);

            Mage::app()->getFrontController()->getResponse()->setRedirect($url);

            return true;
        }

        return false;
    }

    public function onPrepareCollection()
    {
        // Mage::helper('catalogsearch')->setSuggestQuery();
    }
}
