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



class Mirasvit_MstCore_Helper_Validator_Crc extends Mirasvit_MstCore_Helper_Validator_Abstract
{
    const NOT_FOUND = 10;
    protected $modifiedFiles = array();

    public function testMirasvitCrc($modules)
    {
        $result = self::SUCCESS;
        $title = 'Files of extension Mirasvit '.implode(',', $modules).'';
        $description = array();

        foreach ($modules as $module) {
            $crcFile = Mage::getBaseDir('code').'/local/Mirasvit/MstCore/etc/'.strtolower($module).'.crc';
            if (!is_file($crcFile)) {
                $crcFile = Mage::getBaseDir('code').'/local/Mirasvit/MstCore/etc/'.$module.'.crc';
            }
            if (!is_file($crcFile)) {
                if ($result !== self::FAILED) {
                    $result = self::INFO;
                }
                $description[] = "Check of extension Mirasvit {$module} is skipped";
                continue;
            }
            $res = $this->checkCrc($crcFile);
            if ($res === self::SUCCESS) {
                continue;
            } elseif (is_array($res)) {
                list($cantFindFiles, $cantReadFiles, $modifiedFiles) = $res;
                foreach ($cantFindFiles as $file) {
                    $description[] = "Can't find a file $file";
                    $result = self::FAILED;
                }
                foreach ($cantReadFiles as $file) {
                    $description[] = "Can't read a file $file";
                    $result = self::FAILED;
                }
                $changeLog = '';
                $changeLogFile = Mage::getBaseDir('code').'/local/Mirasvit/'.$module.'/changelog.txt';
                if (file_exists($changeLogFile)) {
                    $changeLog = file_get_contents($changeLogFile);
                }
                foreach ($modifiedFiles as $file) {
                    $baseFile = trim(str_replace(Mage::getBaseDir(), '', $file), '/');
                    if (strpos($changeLog, $baseFile) !== false) {
                        $description[] = "Modified file $file (present in changelog)";
                    } else {
                        $description[] = "Modified file $file";
                        $result = self::FAILED;
                    }
                }
            }
        }

        return array($result, $title, $description);
    }

    public function testMagentoCrc($filters = array())
    {
        $result = self::SUCCESS;
        $title = 'Magento Core files';
        $description = array();

        $crcFile = $this->getCrcFile();
        if (is_file($crcFile)) {
            $res = $this->checkCrc($crcFile, $filters);
            if ($res === self::SUCCESS) {
            } elseif (is_array($res)) {
                $result = self::SUCCESS;
                list($cantFindFiles, $cantReadFiles, $modifiedFiles) = $res;
                foreach ($cantFindFiles as $file) {
                    $description[] = "Can't find a file $file";
                    $result = self::FAILED;
                }
                foreach ($cantReadFiles as $file) {
                    $description[] = "Can't read a file $file";
                    $result = self::FAILED;
                }
                foreach ($modifiedFiles as $file) {
                    $description[] = "File $file was changed";
                    $result = self::INFO;
                }
            }
        } else {
            $result = self::INFO;
            $description[] = "Check of Magento Core files is skipped (we don't have CRC sum for this magento version)";
        }

        return array($result, $title, $description);
    }

    private function getCrcFile()
    {
        $basePath = Mage::getBaseDir('code').'/local/Mirasvit/MstCore/etc/crc/';
        $magentoEdition = Mage::helper('mstcore/version')->getEdition();
        $magentoVersion = Mage::getVersion();
        $basePath .= $magentoEdition.$magentoVersion.'.txt';
        if (!is_file($basePath)) {
            return false;
        }

        return $basePath;
    }

    public function checkCrc($crcFile, $filters = array())
    {
        if (!is_file($crcFile)) {
            return self::NOT_FOUND;
        }

        $contents = file_get_contents($crcFile);
        if (strpos($contents, 'app/code') === false) {
            $crcs = gzuncompress($contents);
        } else {
            $crcs = $contents;
        }

        $description = array();

        $crcs = explode("\n", $crcs);
        // echo "<pre>";
        // print_r($crcs);
        $cantFindFiles = array();
        $cantReadFiles = array();
        $modifiedFiles = array();

        foreach ($crcs as $value) {
            $ar = explode('  ', $value);
            $origCrc = $ar[0];
            $file = $ar[1];
            if (strpos($file, '/Test/')) {
                continue;
            }
            if (strpos($file, 'Helper/Code.php')) {
                continue;
            }
            if (strpos($file, '.csv')) {
                continue;
            }
            if (count($filters)) {
                $has = false;
                foreach ($filters as $path) {
                    if (strpos($file, '/'.$path) === 0) {
                        $has = true;
                        break;
                    }
                }
                if (!$has) {
                    continue;
                }
            }

            $file = Mage::getBaseDir().'/'.ltrim($file, '/');

            if (!is_file($file)) {
                $cantFindFiles[] = $file;
                $description[] = "Can't find a file $file";
                continue;
            }
            $contents = @file_get_contents($file);
            $crc = $this->calculateCrc($contents);
            if ($contents == '' && $crc !== $origCrc) {
                $cantReadFiles[] = $file;
                $description[] = "Can't read a file $file";
                continue;
            }
            if ($crc !== $origCrc) {
                $modifiedFiles[] = $file;
                $description[] = "File $file has modifications or incomplete";
                $this->modifiedFiles = $file;
                continue;
            }
        }

        return array($cantFindFiles, $cantReadFiles, $modifiedFiles);
    }

    //should be the same as in generatecrc.php
    public function calculateCrc($str)
    {
        $pos = strpos($str, '* This source file is subject to the Mirasvit Software License');
        if ($pos !== false && $pos < 40) {
            $start = strpos($str, '/**');
            $comment = substr($str, $start - 1, strpos($str, '*/') + 5 - $start);
            $str = substr($str, 0, $start).substr($str, $start + strlen($comment));
        }

        $newStr = '';
        for ($i = 0; $i < strlen($str); ++$i) {
            $c = ord($str[$i]);
            if ($c > 31 && $c < 127) {
                $newStr .= $str[$i];
            }
        }

        return md5($newStr);
    }

    private function rscandir($base = '', &$data = array())
    {
        $array = array_diff(scandir($base), array('.', '..'));
        foreach ($array as $value) {
            if (is_dir($base.$value)) {
                $data[] = $base.$value.'/';
                $data = $this->rscandir($base.$value.'/', $data);
            } elseif (is_file($base.$value)) {
                $data[] = $base.$value;
            }
        }

        return $data;
    }
}
