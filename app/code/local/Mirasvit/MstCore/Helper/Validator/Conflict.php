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


class Mirasvit_MstCore_Helper_Validator_Conflict extends Mirasvit_MstCore_Helper_Validator_Abstract
{
    protected $_rewriteTypes = array(
        'blocks',
        'helpers',
        'models',
    );

    public function testConflicts()
    {
        $result = self::SUCCESS;
        $title = 'Check Rewrite conflicts';
        $description = array();

        $rewrites = $this->loadRewrites();
        $conflictCounter = 0;
        foreach ($rewrites as $type => $data) {
            if (count($data) > 0 && is_array($data)) {
                foreach ($data as $class => $rewriteClass) {
                    if (count($rewriteClass) > 1) {
                        if ($this->_isInheritanceConflict($rewriteClass)) {
                            $tableData[] = array(
                                'Type'         => $type,
                                'Class'        => $class,
                                'Rewrites'     => implode(', ', $rewriteClass),
                                'Loaded Class' => $this->_getLoadedClass($type, $class),
                            );
                            $conflictCounter++;
                        }
                    }
                }
            }
        }
        if ($conflictCounter > 0) {
            $result = self::INFO;
            foreach ($tableData as $record) {
                $description[] = '<b>'.$record['Class'].'</b> <br>Rewrites: '.$record['Rewrites'].' <br>Loaded Class: '.$record['Loaded Class'];
            }
        }
        return array($result, $title, $description);
    }

    /**
     * Return all rewrites
     *
     * @return array
     */
    protected function loadRewrites()
    {
        $return = array(
            'blocks',
            'models',
            'helpers',
        );

        // Load config of each module because modules can overwrite config each other. Globl config is already merged
        $modules = Mage::getConfig()->getNode('modules')->children();
        foreach ($modules as $moduleName => $moduleData) {
            // Check only active modules
            if (!$moduleData->is('active')) {
                continue;
            }

            // Load config of module
            $configXmlFile = Mage::getConfig()->getModuleDir('etc', $moduleName) . DIRECTORY_SEPARATOR . 'config.xml';
            if (! file_exists($configXmlFile)) {
                continue;
            }

            $xml = simplexml_load_file($configXmlFile);
            if ($xml) {
                $rewriteElements = $xml->xpath('//rewrite');
                foreach ($rewriteElements as $element) {
                    foreach ($element->children() as $child) {
                        $type = simplexml_import_dom(dom_import_simplexml($element)->parentNode->parentNode)->getName();
                        if (!in_array($type, $this->_rewriteTypes)) {
                            continue;
                        }
                        $groupClassName = simplexml_import_dom(dom_import_simplexml($element)->parentNode)->getName();
                        if (!isset($return[$type][$groupClassName . '/' . $child->getName()])) {
                            $return[$type][$groupClassName . '/' . $child->getName()] = array();
                        }
                        $return[$type][$groupClassName . '/' . $child->getName()][] = (string) $child;
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Check if rewritten class has inherited the parent class.
     * If yes we have no conflict. The top class can extend every core class.
     * So we cannot check this.
     *
     * @var array $classes
     * @return bool
     */
    protected function _isInheritanceConflict($classes)
    {
        $classes = array_reverse($classes);
        for ($i = 0; $i < count($classes) - 1; $i++) {
            try {
                if (class_exists($classes[$i])
                    && class_exists($classes[$i + 1])
                ) {
                    if (! is_a($classes[$i], $classes[$i + 1], true)) {
                        return true;
                    }
                }
            } catch (Exception $e) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns loaded class by type like models or blocks
     *
     * @param string $type
     * @param string $class
     * @return string
     */
    protected function _getLoadedClass($type, $class)
    {
        switch ($type) {
            case 'blocks':
                return Mage::getConfig()->getBlockClassName($class);

            case 'helpers':
                return Mage::getConfig()->getHelperClassName($class);

            default:
            case 'models':
                return Mage::getConfig()->getModelClassName($class);
                break;
        }
    }
}