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



require_once 'abstract.php';

class Mirasvit_Shell_Search extends Mage_Shell_Abstract
{
    public function run()
    {
        if ($this->getArg('engine')) {
            echo get_class(Mage::helper('searchindex')->getSearchEngine()).PHP_EOL;
        } elseif ($this->getArg('reindex')) {
            if (Mage::getSingleton('searchsphinx/config')->getSearchEngine() == 'sphinx') {
                echo Mage::helper('searchindex')->getSearchEngine()->reindex().PHP_EOL;
            }
        } elseif ($this->getArg('reindex-delta')) {
            if (Mage::getSingleton('searchsphinx/config')->getSearchEngine() == 'sphinx') {
                echo Mage::helper('searchindex')->getSearchEngine()->reindex(true).PHP_EOL;
            }
        } elseif ($this->getArg('import-synonyms')) {
            echo $this->_importSynonyms($this->getArg('import-synonyms'));
        } elseif ($this->getArg('import-stopwords')) {
            echo $this->_importStopwords($this->getArg('import-stopwords'));
        } elseif ($this->getArg('status')) {
            echo Mage::helper('searchindex')->getSearchEngine()->isSearchdRunning();
        } elseif (isset($_GET) && isset($_GET['ping'])) {
            echo 'ok';
            exit();
        } else {
            echo $this->usageHelp();
        }

        echo PHP_EOL;
    }

    protected function _importSynonyms($file)
    {
        $csv = file_get_contents(Mage::getBaseDir('var').DS.'import'.DS.$file);
        $data = explode(PHP_EOL, $csv);
        $result = array();
        $i = 0;
        foreach ($data as $value) {
            $value = explode(',', $value);
            $synonym = Mage::getModel('searchsphinx/synonym');
            $synonym->setWord($value[0])
                ->setSynonym($value[1])
                ->save();

            $i++;

            echo $i.' of '.count($data).PHP_EOL;
        }

        return sprintf('Imported %s lines', count($data));
    }

    protected function _importStopwords($file)
    {
        $csv = new Varien_File_Csv();
        $data = $csv->getData(Mage::getBaseDir('var').DS.'import'.DS.$file);
        $result = array();

        foreach ($data as $value) {
            $result[] = array(
                'stopword' => $value[0],
            );
        }

        if (strlen(serialize($result)) < 65535) {
            $config = new Mage_Core_Model_Config();
            $config->saveConfig('searchsphinx/advanced/stopwords', serialize($result), 'default', 0);
        } else {
            return 'File too long';
        }

        return sprintf('Imported %s lines', count($result));
    }

    public function _validate()
    {
    }

    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f search.php -- [options]

  --engine                      Get Current Engine Class Name
  --reindex                     Run sphinx reindex
  --reindex-delta               Run sphinx delta reindex
  --import-synonyms <csv file>  Import synonyms
  --import-stopwords <csv file>  Import synonyms
  help                          This help
USAGE;
    }
}

$shell = new Mirasvit_Shell_Search();
$shell->run();
