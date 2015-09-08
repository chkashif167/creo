<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VariablesController
 *
 * @author Ea Design
 */
class EaDesign_PdfGenerator_Adminhtml_VariableController extends Mage_Adminhtml_Controller_Action
{
    /**
     * WYSIWYG Plugin Action
     *
     */
    public function wysiwygPluginAction()
    {
        $customVariables = Mage::getModel('eadesign/variables_process')->getVariablesOptionArray(true);
        $this->getResponse()->setBody(Zend_Json::encode($customVariables));
    }
}

?>
