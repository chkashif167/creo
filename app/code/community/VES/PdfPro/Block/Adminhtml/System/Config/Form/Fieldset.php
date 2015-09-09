<?php
/**
 * Config form fieldset renderer
 *
 * @category   VES
 * @package    VES_PdfPro
 * @author     Easy PDF Invoice Team <support@easypdfinvoice.com>
 */
class VES_PdfPro_Block_Adminhtml_System_Config_Form_Fieldset extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    /**
     * Return header comment part of html for fieldset
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getHeaderCommentHtml($element)
    {
        $info = array('easypdfinvoice'=>array('label'=>Mage::helper('pdfpro')->__('Easy PDF Invoice Version'),'value'=>Mage::helper('pdfpro')->getVersion()));
    	$html = '
        <div style="margin-bottom: 20px; display: block; padding: 5px; position: relative; border: 1px dashed #FF0000;">
        <table class="form-list" cellspacing="0">
        ';
    	$transport = new Varien_Object($info);
    	Mage::dispatchEvent('ves_pdfpro_config_version',array('transport'=>$transport));
    	$info = $transport->getData();
    	foreach($info as $row){
	    	$html .= '<tr><td class="label">'.$row['label'].'</td><td class="value"><strong style="color: #1f5e00;">'.$row['value'].'</strong></td></tr>';
    	}
        
        $html .='
        </table>
        </div>';
    	return $html.$element->getComment();
    }
}