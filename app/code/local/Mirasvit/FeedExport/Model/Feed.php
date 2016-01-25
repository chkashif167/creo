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
 * @version   1.1.2
 * @build     616
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_FeedExport_Model_Feed extends Mage_Core_Model_Abstract
{
    protected $_store           = null;
    protected $_rule            = null;
    protected $_trackerRule     = null;
    protected $_generator       = null;
    protected $_state           = null;
    protected $_fileNameWithExt = null;
    protected $_tmpPathKey      = null;

    protected function _construct()
    {
        $this->_init('feedexport/feed');
    }

    public function getTmpPathKey()
    {
        return $this->getId() . $this->getGenerator()->getMode();
    }

    public function getStore()
    {
        if (!$this->_store) {
            $this->_store = Mage::getModel('core/store')->load($this->getStoreId());
        }

        return $this->_store;
    }

    public function getRuleIds()
    {
        if ($this->hasData('rule_ids') || is_array($this->getData('rule_ids'))) {
            return $this->getData('rule_ids');
        }

        return array();
    }

    public function getNotificationEvents()
    {
        if (!is_array($this->getData('notification_events'))) {
            $this->setNotificationEvents(explode(',', $this->getData('notification_events')));
        }

        return $this->getData('notification_events');
    }

    public function getGenerator()
    {
        if (!$this->_generator) {
            $this->_generator = Mage::getModel('feedexport/feed_generator');
            $this->_generator->setFeed($this)
                ->init();
        }

        return $this->_generator;
    }

    public function getUrl()
    {
        $feedUrl = false;
        if (file_exists(Mage::getBaseDir('media').DS.'feed'.DS.$this->getFilenameWithExt())) {
            $feedUrl = Mage::getBaseUrl('media').'feed'.DS.$this->getFilenameWithExt();
            if (Mage::getBaseUrl() !== substr($feedUrl, 0, strlen(Mage::getBaseUrl()))) {
                $feedUrl = str_replace(Mage::getModel('core/url')->parseUrl($feedUrl)->getHost(), Mage::app()->getRequest()->getHttpHost(), $feedUrl);
            }
        }

        return $feedUrl;
    }

    public function getArchiveUrl()
    {
        if ($this->getArchivation() &&
            file_exists(Mage::getBaseDir('media').DS.'feed'.DS.$this->getFilenameWithExt().'.'.$this->getArchivation())) {
            return Mage::getBaseUrl('media').'feed'.DS.$this->getFilenameWithExt().'.'.$this->getArchivation();
        }

        return false;
    }

    public function getFormat()
    {
        $this->_format = Mage::helper('feedexport/format')
            ->parseFormat($this->getXmlFormat());

        return $this->_format;
    }

    public function generate()
    {
        $generator = $this->getGenerator();

        if (Mage::registry('current_state')) {
            Mage::unregister('current_state');
        }
        Mage::register('current_state', $generator->getState());

        $appEmulation           = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($this->getStore()->getId());

        try {
            Mage::getConfig()->loadEventObservers('frontend');
        } catch(Exception $e) {}

        Mage::app()->addEventArea('frontend');

        $generator->process();

        $this->updateGenerationInfo($generator);

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $generator->getState()->getStatus();
    }

    public function generateCli($verbose = false)
    {
        $requestHelper = Mage::helper('feedexport/request');
        $status        = $this->getGenerator()->getState()->getStatus();

        $this->getGenerator()->getState()->reset();

        $status = null;
        while ($status != Mirasvit_FeedExport_Model_Feed_Generator_State::STATUS_READY) {
            $requestHelper->request('generate', $this);
            $status = $this->getGenerator()->getState()->getStatus();

            if ($status == Mirasvit_FeedExport_Model_Feed_Generator_State::STATUS_ERROR) {
                break;
            }

            if ($verbose) {
                echo $this->getGenerator()->getState()->__toString().PHP_EOL;
            }

            $this->getGenerator()->getState()->resetTimeout();
        }
    }

    public function generateTest()
    {
        $generator = $this->getGenerator();
        $generator->setMode('test');

        if (Mage::registry('current_state')) {
            Mage::unregister('current_state');
        }
        Mage::register('current_state', $generator->getState());

        $appEmulation           = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($this->getStore()->getId());
        try {
            Mage::getConfig()->loadEventObservers('frontend');
        } catch(Exception $e) {}
        Mage::app()->addEventArea('frontend');

        do {
            $generator->process();
        } while (!in_array($generator->getState()->getStatus(), array(
            Mirasvit_FeedExport_Model_Feed_Generator_State::STATUS_READY,
            Mirasvit_FeedExport_Model_Feed_Generator_State::STATUS_ERROR
        )));

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
    }

    public function updateGenerationInfo($generator)
    {
        if ($generator->getState()->isReady()) {
            $this->setGeneratedAt(Mage::getSingleton('core/date')->gmtDate())
                ->setGeneratedCnt($generator->getState()->getChainItemValue('iterator_product', 'size'))
                ->setGeneratedTime(Mage::getSingleton('core/date')->gmtTimestamp() - $generator->getState()->getCreatedAt())
                ->save();

            Mage::dispatchEvent('feedexport_generation_success', array('feed' => $this));
        }

        return $this;
    }

    public function delivery()
    {
        if (!$this->getFtp()) {
            return false;
        }

        try {
            Mage::helper('feedexport/io')->uploadFile(
                $this->getFtpProtocol(),
                $this->getFtpHost(),
                $this->getFtpUser(),
                $this->getFtpPassword(),
                $this->getFtpPassiveMode(),
                $this->getFtpPath(),
                Mage::getSingleton('feedexport/config')->getBasePath(),
                $this->getFilenameWithExt()
            );

            $this->setUploadedAt(Mage::getSingleton('core/date')->gmtDate())
                ->save();
            Mage::dispatchEvent('feedexport_delivery_success', array('feed' => $this));
            Mage::helper('feedexport')->addToHistory($this, __('Delivery'), __('Success delivery to %s', $this->getFtpHost()));
        } catch (Exception $e) {
            Mage::dispatchEvent('feedexport_delivery_fail', array('feed' => $this, 'error' => $e->getMessage()));
            Mage::helper('feedexport')->addToHistory($this, __('Delivery'), __('Fail delivery to %s', $this->getFtpHost()));

            throw $e;
        }

        return true;
    }

    public function getFilenameWithExt()
    {
        if ($this->_fileNameWithExt == null) {
            $file = $this->getData('filename');

            if (strpos($file, '.') === false) {
                $file .= '.'.$this->getData('type');
            }

            $file = Mage::getSingleton('feedexport/feed_generator_pattern')->getPatternValue($file, null, null);

            $this->_fileNameWithExt = $file;
        }

        return $this->_fileNameWithExt;
    }

    public function getHistoryCollection()
    {
        return Mage::getModel('feedexport/feed_history')->getCollection()
            ->addFieldToFilter('feed_id', $this->getId())
            ->setOrder('created_at', 'desc')
            ->setOrder('history_id', 'desc');
    }

    public function addToHistory($title, $message = null, $type = null)
    {
        Mage::getModel('feedexport/feed_history')
                ->setFeedId($this->getId())
                ->setTitle($title)
                ->setMessage($message)
                ->setType($type)
                ->save();

        return $this;
    }

    /**
     * @todo rename to loadFromTemplate
     */
    public function fromTemplate($templateId)
    {
        $template = Mage::getModel('feedexport/template')->load($templateId);
        $this->addData($template->getData());

        return $this;
    }

    public function canRunCron()
    {
        $result  = false;

        $crntDay        = Mage::getSingleton('core/date')->date('w');
        $crntDayOfYear  = Mage::getSingleton('core/date')->date('z');
        $crntTime       = Mage::getSingleton('core/date')->date('G') * 60 + Mage::getSingleton('core/date')->date('i');

        $lastRun        = strtotime($this->getGeneratedAt());
        $lastDayOfYear  = Mage::getSingleton('core/date')->date('z', $lastRun);
        $lastTime       = Mage::getSingleton('core/date')->date('G', $lastRun) * 60 + Mage::getSingleton('core/date')->date('i', $lastRun);

        // we run generation minimum day ago. Need run generation
        if ($crntDayOfYear > $lastDayOfYear) {
            $lastTime = 0;
        }

        if (in_array($crntDay, $this->getCronDay())) {
            foreach ($this->getCronTime() as $cronTime) {
                if ($crntTime >= $cronTime && $cronTime >= $lastTime) {
                    $result = $cronTime;
                    break;
                }
            }
        }

        return $result;
    }

    public function duplicate()
    {
        $feedCopy = Mage::getModel('feedexport/feed')
            ->addData($this->getData())
            ->setId(null)
            ->setCreatedAt(null)
            ->setUpdatedAt(null)
            ->setGeneratedAt(null)
            ->setGeneratedCnt(null)
            ->setGeneratedTime(null)
            ->setUploadedAt(null)
            ->setRuleIds(null)
            ->setFilename($this->getFilename().'_copy')
            ->save();

        $feedCopy->setRuleIds($this->getRuleIds())
            ->save();

        return $this;
    }
}