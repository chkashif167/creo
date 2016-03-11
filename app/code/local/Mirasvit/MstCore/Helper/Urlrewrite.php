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


class Mirasvit_MstCore_Helper_Urlrewrite extends Mage_Core_Helper_Data
{
    protected $config = array();
    protected $config2 = array();

    public function isEnabled($module)
    {
        if (!isset($this->config[$module])) {
            return false;
        }
        if (isset($this->config[$module]['_ENABLED'])) {
            return $this->config[$module]['_ENABLED'];
        }
        return true;
    }

    public function rewriteMode($module, $isEnabled)
    {
        $this->config[$module]['_ENABLED'] = $isEnabled;
    }

    public function registerBasePath($module, $path)
    {
        $this->config[$module]['_BASEPATH'] = $path;
        $this->config2[$path] = $module;
    }

    public function registerPath($module, $type, $pathTemplate, $action, $params = array())
    {
        $this->config[$module][$type] = $pathTemplate;
        $this->config2[$module.'_'.$type]['ACTION'] = $action;
        $this->config2[$module.'_'.$type]['PARAMS'] = $params;
    }

    public function getUniquePath($module, $type, $path, $objectId, $i = 0)
    {
        if ($i) {
            $pathToCheck = $path.'-'.$i;
        } else {
            $pathToCheck = $path;
        }
        // check path for dublicates
        $collection = Mage::getModel('mstcore/urlrewrite')->getCollection()
                    ->addFieldToFilter('module', $module)
                    ->addFieldToFilter('type', $type)
                    ->addFieldToFilter('url_key', $pathToCheck)
                    ->addFieldToFilter('entity_id', array('neq'=>$objectId))
                    ->setOrder('url_key', 'asc')
                    ;

        if ($collection->count()) {
            return $this->getUniquePath($module, $type, $path, $objectId, ++$i);
        }
        return $pathToCheck;
    }

    public function updateUrlrewrite($module, $type, $object, $values)
    {
        if (!isset($this->config[$module])) {
            return false;
        }
        $objectId = $object->getId();
        $pathTemplate = $this->config[$module][$type];
        $path = $pathTemplate;
        foreach ($values as $key => $value) {
            $path = str_replace("[$key]", $value, $path);
        }
        $path = trim($path, '/');
        $path = $this->getUniquePath($module, $type, $path, $objectId);

        $collection = Mage::getModel('mstcore/urlrewrite')->getCollection()
                        ->addFieldToFilter('module', $module)
                        ->addFieldToFilter('type', $type)
                        ->addFieldToFilter('entity_id', $objectId)
                        ;
        if ($collection->count()) {
            $rewrite = $collection->getFirstItem();
            $rewrite->setUrlKey($path)
                    ->save();
        } else {
            $rewrite = Mage::getModel('mstcore/urlrewrite');
            $rewrite
                ->setModule($module)
                ->setType($type)
                ->setEntityId($objectId)
                ->setUrlKey($path)
                ->save();
        }
    }

    public function parseKeyNum($key)
    {
        preg_match('/(\d+)$/', $key, $matches);
        $result = 0;
        if (count($matches)) {
            $result = (int)$matches[1];
        }
        return $result;
    }

    public function removeUrlRewrite($module, $type, $object)
    {
        $collection = Mage::getModel('mstcore/urlrewrite')->getCollection()
                        ->addFieldToFilter('module', $module)
                        ->addFieldToFilter('type', $type)
                        ->addFieldToFilter('entity_id', $object->getId())
                        ;
        if ($collection->count()) {
            $rewrite = $collection->getFirstItem();
            $rewrite->delete();
        }
    }

    public function getUrl($module, $type, $object = false)
    {
        if ($this->isEnabled($module)) {
            $basePath = $this->config[$module]['_BASEPATH'];
            if ($object) {
                $collection = Mage::getModel('mstcore/urlrewrite')->getCollection()
                        ->addFieldToFilter('module', $module)
                        ->addFieldToFilter('type', $type)
                        ->addFieldToFilter('entity_id', $object->getId())
                        ;
                if ($collection->count()) {
                    $rewrite = $collection->getFirstItem();
                    return $this->getUrlByKey($basePath, $rewrite->getUrlKey());
                } else {
                    return $this->getDefaultUrl($module, $type, $object);
                }
            } else {
                return $this->getUrlByKey($basePath, $this->config[$module][$type]);
            }
        } else {
            return $this->getDefaultUrl($module, $type, $object);
        }
    }

    protected function getDefaultUrl($module, $type, $object) {
            $action = $this->config2[$module.'_'.$type]['ACTION'];
            $params = $this->config2[$module.'_'.$type]['PARAMS'];

            $action = str_replace('_', '/', $action);
            if ($object) {
                $params['id'] = $object->getId();
            }

            return Mage::getUrl($action, $params);
    }

    public function getUrlByKey($basePath, $urlKey, $params = false) {
        if ($urlKey) {
            $url = $basePath.'/'. $urlKey;
        } else {
            $url = $basePath;
        }
        $configUrlSuffix = Mage::getStoreConfig('catalog/seo/product_url_suffix');
        //user can enter .html or html suffix
        if ($configUrlSuffix != '' && $configUrlSuffix[0] != '.') {
            $configUrlSuffix = '.'.$configUrlSuffix;
        }
        if (substr($url, -strlen($configUrlSuffix)) == $configUrlSuffix) {
            $url = substr($url, 0, -strlen($configUrlSuffix));
        }
        $url .= $configUrlSuffix;
        if ($params) {
            $url .= '?'.http_build_query($params);
        }
        $url = Mage::getModel('core/url')->getDirectUrl($url);
        return $url;
    }

    public function getUrlKeyWithoutSuffix($key) {
        $configUrlSuffix = Mage::getStoreConfig('catalog/seo/product_url_suffix');
        //user can enter .html or html suffix
        if ($configUrlSuffix != '' && $configUrlSuffix[0] != '.') {
            $configUrlSuffix = '.'.$configUrlSuffix;
        }
        $key = str_replace($configUrlSuffix, '', $key);;
        return $key;
    }

    public function match($pathInfo) {

        $identifier = trim($pathInfo, '/');
        $parts      = explode('/', $identifier);
        if (count($parts) == 1) {
            $parts[0] = $this->getUrlKeyWithoutSuffix($parts[0]);
        }

        if (isset($parts[0]) && !isset($this->config2[$parts[0]])) {
            return false;
        }
        $module = $this->config2[$parts[0]];

        if (!$this->isEnabled($module)) {
            return false;
        }
        if (count($parts) > 1) {
            unset($parts[0]);
            $urlKey = implode('/', $parts);
            $urlKey = urldecode($urlKey);
            $urlKey = $this->getUrlKeyWithoutSuffix($urlKey);
        } else {
            $urlKey = '';
        }

        # check on static urls (urls for static pages, ex. lists)
        $type = $rewrite = false;
        foreach ($this->config[$module] as $t => $key) {
            if ($key === $urlKey) {
                if ($t == '_BASEPATH') {
                    continue;
                }
                $type = $t;
                break;
            }
        }

        # check on dynamic urls (ex. urls of products, categories etc)
        if (!$type) {
            $collection = Mage::getModel('mstcore/urlrewrite')->getCollection()
                    ->addFieldToFilter('url_key', $urlKey)
                    ->addFieldToFilter('module', $module)
                    ;
            if ($collection->count()) {
                $rewrite = $collection->getFirstItem();
                $type = $rewrite->getType();
            } else {
                return false;
            }
        }
        if ($type) {
            $action = $this->config2[$module.'_'.$type]['ACTION'];
            $params = $this->config2[$module.'_'.$type]['PARAMS'];
            $result = new Varien_Object();
            $actionParts = explode('_', $action);
            $result->setRouteName($actionParts[0])
                ->setModuleName($actionParts[0])
                ->setControllerName($actionParts[1])
                ->setActionName($actionParts[2])
                ->setActionParams($params)
                ;

             if ($rewrite) {
                $result->setEntityId($rewrite->getEntityId());
             }
             return $result;
         }
         return false;
    }

   /**
     * normalize Characters
     * Example: ü -> ue
     *
     * @param string $string
     * @return string
     */
    public function normalize($string)
    {
        $table = array(
            'Š'=>'S',  'š'=>'s',  'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z',  'ž'=>'z',  'Č'=>'C',  'č'=>'c',  'Ć'=>'C',  'ć'=>'c',
            'À'=>'A',  'Á'=>'A',  'Â'=>'A',  'Ã'=>'A',  'Ä'=>'Ae', 'Å'=>'A',  'Æ'=>'A',  'Ç'=>'C',  'È'=>'E',  'É'=>'E',
            'Ê'=>'E',  'Ë'=>'E',  'Ì'=>'I',  'Í'=>'I',  'Î'=>'I',  'Ï'=>'I',  'Ñ'=>'N',  'Ò'=>'O',  'Ó'=>'O',  'Ô'=>'O',
            'Õ'=>'O',  'Ö'=>'Oe', 'Ø'=>'O',  'Ù'=>'U',  'Ú'=>'U',  'Û'=>'U',  'Ü'=>'Ue', 'Ý'=>'Y',  'Þ'=>'B',  'ß'=>'Ss',
            'à'=>'a',  'á'=>'a',  'â'=>'a',  'ã'=>'a',  'ä'=>'ae', 'å'=>'a',  'æ'=>'a',  'ç'=>'c',  'è'=>'e',  'é'=>'e',
            'ê'=>'e',  'ë'=>'e',  'ì'=>'i',  'í'=>'i',  'î'=>'i',  'ï'=>'i',  'ð'=>'o',  'ñ'=>'n',  'ò'=>'o',  'ó'=>'o',
            'ô'=>'o',  'õ'=>'o',  'ö'=>'oe', 'ø'=>'o',  'ù'=>'u',  'ú'=>'u',  'û'=>'u',  'ý'=>'y',  'ý'=>'y',  'þ'=>'b',
            'ÿ'=>'y',  'Ŕ'=>'R',  'ŕ'=>'r',  'ü'=>'ue', '/'=>'',   '&'=>'',  '('=>'',   ')'=>''
        );

        $string = strtr($string, $table);
        $string = Mage::getSingleton('catalog/product_url')->formatUrlKey($string);
        return $string;
    }
}