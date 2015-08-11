<?php
/**
 * @category   Auguria
 * @package    Auguria_Sliders
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_Sliders_Block_Adminhtml_Sliders_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
    	$this->_objectId = 'id';
    	$this->_blockGroup = 'auguria_sliders';
    	$this->_controller = 'adminhtml_sliders';
    	
        parent::__construct();
        
        $objId = $this->getRequest()->getParam($this->_objectId);
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        
        $this->_formScripts[] = "
           function toggleEditor() {
                if (tinyMCE.getInstanceById('cms_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'cms_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'cms_content');
                }
            }
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('sliders_data') && Mage::registry('sliders_data')->getId() ) {
            return Mage::helper('auguria_sliders')->__("Edition of the slider '%s'", $this->htmlEscape(Mage::registry('sliders_data')->getName()));
        }
        else {
            return Mage::helper('auguria_sliders')->__('Add a slider');
        }
    }
}