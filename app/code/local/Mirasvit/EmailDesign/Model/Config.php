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
 * @package   Follow Up Email
 * @version   1.0.2
 * @build     564
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_EmailDesign_Model_Config extends Varien_Object
{
    public function getBasePath()
    {
        $dir = Mage::getBaseDir('media').DS.'emaildesign';
        if (!file_exists($dir)) {
            mkdir($dir);
        }

        return $dir;
    }

    public function getTmpPath()
    {
        $dir = $this->getBasePath().DS.'tmp';
        if (!file_exists($dir)) {
            mkdir($dir);
        }

        return $dir;
    }

    public function getDesignPath()
    {
        $dir = $this->getBasePath().DS.'design';
        if (!file_exists($dir)) {
            mkdir($dir);
        }

        return $dir;
    }

    public function getMailchimpPath()
    {
        $dir = $this->getBasePath().DS.'mailchimp';
        if (!file_exists($dir)) {
            mkdir($dir);
        }

        return $dir;
    }

    public function getTemplatePath()
    {
        $dir = $this->getBasePath().DS.'template';
        if (!file_exists($dir)) {
            mkdir($dir);
        }

        return $dir;
    }

    public function getVariablesHelpers()
    {
        $result = array();
        
        $pathes = array(
            'email'       => Mage::getModuleDir('', 'Mirasvit_Email').DS.'Helper'.DS.'Variables',
            'emaildesign' => Mage::getModuleDir('', 'Mirasvit_EmailDesign').DS.'Helper'.DS.'Variables',
        );

        $io = new Varien_Io_File();
        $io->open();

        foreach ($pathes as $pathKey => $path) {
            $io->cd($path);

            foreach ($io->ls(Varien_Io_File::GREP_FILES) as $event) {
                if ($event['filetype'] != 'php') {
                    continue;
                }

                $info = pathinfo($event['text']);

                $result[] = $pathKey.'/variables_'.strtolower($info['filename']);
            }
        }

        $io->close();
        
        return $result;
    }
}