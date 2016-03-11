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


/**
 * Class for do CLI request
 *
 * @category Mirasvit
 * @package  Mirasvit_FeedExport
 */
class Mirasvit_FeedExport_Helper_Request extends Mage_Core_Helper_Abstract
{
    protected $_shellScript = null;
    protected $_phpBin      = null;

    public function __construct()
    {
        $this->_shellScript = Mage::getBaseDir().DS.'shell'.DS.'feedexport.php';
        $this->_phpBin      = $this->getPhpBin();
    }

    /**
     * Return path to php bin
     *
     * @return string
     */
    public function getPhpBin()
    {
        $phpBin = 'php';

        if (PHP_BINDIR) {
            $phpBin = PHP_BINDIR.DS.'php';
        }

        return $phpBin;
    }

    /**
     * Check, that exec requests to shell/feedexport.php are possible
     *
     * @return boolean
     */
    public function pingShell()
    {
        $cmd = "$this->_phpBin $this->_shellScript --ping";

        if ($this->exec($cmd) === '1') {
            return true;
        }

        return false;
    }

    /**
     * Run php exec command
     *
     * @param  string $cmd [description]
     *
     * @return string
     */
    public function exec($cmd)
    {
        $result = array();
        @exec($cmd, $result);

        return implode(' ', $result);
    }

    /**
     * Do request
     *
     * @param  string $method
     * @param  object $feed
     *
     * @return string
     */
    public function request($method, $feed)
    {
        $result = false;

        $feedId = $feed->getId();

        if ($this->pingShell()) {
            $cmd = "$this->_phpBin $this->_shellScript --control --method $method --feed $feedId";
            $result = $this->exec($cmd);
        } else {
            call_user_func(array($feed, $method));
        }

        return $result;
    }
}