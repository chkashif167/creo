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



class Mirasvit_Email_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();

        $this->setType('email/rule_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        $attributes = array();

        $ruleClasses = $this->_getRuleClasses();

        foreach ($ruleClasses as $key => $class) {
            $classCondition = Mage::getModel($class);

            $classAttributes = $classCondition->loadAttributeOptions()->getAttributeOption();

            if (is_array($classAttributes)) {
                foreach ($classAttributes as $code => $label) {
                    $attributes[ucfirst($key)][] = array(
                        'value' => 'email/rule_condition_'.$key.'|'.$code,
                        'label' => $label,
                    );
                }
            }
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array(
                'value' => 'email/rule_condition_combine',
                'label' => Mage::helper('email')->__('Conditions Combination'),
            ),
            array(
                'value' => 'email/rule_condition_product_subselect',
                'label' => Mage::helper('email')->__('Products subselection'), ),
        ));

        foreach ($attributes as $group => $arrAttributes) {
            $conditions = array_merge_recursive($conditions, array(
                array(
                    'label' => $group,
                    'value' => $arrAttributes,
                ),
            ));
        }

        return $conditions;
    }

    protected function _getRuleClasses()
    {
        $classes = array();

        $rulesDir = Mage::getModuleDir('', 'Mirasvit_Email').DS.'Model'.DS.'Rule'.DS.'Condition';

        $io = new Varien_Io_File();
        $io->open();
        $io->cd($rulesDir);

        foreach ($io->ls(Varien_Io_File::GREP_FILES) as $event) {
            if ($event['filetype'] != 'php' || $event['text'] === 'Product.php') {
                continue;
            }

            $info = pathinfo($event['text']);
            $class = strtolower($info['filename']);

            $classes[$class] = 'email/rule_condition_'.strtolower($class);
        }
        $io->close();

        return $classes;
    }

    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }
}
