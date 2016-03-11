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



class Mirasvit_FeedExport_Model_Feed_Generator_State extends Varien_Object
{
    /**
     * @todo move to config
     */
    const STATUS_READY = 'ready';
    const STATUS_INIT = 'init';
    const STATUS_PROCESSING = 'processing';
    const STATUS_ERROR = 'error';

    const CHAIN_STATUS_READY = 'ready';
    const CHAIN_STATUS_FINISH = 'finish';
    const CHAIN_STATUS_PROCESSING = 'processing';

    protected $_key = null;
    protected $_io = null;
    protected $_start = null;

    public function __construct()
    {
        $this->_io = Mage::helper('feedexport/io');
        $this->resetTimeout();

        return parent::__construct();
    }

    public function setKey($key)
    {
        $this->_key = $key;
        $this->load();

        return $this;
    }

    public function getKey()
    {
        if ($this->_key) {
            return $this->_key;
        }

        Mage::throwException('State key is empty');
    }

    public function getStatePath()
    {
        $path = Mage::getSingleton('feedexport/config')->getStatePath();
        $this->_io->mkdir($path);

        return $path;
    }

    public function getStateFile()
    {
        return $this->getStatePath().DS.$this->_key.'.xml';
    }

    public function getAbortFile()
    {
        return $this->getStatePath().DS.$this->_key.'.abort';
    }

    public function save()
    {
        $json = $this->toJson();
        // Mage::log($json, null, 'feed.log');
        $this->_io->write($this->getStateFile(), $json);

        return $this;
    }

    public function load($iteration = 0)
    {
        if ($this->_io->fileExists($this->getStateFile())) {
            $json = $this->_io->read($this->getStateFile());
            $data = json_decode($json, true);

            if (stripos(PHP_OS, 'win') === false && !$json && $iteration < 15) {
                return $this->load($iteration + 1);
            }

            $this->_data = $data;
        }

        return $this;
    }

    public function remove()
    {
        $this->_io->unlink($this->getStateFile());
        $this->unsetData();

        return $this;
    }

    public function isReady()
    {
        return $this->getStatus() == self::STATUS_READY;
    }

    public function isProcessing()
    {
        return $this->getStatus() == self::STATUS_PROCESSING;
    }

    public function isError()
    {
        return $this->getStatus() == self::STATUS_ERROR;
    }

    /**
     * I.e. after reset , we add abort file and remove abort file.
     *
     * @return [type] [description]
     */
    public function reset()
    {
        if ($this->_io->fileExists($this->getStateFile())) {
            $this->abort();
            $this->remove();
        }

        return $this;
    }

    public function abort()
    {
        if ($this->load()->getStatus() != self::STATUS_READY) {
            $this->_io->write($this->getAbortFile(), '+');
            $time = time();
            while ($this->_io->fileExists($this->getAbortFile()) && time() - $time < 5) {
                sleep(1);
            }
            $this->_io->unlink($this->getAbortFile());
        }

        $this->unsetData();

        return $this;
    }

    public function isAborted()
    {
        if ($this->_io->fileExists($this->getAbortFile())) {
            $this->_io->unlink($this->getAbortFile());
            Mage::throwException('Process is aborted by another process');
        }
    }

    public function isTimeout()
    {
        $this->isAborted();

        $near = 0.85;
        $currentTime = Mage::getSingleton('core/date')->gmtTimestamp();
        $currentMemory = memory_get_usage(true);
        $maxTime = Mage::helper('feedexport')->getMaxAllowedTime();
        $maxMemory = Mage::helper('feedexport')->getMaxAllowedMemory();

        if ($currentTime - $this->_start > $maxTime * $near) {
            return true;
        }

        if ($currentMemory > $maxMemory * $near) {
            return true;
        }

        return false;
    }

    public function resetTimeout()
    {
        $this->_start = Mage::getSingleton('core/date')->gmtTimestamp();

        return $this;
    }

    public function addChainItem($array)
    {
        $this->load();

        $array['status'] = self::STATUS_READY;

        $this->_data['chain'][$array['key']] = $array;
        $this->_data['created_at'] = Mage::getSingleton('core/date')->gmtTimestamp();

        $this->save();

        return $this;
    }

    public function startChainItem($key)
    {
        $this->load();
        $this->_data['chain'][$key]['started_at'] = Mage::getSingleton('core/date')->gmtTimestamp();
        $this->_data['chain'][$key]['status'] = self::CHAIN_STATUS_PROCESSING;
        $this->save();

        return $this;
    }

    public function finishChainItem($key)
    {
        $this->load();
        $this->_data['chain'][$key]['status'] = self::CHAIN_STATUS_FINISH;
        $this->save();

        return $this;
    }

    public function getEta($idx, $size, $time, $additional = '')
    {
        $etaMsg = null;
        $timediff = Mage::getSingleton('core/date')->gmtTimestamp() - $time;
        $percent = $idx / $size;
        $eta = ($timediff / $percent) * (1 - $percent);
        if ($eta > 3600) {
            $etaMsg = date('h:i:s', $eta);
        } else {
            $etaMsg = date('i:s', $eta);
        }

        $msg = __('%d <sup>%d</sup> of %d (%01.1f%%)');
        $msg = sprintf($msg, $idx, $additional, $size, $idx / $size * 100);

        if ($etaMsg) {
            $msg .= PHP_EOL.__('ETA '.$etaMsg);
        }

        return $msg;
    }

    public function setData($key, $value = null)
    {
        $this->load();
        parent::setData($key, $value);

        if ($key != 'data_changes') {
            $this->_data['updated_at'] = Mage::getSingleton('core/date')->gmtTimestamp();
            $this->save();

            $this->setDataChanges(false);
        }

        return $this;
    }

    public function getData($key = '', $index = null)
    {
        $this->load();

        return parent::getData($key, $index);
    }

    public function hasData($key = '')
    {
        $this->load();

        return parent::hasData($key);
    }

    public function getStatus()
    {
        if ($this->hasData('status')) {
            return $this->getData('status');
        }

        return self::STATUS_READY;
    }

    public function setChainItemValue($key, $var, $value)
    {
        $this->load();
        if (isset($this->_data['chain'][$key])) {
            $this->_data['chain'][$key][$var] = $value;
        } else {
            Mage::throwException('Wrong chain');
        }

        $this->save();

        return $this;
    }

    public function getChainItemValue($key, $var)
    {
        $this->load();

        return @$this->_data['chain'][$key][$var];
    }

    public function toHtml()
    {
        $text = '';

        if ($this->getError()) {
            $text = $this->getError();

            return $text;
        }

        $chain = $this->getChain();
        if (is_array($chain)) {
            foreach ($chain as $item) {
                $text .= '<span class="status '.$item['status'].'">';
                $text .= $item['title'];
                if ($item['status'] == 'processing') {
                    $text .= '...';
                }
                $text .= '</span>';

                if ($item['status'] == 'processing') {
                    if (isset($item['idx'])
                        && isset($item['size'])
                        && isset($item['started_at'])) {
                        $additional = '';
                        if (isset($item['add'])) {
                            $additional = $item['add'];
                        }
                        $text .= '<span class="eta">'.$this->getEta($item['idx'], $item['size'], $item['started_at'], $additional).'</span>';
                    }
                }
            }
        }

        return $text;
    }

    public function __toString()
    {
        $text = '';

        if ($this->getError()) {
            $text = $this->getError();

            return $text;
        }

        $chain = $this->getChain();
        if (is_array($chain)) {
            foreach ($chain as $item) {
                $text .= $item['title'];

                if ($item['status'] == self::CHAIN_STATUS_PROCESSING) {
                    $text .= '...'.PHP_EOL;
                    if (isset($item['idx'])
                        && isset($item['size'])
                        && isset($item['started_at'])) {
                        $additional = '';
                        if (isset($item['add'])) {
                            $additional = $item['add'];
                        }
                        $text .= $this->getEta($item['idx'], $item['size'], $item['started_at'], $additional);
                    }
                } elseif ($item['status'] == self::CHAIN_STATUS_FINISH) {
                    $text .= '...Done';
                }

                $text .= PHP_EOL;
            }
        }

        return $text;
    }
}
