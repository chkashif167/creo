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
 * Класс реализует методы для работы со сфинксом на томже сервере что и magento
 * Дополняет базовый класс Mirasvit_SearchSphinx_Model_Engine_Sphinx методами управления индексом и демоном
 * reindex/delta reindex/stop/start.
 *
 * @category Mirasvit
 */
class Mirasvit_SearchSphinx_Model_Engine_Sphinx_Native extends Mirasvit_SearchSphinx_Model_Engine_Sphinx
{
    const SEARCHD = 'searchd';
    const INDEXER = 'indexer';
    const REINDEX_SUCCESS_MESSAGE = 'rotating indices: succesfully sent SIGHUP to searchd';

    protected $_indexerCommand = null;
    protected $_searchdCommand = null;

    /**
     * Устанавливаем переменный свойственные для локально сфинкса.
     */
    public function __construct()
    {
        parent::__construct();

        $binPath = Mage::getStoreConfig('searchsphinx/general/bin_path');
        // если в пути есть searchd, убераем его
        if (substr($binPath, strlen($binPath) - strlen(self::SEARCHD)) == self::SEARCHD) {
            $binPath = substr($binPath, 0, strlen($binPath) - strlen(self::SEARCHD));
        }

        $this->_indexerCommand = $binPath.self::INDEXER;
        $this->_searchdCommand = $binPath.self::SEARCHD;

        $this->_spxHost = Mage::getStoreConfig('searchsphinx/general/host');
        $this->_spxPort = Mage::getStoreConfig('searchsphinx/general/port');

        $this->_spxHost = $this->_spxHost ? $this->_spxHost : 'localhost';
        $this->_spxPort = intval($this->_spxPort ? $this->_spxPort : '9315');

        $this->_basePath = Mage::getBaseDir('var').DS.'sphinx';

        return $this;
    }

    /**
     * Переиндексация - отправка http запроса.
     *
     * @param bool $delta выполнить делта-реиндекс
     *
     * @return string
     */
    public function reindex($delta = false)
    {
        return $this->_request('reindex/delta/'.$delta);
    }

    /**
     * Запуск демона - отправка http запроса.
     */
    public function start()
    {
        $error = $this->_request('start');

        if ($error) {
            Mage::throwException($error);
        }

        return $this;
    }

    /**
     * Остановка демона - отправка http запроса.
     */
    public function stop()
    {
        $error = $this->_request('stop');

        if ($error) {
            Mage::throwException($error);
        }

        return $this;
    }

    /**
     * Рестарт демона - отправка 2х http запросов (стоп, старт).
     */
    public function restart()
    {
        $this->stop();
        $this->start();

        return $this;
    }

    /**
     * Выполняет реиндекс всех активных индексов.
     *
     * @param bool $delta выполнить делта-реиндекс
     *
     * @return string
     */
    public function doReindex($delta = false)
    {
        $this->makeConfigFile();

        if (!$this->isSphinxFounded()) {
            Mage::throwException($this->_indexerCommand.': command not found');
        }

        if (!$this->isIndexerRunning()) {
            $indexes = Mage::helper('searchindex/index')->getIndexes();
            $toReindex = array();
            foreach ($indexes as $index) {
                $indexCode = $index->getCode();
                if ($delta) {
                    $indexCode = 'delta_'.$indexCode;
                }
                $toReindex[] = $indexCode;
            }

            $exec = $this->_exec($this->_indexerCommand.' --config '.$this->_configFilepath.' --rotate '.implode(' ', $toReindex));
            $result = ($exec['status'] == 0) || (strpos($exec['data'], self::REINDEX_SUCCESS_MESSAGE) !== false);

            if (!$result) {
                Mage::throwException('Error on reindex '.$exec['data']);
            }

            if ($delta) {
                $this->mergeDeltaWithMain();
            }
            $this->restart();
        } else {
            Mage::throwException('Reindex already run, please wait... '.$this->isIndexerRunning());
        }

        return 'Index has been successfully rebuilt';
    }

    /**
     * Выполняет запуск демона.
     */
    public function doStart()
    {
        $this->stop();

        if (!$this->isSphinxFounded()) {
            Mage::throwException($this->_searchdCommand.': command not found');
        }

        if (!is_readable($this->_configFilepath)) {
            Mage::throwException('Please run full reindex, before start sphinx daemon');
        }

        $command = $this->_searchdCommand.' --config '.$this->_configFilepath;
        $exec = $this->_exec($command);
        if ($exec['status'] !== 0) {
            Mage::throwException('Error when running searchd '.$exec['data']);
        }

        return $this;
    }

    /**
     * Выполняет остановку демона.
     */
    public function doStop()
    {
        $find = 'ps aux | grep searchd | grep '.$this->_configFilepath.'  | awk \'{print $2}\'';
        $exec = $this->_exec($find);

        foreach (explode(PHP_EOL, $exec['data']) as $id) {
            $command = 'kill -9 '.$id;
            $this->_exec($command);
        }

        return $this;
    }

    /**
     * Проверяет запущен-ли реиндекс.
     *
     * @return bool
     */
    public function isIndexerRunning()
    {
        $status = false;

        $command = 'ps aux | grep '.self::INDEXER.' | grep '.$this->_configFilepath;
        $exec = $this->_exec($command);
        if ($exec['status'] === 0) {
            $pos = strpos($exec['data'], '--rotate');
            if ($pos !== false) {
                $status = $exec['data'];

                return $status;
            }
        }

        return $status;
    }

    /**
     * Проверяет запущен-ли демон.
     *
     * @return bool
     */
    public function isSearchdRunning()
    {
        $command = 'ps aux | grep '.self::SEARCHD.' | grep '.$this->_configFilepath;
        $exec = $this->_exec($command);

        if ($exec['status'] === 0) {
            $pos = strpos($exec['data'], self::SEARCHD.' --config');

            if ($pos !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Проверят найден-ли на сервер сфинкс (searchd).
     *
     * @return bool
     */
    public function isSphinxFounded()
    {
        $exec = $this->_exec($this->_searchdCommand.' --config /fake/fake/sphinx.conf');

        if (strpos($exec['data'], 'sphinx.conf') === false) {
            return false;
        }

        return true;
    }

    /**
     * Запускает объеденение основного индекса с делта-индексом
     * для всех активных индексов.
     */
    public function mergeDeltaWithMain()
    {
        $indexes = Mage::helper('searchindex/index')->getIndexes();
        foreach ($indexes as $index) {
            $exec = $this->_exec($this->_indexerCommand.' --config '.$this->_configFilepath.' --merge '.$index->getCode().' delta_'.$index->getCode().' --merge-dst-range deleted 0 0 --rotate');
        }

        return $this;
    }

    /**
     * Выполнение php комманды exec() с проверкой на существование функции
     * На некоторых серверах функция находиться в игнор-листе. В этом случае ее надо включить через php.ini.
     *
     * @param string $command коммандра
     *
     * @return array
     */
    protected function _exec($command)
    {
        $status = null;
        $data = array();

        if (function_exists('exec')) {
            exec($command, $data, $status);
            Mage::helper('mstcore/logger')->log($this, __FUNCTION__, $command."\n".implode("\n", $data));
        } else {
            Mage::helper('mstcore/logger')->log($this, __FUNCTION__, 'PHP function "exec" not available');

            Mage::throwException('PHP function "exec" not available');
        }

        return array('status' => $status, 'data' => implode(PHP_EOL, $data));
    }

    /**
     * Отправляет http запрос на контролеер расширения
     * Таким образом все действия со сфинксом выполняються от apache пользователя.
     *
     * @param string $command комманда (старт\стоп\реиндекс)
     *
     * @return string
     */
    protected function _request($command)
    {
        $httpClient = new Zend_Http_Client();
        $httpClient->setConfig(array('timeout' => 60000));

        Mage::register('custom_entry_point', true, true);

        $store = Mage::app()->getStore(0);
        $url = $store->getUrl('searchsphinx/action/'.$command, array('_query' => array('rand' => microtime(true))));
        $result = $httpClient->setUri($url)->request()->getBody();

        Mage::helper('mstcore/logger')->log($this, __FUNCTION__, $url."\n".$result);

        return $result;
    }

    /**
     * Define sphinx version.
     *
     * @return string $version
     */
    public function getSphinxVersion()
    {
        $version = '2.0';
        $cmd = $this->_searchdCommand.' --help';
        $exec = $this->_exec($cmd);
        $res = preg_match('/Sphinx[\s]?([\d.]*)([\s\w\d.-]*)?/i', $exec['data'], $match);
        if ($res === 1 && ($match[1] != '' || null != $match[1])) {
            if ($match[1] > 2.1) {
                $version = '2.2';
            }
        }

        return $version;
    }
}
