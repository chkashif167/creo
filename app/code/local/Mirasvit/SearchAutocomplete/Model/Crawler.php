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
 * Паук. Посылает на поиск поочереди запросы тем самым наполняя кеш.
 * Временно отключен.
 *
 * @category Mirasvit
 */
class Mirasvit_SearchAutocomplete_Model_Crawler extends Mage_Core_Model_Abstract
{
    const USER_AGENT = 'MagentoCrawler';

    public function getUrls($pageContent)
    {
        $urls = array();
        preg_match_all(
            "/\s+href\s*=\s*[\"\']?([^\s\"\']+)[\"\'\s]+/ims",
            $pageContent,
            $urls
        );
        $urls = $urls[1];

        return $urls;
    }

    public function getStoresInfo()
    {
        $baseUrls = array();

        foreach (Mage::app()->getStores() as $store) {
            $website = Mage::app()->getWebsite($store->getWebsiteId());
            $defaultWebsiteStore = $website->getDefaultStore();
            $defaultWebsiteBaseUrl = $defaultWebsiteStore->getBaseUrl();
            $defaultWebsiteBaseCurrency = $defaultWebsiteStore->getDefaultCurrencyCode();

            $baseUrl = Mage::app()->getStore($store)->getBaseUrl();
            $defaultCurrency = Mage::app()->getStore($store)->getDefaultCurrencyCode();

            $cookie = '';
            if (($baseUrl == $defaultWebsiteBaseUrl) && ($defaultWebsiteStore->getId() != $store->getId())) {
                $cookie = 'store='.$store->getCode().';';
            }

            $baseUrls[] = array(
                'store_id' => $store->getId(),
                'base_url' => $baseUrl,
                'cookie' => $cookie,
            );

            $currencies = $store->getAvailableCurrencyCodes(true);
            foreach ($currencies as $currencyCode) {
                if ($currencyCode != $defaultCurrency) {
                    $baseUrls[] = array(
                        'store_id' => $store->getId(),
                        'base_url' => $baseUrl,
                        'cookie' => $cookie.'currency='.$currencyCode.';',
                    );
                }
            }
        }

        return $baseUrls;
    }

    public function crawl()
    {
        return $this;

        Mage::register('custom_entry_point', true, true);

        $counter = 0;
        $timeStart = time();
        $storesInfo = $this->getStoresInfo();
        $adapter = new Varien_Http_Adapter_Curl();

        foreach ($storesInfo as $info) {
            $options = array(CURLOPT_USERAGENT => self::USER_AGENT);
            $storeId = $info['store_id'];
            $threads = 1;

            if (!empty($info['cookie'])) {
                $options[CURLOPT_COOKIE] = $info['cookie'];
            }

            $urls = array();
            $urlsCount = 0;
            $totalCount = 0;

            $queries = Mage::getModel('catalogsearch/query')->getCollection()
                ->addFieldToFilter('store_id', $storeId)
                ->setOrder('popularity', 'desc');

            foreach ($queries as $query) {
                $queryText = $query->getQueryText();

                $part = '';
                for ($i = 0; $i < strlen($queryText); $i++) {
                    $part .= $queryText[$i];
                    $url = $info['base_url'].'searchautocomplete/ajax/get/?q='.$part.'&cat=0';

                    $urls[] = $url;
                    $urlsCount++;
                    $totalCount++;
                    $counter++;
                    if ($urlsCount == $threads) {
                        $result = $adapter->multiRequest($urls, $options);
                        $urlsCount = 0;
                        $urls = array();
                    }
                }

                if (time() - $timeStart > 1 * 60 * 60) {
                    return $this;
                }
            }

            if (!empty($urls)) {
                $adapter->multiRequest($urls, $options);
            }
        }

        return $this;
    }
}
