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



require_once 'abstract.php';

class Mirasvit_Shell_FeedExport extends Mage_Shell_Abstract
{
    public function run()
    {
        Mage::register('custom_entry_point', true, true);

        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        set_time_limit(36000);

        if ($this->getArg('generate')) {
            $feeds = $this->_parseFeedString($this->getArg('generate'));
            foreach ($feeds as $feed) {
                $this->generate($feed);
            }
        } elseif ($this->getArg('control')) {
            $method = $this->getArg('method');
            $feedId = $this->getArg('feed');
            $feed = Mage::getModel('feedexport/feed')->load($feedId);
            call_user_func(array($feed, $method));
        } elseif ($this->getArg('deliver')) {
            $feeds = $this->_parseFeedString($this->getArg('deliver'));
            foreach ($feeds as $feed) {
                $feed->delivery();
            }
        } elseif ($this->getArg('ping')) {
            echo '1';
        } elseif ($this->getArg('cron')) {
            Mage::getSingleton('feedexport/observer')->generate();
        } elseif ($this->getArg('test')) {
            $this->_test();
        } else {
            echo $this->usageHelp();
        }
    }

    protected function _parseFeedString($string)
    {
        $feeds = array();
        if ($string == 'all') {
            $collection = Mage::getModel('feedexport/feed')->getCollection()
                ->addFieldToFilter('is_active', 1);
            foreach ($collection as $feed) {
                $feed = $feed->load($feed->getId());
                $feeds[] = $feed;
            }
        } elseif (!empty($string)) {
            $ids = explode(',', $string);
            foreach ($ids as $feedId) {
                $feed = Mage::getModel('feedexport/feed')->load(trim($feedId));
                if (!$feed) {
                    echo 'Warning: Unknown feed with id '.trim($feedId)."\n";
                } else {
                    $feeds[] = $feed;
                }
            }
        }

        return $feeds;
    }

    public function generate($feed)
    {
        $ts = microtime(true);

        $name = '['.$feed->getId().'] '.$feed->getName();
        echo $name.str_repeat('.', 50 - strlen($name)).'<br>';
        $status = null;
        $feed->getGenerator()->getState()->reset();
        $feed->generateCli(true);
        echo 'done'.'<br>';
    }

    protected function _test()
    {
        echo '<pre>';
        $product = Mage::getModel('catalog/product')->load(234);
        $pattern = Mage::getModel('feedexport/feed_generator_pattern_product');

        $variables = array(
            '{name}',
            '{sku}',
            '{(return $sku;)}',
            '{(return str_replace("0", "*", $sku);)}',
            '{price}',
            '{group_price}',
            '{group_price2}',
            '{group_price3}',
            '{(return $price * 1.2;)}',
            '{(return $group_price3 * 1.2;)}',
            '{(return $group_price2 / 10 * 4 - 2.5;)}',
            '{group_price2}',
            '{group_price2}',
            '{group_price2}',

        );

        foreach ($variables as $var) {
            echo $var;
            echo str_repeat(' ', 50 - strlen($var));
            echo $pattern->getValue($var, $product);
            echo '<br>';
        }
    }

    public function _validate()
    {
    }

    /**
     * Retrieve Usage Help Message.
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f feedexport.php -- [options]

  --generate all      Generate all active feeds
  --generate <id>     Generate Feed with ID <id>
  --deliver all       Deliver all active feeds
  --deliver <id>      Deliver Feed with ID <id>

USAGE;
    }
}

$shell = new Mirasvit_Shell_FeedExport();
$shell->run();
