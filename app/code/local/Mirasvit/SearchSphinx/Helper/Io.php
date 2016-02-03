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
 * @category Mirasvit
 */
class Mirasvit_SearchSphinx_Helper_Io extends Mage_Core_Helper_Abstract
{
    public function write($filename, $content, $mode = 'w')
    {
        $fp = fopen($filename, $mode);
        fwrite($fp, $content);
        fclose($fp);

        @chmod($filename, 0777);

        if (!$this->fileExists($filename)) {
            Mage::throwException(sprintf('File %s not created.', $filename));
        }
    }

    public function read($filename)
    {
        if (!$this->fileExists($filename)) {
            Mage::throwException(sprintf('File %s not exists.', $filename));
        } else {
            $fp = fopen($filename, 'r');
            $content = file_get_contents($filename);
            fclose($fp);

            return $content;
        }
    }

    public function fileExists($file)
    {
        $result = file_exists($file);
        if ($result) {
            $result = is_file($file);
        }

        return $result;
    }

    public function directoryExists($path)
    {
        $result = file_exists($path);
        if ($result) {
            $result = is_dir($path);
        }

        return $result;
    }

    public function isWriteable($path)
    {
        if ($this->fileExists($path)) {
            return is_writable($path);
        } else {
            $info = pathinfo($path);

            return is_writable($info['dirname']);
        }
    }

    public function unlink($file)
    {
        return @unlink($file);
    }

    public function mkdir($dir, $mode = 0777, $recursive = true)
    {
        $result = @mkdir($dir, $mode, $recursive);

        if ($result) {
            @chmod($dir, $mode);
        }

        return $result;
    }

    public function rmdir($dir, $recursive = false)
    {
        $result = self::rmdirRecursive($dir, $recursive);

        if (!$result) {
            Mage::throwException('fasdfa');
        }

        return $result;
    }

    public function rmdirRecursive($dir, $recursive = true)
    {
        if ($recursive) {
            if (is_dir($dir)) {
                foreach (scandir($dir) as $item) {
                    if (!strcmp($item, '.') || !strcmp($item, '..')) {
                        continue;
                    }
                    $this->rmdirRecursive($dir.DS.$item, $recursive);
                }
                $result = @rmdir($dir);
            } else {
                $result = @unlink($dir);
            }
        } else {
            $result = @rmdir($dir);
        }

        return $result;
    }
}
