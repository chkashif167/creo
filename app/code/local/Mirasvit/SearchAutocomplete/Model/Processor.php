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
 * Мы ловим запрос еще до того как пришел на контроллер и если есть кеш сразу отдаем ответ иначе запрос идет на контроллер.
 * За счет этого скорость выдачи из кеша - мгновенна. По сути FPC кеш.
 *
 * @category Mirasvit
 */
class Mirasvit_SearchAutocomplete_Model_Processor
{
    const CACHE_TAG = 'BLOCK_HTML';
    const CACHE_LIFETIME = 604800;

    protected $_cacheId = null;

    public function __construct()
    {
        $key = false;
        if (!empty($_SERVER['REQUEST_URI'])) {
            $key .= $_SERVER['REQUEST_URI'];
        }

        if ($key) {
            if (isset($_COOKIE['store'])) {
                $key = $key.'_'.$_COOKIE['store'];
            }
            if (isset($_COOKIE['currency'])) {
                $key = $key.'_'.$_COOKIE['currency'];
            }
        }

        $this->_cacheId = 'SEARCHAUTOCOMPLETE_'.md5($key);
    }

    public function extractContent()
    {
        $content = Mage::app()->loadCache($this->_cacheId);

        return $content;
    }

    public function cacheResponse(Varien_Event_Observer $observer)
    {
        $frontController = $observer->getEvent()->getFront();
        $request = $frontController->getRequest();

        if ($request->getControllerModule() == 'Mirasvit_SearchAutocomplete') {
            $response = $frontController->getResponse();

            $content = Mage::app()->saveCache($response->getBody(), $this->_cacheId, array(self::CACHE_TAG), self::CACHE_LIFETIME);
        }
    }
}
