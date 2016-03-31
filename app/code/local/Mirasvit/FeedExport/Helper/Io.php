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



class Mirasvit_FeedExport_Helper_Io extends Mage_Core_Helper_Abstract
{
    protected $_lockPointers = array();

    public function write($filename, $content, $mode = 'w')
    {
        $wait = true;
        $fp = fopen($filename, $mode);
        if ($this->isWin()) {
            fwrite($fp, $content);
        } else {
            flock($fp, LOCK_SH, $wait);
            fwrite($fp, $content);
            flock($fp, LOCK_UN);
        }
        fclose($fp);

        @chmod($filename, 0777);

        if (!$this->fileExists($filename)) {
            Mage::throwException(sprintf('File %s not created.', $filename));
        }
    }

    public function read($filename)
    {
        $wait = true;
        if (!$this->fileExists($filename)) {
            Mage::throwException(sprintf('File %s not exists.', $filename));
        } else {
            $fp = fopen($filename, 'r');
            if ($this->isWin()) {
                $content = file_get_contents($filename);
            } else {
                flock($fp, LOCK_SH, $wait);
                $content = file_get_contents($filename);
                flock($fp, LOCK_UN);
            }
            fclose($fp);

            return $content;
        }
    }
    public function copy($from, $to)
    {
        if (!$this->fileExists($from)) {
            Mage::throwException(sprintf('File %s not exists.', $from));
        }

        copy($from, $to);

        @chmod($to, 0777);

        if (!$this->fileExists($to)) {
            Mage::throwException(sprintf('File %s not copied to %s', $from, $to));
        }

        return $this;
    }

    public function fileExists($file)
    {
        $result = file_exists($file);
        if ($result) {
            $result = is_file($file);
        }

        return $result;
    }

    public function dirExists($path)
    {
        $result = file_exists($path);
        if ($result) {
            $result = is_dir($path);
        }

        return $result;
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
        if (!$this->dirExists($dir)) {
            return true;
        }

        $result = self::rmdirRecursive($dir, $recursive);

        if (!$result) {
            Mage::throwException(sprintf("Can't remove folder %s", $dir));
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

    public function streamOpen($file, $mode = 'r')
    {
        $fp = fopen($file, $mode);
        @chmod($file, 0777);
        if (!$fp) {
            Mage::throwException(sprintf("Can't open file %s to %s", $file, $mode));
        }

        return $fp;
    }

    public function streamRead($fp)
    {
        if (!$fp) {
            return false;
        }
        if (feof($fp)) {
            return false;
        }

        return fgets($fp, 1024);
    }

    public function streamWrite($fp, $str)
    {
        if (!$fp) {
            return false;
        }

        return fwrite($fp, $str);
    }

    public function streamClose($fp)
    {
        return fclose($fp);
    }

    public function lock($file)
    {
        if (!$this->isWin()) {
            flock($this->_getLockFilePointer($file), LOCK_EX | LOCK_NB);
        }

        return $this;
    }

    public function unlock($file)
    {
        if (!$this->isWin()) {
            flock($this->_getLockFilePointer($file), LOCK_UN);
        }

        return $this;
    }

    public function isLocked($file)
    {
        $fp = $this->_getLockFilePointer($file);
        if (!$this->isWin()) {
            if (flock($fp, LOCK_EX | LOCK_NB)) {
                flock($fp, LOCK_UN);

                return false;
            }
        }

        return true;
    }

    public function _getLockFilePointer($file)
    {
        if (!isset($this->_lockPointers[$file])) {
            $this->_lockPointers[$file] = fopen($file, 'w');
            @chmod($file, 0777);
        }

        return $this->_lockPointers[$file];
    }

    public function uploadFile($protocol, $host, $user, $password, $isPassive, $path, $filePath, $fileName)
    {
        $hostInfo = explode(':', $host);
        $host = $hostInfo[0];
        $port = null;

        if (isset($hostInfo[1])) {
            $port = intval($hostInfo[1]);
        }

        if ($protocol == 'sftp') {
            $port = ($port == null) ? 22 : $port;

            if (extension_loaded('ssh2')) {
                if ($connection = ssh2_connect($host, $port)) {
                    $isLoggedIn = false;

                    try {
                        $isLoggedIn = ssh2_auth_password($connection, $user, $password);
                    } catch (Exception $e) {
                        $isLoggedIn = false;
                    }

                    if ($isLoggedIn) {
                        try {
                            $sftp = ssh2_sftp($connection);
                            $stream = fopen('ssh2.sftp://'.$sftp.DS.$path.DS.$fileName, 'w');
                            $data = file_get_contents($filePath.DS.$fileName);
                            fwrite($stream, $data);
                            fclose($stream);

                            return true;
                        } catch (Exception $e) {
                            Mage::throwException('There was a problem while uploading file "'.$filePath.DS.$fileName.'"');
                        }
                    } else {
                        Mage::throwException('Authenticate failure. Can\'t login to host "'.$host.'"');
                    }
                } else {
                    Mage::throwException('Can\'t connect to host "'.$host.'"');
                }
            } else {
                Mage::throwException('You can’t upload the file via SSH because PHP5-SSH2 extension is not installed.
Please contact your hosting provider to install the extension. More information at:
<a href="http://www.php.net/manual/en/book.ssh2.php">http://www.php.net/manual/en/book.ssh2.php</a>');
            }
        } else {
            $port = ($port == null) ? 21 : $port;

            try {
                $connection = ftp_connect($host, $port);

                $isLoggedIn = false;
                try {
                    $isLoggedIn = ftp_login($connection, $user, $password);
                } catch (Exception $e) {
                    $isLoggedIn = false;
                }

                if ($isLoggedIn) {
                    try {
                        ftp_pasv($connection, (bool) $isPassive);
                    } catch (Exception $e) {
                        Mage::throwException('Can\'t change FTP mode');
                    }

                    try {
                        ftp_chdir($connection, $path);
                    } catch (Exception $e) {
                        Mage::throwException('Can\'t change destination directory');
                    }

                    try {
                        ftp_put($connection, $fileName, $filePath.DS.$fileName, FTP_ASCII);

                        return true;
                    } catch (Exception $e) {
                        Mage::throwException('There was a problem while uploading file "'.$filePath.DS.$fileName.'"');
                    }
                } else {
                    Mage::throwException('Authenticate failure. Can\'t login to host "'.$host.'"');
                }
            } catch (Exception $e) {
                Mage::throwException('Can\'t connect to host "'.$host.'"');
            }
        }

        return false;
    }

    public function isWin()
    {
        return (strtolower(substr(PHP_OS, 0, 3)) == 'win') ? true : false;
    }
}
