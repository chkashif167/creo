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


class Mirasvit_MstCore_Helper_Debug extends Mage_Core_Helper_Abstract
{
    protected $_filename = null;
    protected $_enabled  = null;
    protected $_level    = 0;
    protected $_id       = 0;

    /**
     * Start method debuging
     *
     * @return array $uid - unique identificatior
     */
    public function start()
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $this->_level++;
        $this->_id++;
        $backtrace = debug_backtrace();
        $caller = array();
        $this->_addSourceInfo($caller);
        $caller['backtrace']       = $this->_backtrace();
        $caller['args']            = $this->_prepareArgs($backtrace[1]['args']);
        $caller['action']          = 'start';
        $caller['level']           = $this->_level;
        $caller['id']              = $this->_id;

        $this->_write($caller);

        return array('level' => $this->_level, 'id' => $this->_id);
    }

    /**
     * Save data in method
     *
     * @param  array $uid - unique identificator (returned by start method)
     * @param  array $data
     * @return
     */
    public function dump($uid = false, $data = array()) //$uid = false for compatiblity
    {
        if (!$this->isEnabled()) {
            return false;
        }
        if (is_string($uid)) { # for compatiblity
            $data = array($uid => $data);
        }

        $caller = array();
        $this->_addSourceInfo($caller);
        $caller['dump']            = $this->_prepareArgs($data);
        $caller['action']          = 'dump';
        $caller['level']           = $this->_getLevel($uid);
        $caller['id']              = $this->_getId($uid);
        $this->_write($caller);
    }

    /**
     * End of debuging
     *
     * @param  array $data - array for save in debuger log
     * @param  array $data2 - deprecated. for compatiblity
     * @return
     */
    public function end($uid = false, $data = array()) //$uid = false for compatiblity
    {
        if (!$this->isEnabled()) {
            return false;
        }
        if (is_string($uid)) {//for compatiblity
            $data = array($uid => $data);
        }
        $caller = array();
        $this->_addSourceInfo($caller);

        $caller['dump']            = $this->_prepareArgs($data);
        $caller['action']          = 'end';
        $caller['level']           = $this->_getLevel($uid);
        $caller['id']              = $this->_getId($uid);
        $this->_level = $caller['level'] - 1;

        $this->_write($caller);
    }


    public function isEnabled()
    {
        if ($this->_enabled === null) {
            if (Mage::getStoreConfig('mstcore/logger/enabled')) {
                if (Mage::getStoreConfig('mstcore/logger/developer_ip') == '*'
                    || Mage::helper('core/http')->getRemoteAddr() == Mage::getStoreConfig('mstcore/logger/developer_ip')) {
                    $this->_enabled = true;
                } elseif (Mage::helper('core/http')->getRemoteAddr() == '' && Mage::getStoreConfig('mstcore/logger/cron')) {
                    $this->_enabled = true;
                }
            }
        }

        return $this->_enabled;
    }

    protected function _addSourceInfo(&$caller) {
        $backtrace = debug_backtrace();
        $i = 1;
        $caller['class']           = @$backtrace[$i + 1]['class'];
        $caller['type']            = @$backtrace[$i+1]['type'];

        $caller['function']        = $backtrace[$i+1]['function'];
        $caller['file']            = $this->_preparePath($backtrace[$i+1]['file']);
        $caller['line']            = $backtrace[$i+1]['line'];

        $caller['source_function'] = $backtrace[$i+1]['function'];
        $caller['source_file']     = $this->_preparePath($backtrace[$i]['file']);
        $caller['source_line']     = $backtrace[$i]['line'];

        $caller['caller_source']   = $this->_getSource($backtrace[$i+1]['file'], $caller['line']);
        $caller['function_source'] = $this->_getSource($backtrace[$i]['file'], $caller['source_line'], 10, 30);
        $caller['backtrace']       = $this->_backtrace();
        // $caller['included_files']  = get_included_files();
    }


    protected function _getLevel($uid) {
        $level = 0;
        if (is_array($uid) && isset($uid['level'])) {
            $level = $uid['level'];
        }
        return $level;
    }

    protected function _getId($uid) {
        $id = 0;
        if (is_array($uid) && isset($uid['id'])) {
            $id = $uid['id'];
        }
        return $id;
    }

    protected function _backtrace()
    {
        $backtrace = debug_backtrace();

        unset($backtrace[0]);
        unset($backtrace[1]);

        foreach ($backtrace as $key => $trace) {
            $backtrace[$key] = array(
                'class'    => @$trace['class'],
                'function' => @$trace['function'],
                'line'     => @$trace['line'],
                'file'     => $this->_preparePath(@$trace['file']),
           );
        }

        return $backtrace;
    }

    protected function _getFile()
    {
        $path = Mage::getBaseDir('var').DS.'log/mst';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if ($this->_filename == null) {
            if (isset($_SERVER["REQUEST_URI"])) {
                $this->_filename = time().'-'. str_replace('/', '-', $_SERVER["REQUEST_URI"]);
            } else {
                $this->_filename = time();
            }

            // remove old files
            if ($handle = opendir($path)) {
                while (false !== ($file = readdir($handle))) {
                    if (substr($file, 0, strlen('debug_')) == 'debug_'
                        && time() - intval(substr($file, 6, 16)) > 60 * 60) {
                        unlink($path.DS.$file);
                    }
                }
            }
        }

        return $path.DS.'debug_'.$this->_filename.'.log';
    }

   protected function _checkAllowFilesNumber()
    {
        $maxFilesNumber = 10;
        $maxFilesSize = 10; // specified in megabytes
        $path = Mage::getBaseDir('var').DS.'log/mst';
        if ($handle = opendir($path)) {
            $fileCount = 0;
            $filesSize = 0;
            while (false !== ($file = readdir($handle))) {
                if (($file != '.')
                    && ($file != '..')) {
                    $fileCount++;
                    $filesSize += sprintf("%u", filesize($path . DS . $file));
                }
            }
            $filesSize = $filesSize/1048576;
        }
        if (($fileCount > $maxFilesNumber)
            || ($filesSize > $maxFilesSize)) {
                if ($handle = opendir($path)) {
                    while(false !== ($file = readdir($handle))) {
                        if ( is_file ($path."/".$file)) {
                            unlink ($path."/".$file);
                        }
                    }
                }
        }
    }

    protected function _write($data)
    {
        $this-> _checkAllowFilesNumber();
        $formatter = new Zend_Log_Formatter_Simple('%message%'.PHP_EOL);

        $writer = new Zend_Log_Writer_Stream($this->_getFile());
        $writer->setFormatter($formatter);

        $log = new Zend_Log($writer);
        $log->log(json_encode($data), 0);
    }

    protected function _prepareArgs($args)
    {
        $result = array();
        if (!is_array($args)) {
            if (is_object($args)) {
                $args = '[object] '.get_class($args);
            }

            return $args;
        }

        foreach ($args as $key => $value) {
            if (is_object($value)) {
                $value = '[object] '.get_class($value);
            } elseif (is_array($value)) {
                $value = $this->_prepareArgs($value);
            }

            $result[$key] = $value;
        }

        return $result;
    }

    protected function _preparePath($path)
    {
        return str_replace(Mage::getBaseDir(), '<ROOT>', $path);
    }

    protected function _getSource($file, $lineNumber, $paddingTop = 10, $paddingBottom = 10)
    {
        if (!$file || !is_readable($file)) {
            return false;
        }

        $file = fopen($file, 'r');
        $line = 0;

        $range = array(
            'start' => $lineNumber - $paddingTop,
            'end'   => $lineNumber + $paddingBottom
       );

        $format = '% '.strlen($range['end']).'d';

        $source = '';
        while (($row = fgets($file)) !== false) {
            if (++$line > $range['end']) {
                break;
            }

            if ($line >= $range['start']) {
                $row = htmlspecialchars($row, ENT_NOQUOTES);

                $row = '<span>'.sprintf($format, $line).'</span> '.$row;

                if ($line === $lineNumber) {
                    $row = '<div class="highlight">'.$row.'</div>';
                } else {
                    $row = '<div>'.$row.'</div>';
                }

                $source .= $row;
            }
        }

        fclose($file);

        return $source;
    }
}