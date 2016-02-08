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



if (!@class_exists('SphinxClient')) {
    include Mage::getBaseDir().DS.'lib'.DS.'Sphinx'.DS.'sphinxapi.php';
}

/**
 * Класс реализует методы для:
 *     отправка запросов на поиск
 *     сборка файла конфигурации
 * Базавый класс для работы в режиме Search Sphinx (on another server).
 *
 * @category Mirasvit
 */
class Mirasvit_SearchSphinx_Model_Engine_Sphinx extends Mirasvit_SearchIndex_Model_Engine
{
    const PAGE_SIZE = 1000;

    protected $_config = null;
    protected $_sphinxFilepath = null;
    protected $_configFilepath = null;

    protected $_spxHost = null;
    protected $_spxPort = null;

    protected $_io = null;

    public function __construct()
    {
        $this->_config = Mage::getSingleton('searchsphinx/config');
        $this->_sphinxFilepath = Mage::getBaseDir('var').DS.'sphinx';
        $this->_configFilepath = $this->_sphinxFilepath.DS.'sphinx.conf';

        $this->_spxHost = Mage::getStoreConfig('searchsphinx/general/external_host');
        $this->_spxPort = (int) Mage::getStoreConfig('searchsphinx/general/external_port');
        $this->_basePath = Mage::getStoreConfig('searchsphinx/general/external_path');

        $this->_io = Mage::helper('searchsphinx/io');

        return $this;
    }

    /**
     * Обвертка для функции _query.
     *
     * @param string $queryText поисковый запрос (в оригинальном виде)
     * @param int    $store     ИД текущего магазина
     * @param object $index     индекс по которому нужно провести поиск
     *
     * @return array масив ИД елементов, где ИД - ключ, релевантность значение
     */
    public function query($queryText, $store, $index)
    {
        if ($store) {
            $store = array($store);
        }

        return $this->_query($queryText, $store, $index);
    }

    /**
     * Отправляет подготовленный запрос на сфинкс, и преобразует ответ в нужный вид.
     *
     * @param string $query      поисковый запрос (в оригинальном виде)
     * @param int    $storeId    ИД текущего магазина
     * @param string $indexCode  Код индекса  по которому нужно провести поиск (mage_catalog_product ...)
     * @param string $primaryKey Primary Key индекса (entity_id, category_id, post_id ...)
     * @param array  $attributes Масив атрибутов с весами
     * @param int    $offset     Страница
     *
     * @return array масив ИД елементов, где ИД - ключ, релевантность значение
     */
    protected function _query($query, $storeId, $index, $offset = 1)
    {
        $uid = Mage::helper('mstcore/debug')->start();

        $indexCode = $index->getCode();
        $primaryKey = $index->getPrimaryKey();
        $attributes = $index->getAttributes();

        $client = new SphinxClient();
        $client->setMaxQueryTime(5000); //5 seconds
        $client->setLimits(($offset - 1) * self::PAGE_SIZE, self::PAGE_SIZE, $this->_config->getResultLimit());
        $client->setSortMode(SPH_SORT_RELEVANCE);
        $client->setMatchMode(SPH_MATCH_EXTENDED);
        $client->setServer($this->_spxHost, $this->_spxPort);
        $client->SetFieldWeights($attributes);

        if ($storeId) {
            $client->SetFilter('store_id', $storeId);
        }

        $sphinxQuery = $this->_buildQuery($query, $storeId);

        if (!$sphinxQuery) {
            return array();
        }

        $sphinxQuery = '@('.implode(',', $index->getSearchableAttributes()).')'.$sphinxQuery;

        $sphinxResult = $client->query($sphinxQuery, $indexCode);

        if ($sphinxResult === false) {
            Mage::throwException($client->GetLastError()."\nQuery: ".$query);
        } elseif ($sphinxResult['total'] > 0) {
            $entityIds = array();
            $entityIdsWeights = array();
            foreach ($sphinxResult['matches'] as $data) {
                $additionalWeight = isset($data['attrs']['searchindex_weight'])
                    ? $data['attrs']['searchindex_weight']
                    : 0;
                $entityIds[$data['attrs'][strtolower($primaryKey)]] = $data['weight'];
                $entityIdsWeights[$data['attrs'][strtolower($primaryKey)]] = $additionalWeight;
            }

            if ($sphinxResult['total'] > $offset * self::PAGE_SIZE
                && $offset * self::PAGE_SIZE < $this->_config->getResultLimit()) {
                $newIds = $this->_query($query, $storeId, $index, $offset + 1);
                foreach ($newIds as $key => $value) {
                    $entityIds[$key] = $value;
                }
            }
        } else {
            $entityIds = array();
            $entityIdsWeights = array();
        }

        $entityIds = $this->_normalize($entityIds);

        # add search index weight after normalize
        foreach ($entityIds as $id => $weight) {
            if (isset($entityIdsWeights[$id])) {
                $entityIds[$id] += $entityIdsWeights[$id];
            }
        }

        Mage::helper('mstcore/debug')->end($uid, $entityIds);

        return $entityIds;
    }

    /**
     * Строит запрос к сфинксу
     * Запрос состоит из секций (..) & (..) & ..
     *
     * @param string $query   пользовательский запрос
     * @param int    $storeId
     *
     * @return string
     */
    protected function _buildQuery($query, $storeId)
    {
        // Extended query syntax
        if (substr($query, 0, 1) == '=') {
            return substr($query, 1);
        }

        // Search by field
        if (substr($query, 0, 1) == '@') {
            return $query;
        }

        $arQuery = Mage::helper('searchsphinx/query')->buildQuery($query, $storeId, true);

        if (!is_array($arQuery)) {
            return false;
        }

        $result = array();
        foreach ($arQuery as $key => $array) {
            if ($key == 'not like') {
                $result[] = '-'.$this->_buildWhere($key, $array);
            } else {
                $result[] = $this->_buildWhere($key, $array);
            }
        }
        if (count($result)) {
            $query = '('.implode(' & ', $result).')';
        }

        return $query;
    }

    /**
     * Строит секции запроса.
     *
     * @param string $type  тип секции AND/OR
     * @param array  $array слова для секции
     *
     * @return string
     */
    protected function _buildWhere($type, $array)
    {
        if (!is_array($array)) {
            if (substr($array, 0, 1) == ' ') {
                return '('.$this->escapeSphinxQL($array).')';
            } else {
                return '("*'.$this->escapeSphinxQL($array).'*")';
            }
        }

        foreach ($array as $key => $subarray) {
            if ($key == 'or') {
                $array[$key] = $this->_buildWhere($type, $subarray);
                if (is_array($array[$key])) {
                    $array = '('.implode(' | ', $array[$key]).')';
                }
            } elseif ($key == 'and') {
                $array[$key] = $this->_buildWhere($type, $subarray);
                if (is_array($array[$key])) {
                    $array = '('.implode(' & ', $array[$key]).')';
                }
            } else {
                $array[$key] = $this->_buildWhere($type, $subarray);
            }
        }

        return $array;
    }

    protected function escapeSphinxQL($string)
    {
        $from = array('.', ' ', '\\', '(',')','|','-','!','@','~','"','&', '/', '^', '$', '=', "'");
        $to = array('', ' ', '\\\\', '\(','\)','\|','\-','\!','\@','\~','\"', '\&', '\/', '\^', '\$', '\=', "\'");

        return str_replace($from, $to, $string);
    }

    /**
     * Собирает и сохраняет конфиг файл для работы сфинкса (sphinx.conf)
     * Файл сохраняеться в ../var/sphinx/sphinx.conf
     * Шаблон конфига находиться в расширении etc/config/sphinx.conf.
     *
     * @return string полный путь к файлу
     */
    public function makeConfigFile()
    {
        if (!$this->_io->directoryExists($this->_sphinxFilepath)) {
            $this->_io->mkdir($this->_sphinxFilepath);
        }

        $data = array(
            'time' => date('d.m.Y H:i:s'),
            'host' => $this->_spxHost,
            'port' => $this->_spxPort,
            'logdir' => $this->_basePath,
            'sphinxdir' => $this->_basePath,
        );

        $formater = new Varien_Filter_Template();
        $formater->setVariables($data);
        $config = $formater->filter(file_get_contents($this->getSphinxCfgTpl()));

        $indexes = Mage::helper('searchindex/index')->getIndexes();
        $sections = array();
        foreach ($indexes as $index) {
            $indexer = $index->getIndexer();
            $sections[$index->getCode()] = $this->_getSectionConfig($index->getCode(), $indexer);
        }
        $config .= implode(PHP_EOL, $sections);
        // $config  .= PHP_EOL.$this->_getSectionConfig($index->getCode(), $indexer);

        if ($this->_io->isWriteable($this->_configFilepath)) {
            $this->_io->write($this->_configFilepath, $config);
        } else {
            if ($this->_io->fileExists($this->_configFilepath)) {
                Mage::throwException(sprintf('File %s does not writeable', $this->_configFilepath));
            } else {
                Mage::throwException(sprintf('Directory %s does not writeable', $this->_sphinxFilepath));
            }
        }

        return $this->_configFilepath;
    }

    /**
     * Собирает секцию для конфиг файла
     * Каждый индекс имеет свою секцию
     * Секция состоит source (откуда-что брать) и index (куда это писать и как его индексировать)
     * Шаблон секции находиться в расширении etc/config/section.conf.
     *
     * @param string $name    название (код индекса)
     * @param object $indexer Индексатор! индекса
     *
     * @return string готовая секция
     */
    protected function _getSectionConfig($name, $indexer)
    {
        $sqlHost = Mage::getConfig()->getNode('global/resources/default_setup/connection/host');
        $sqlPort = 3306;

        if (count(explode(':', $sqlHost)) == 2) {
            $arr = explode(':', $sqlHost);
            $sqlHost = $arr[0];
            $sqlPort = $arr[1];
        }

        $data = array(
            'name' => $name,
            'sql_host' => $sqlHost,
            'sql_port' => $sqlPort,
            'sql_user' => Mage::getConfig()->getNode('global/resources/default_setup/connection/username'),
            'sql_pass' => Mage::getConfig()->getNode('global/resources/default_setup/connection/password'),
            'sql_db' => Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname'),
            'sql_query_pre' => $this->_getSqlQueryPre($indexer),
            'sql_query' => $this->_getSqlQuery($indexer),
            'sql_query_delta' => $this->_getSqlQueryDelta($indexer),
            'sql_attr_uint' => $indexer->getPrimaryKey(),
            'min_word_len' => Mage::getStoreConfig(Mage_CatalogSearch_Model_Query::XML_PATH_MIN_QUERY_LENGTH),
            'index_path' => $this->_basePath.DS.$name,
            'delta_index_path' => $this->_basePath.DS.$name.'_delta',
        );

        foreach ($data as $key => $value) {
            $data[$key] = str_replace('#', '\#', $value);
        }

        $formater = new Varien_Filter_Template();
        $formater->setVariables($data);
        $config = $formater->filter(file_get_contents($this->getSphinxSectionCfgTpl()));

        return $config;
    }

    /**
     * Возвращает начальный sql запрос (установить статус в updated = 0).
     *
     * @param object $indexer
     *
     * @return string
     */
    protected function _getSqlQueryPre($indexer)
    {
        $table = $indexer->getTableName();

        $sql = 'UPDATE '.$table.' SET updated=0';

        return $sql;
    }

    /**
     * Возвращает sql запрос, выполняя который сфинкс получает все! индексируемые данные.
     *
     * @param object $indexer
     *
     * @return string
     */
    protected function _getSqlQuery($indexer)
    {
        $table = $indexer->getTableName();

        $sql = 'SELECT CONCAT('.$indexer->getPrimaryKey().', store_id) AS id, '.$table.'.* FROM '.$table;

        return $sql;
    }

    /**
     * Возвращает sql запрос, на выборку всех елементов для делта-реиндекса (обновленных элементов).
     *
     * @param object $indexer
     *
     * @return string
     */
    protected function _getSqlQueryDelta($indexer)
    {
        $sql = $this->_getSqlQuery($indexer);
        $sql .= ' WHERE updated = 1';

        return $sql;
    }

    /**
     * Define path for sphinx config template.
     *
     * @return string
     */
    protected function getSphinxCfgTpl()
    {
        return Mage::getModuleDir('etc', 'Mirasvit_SearchSphinx').DS.'conf'.DS.$this->getSphinxVersion().DS.'sphinx.conf';
    }

    /**
     * Define path for sphinx section template.
     *
     * @return string
     */
    protected function getSphinxSectionCfgTpl()
    {
        return Mage::getModuleDir('etc', 'Mirasvit_SearchSphinx').DS.'conf'.DS.$this->getSphinxVersion().DS.'section.conf';
    }

    /**
     * Default sphinx version.
     *
     * @return string
     */
    protected function getSphinxVersion()
    {
        return '2.0';
    }
}
