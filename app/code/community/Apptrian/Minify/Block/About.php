<?php
/**
 * @category   Apptrian
 * @package    Apptrian_Minify
 * @author     Apptrian
 * @copyright  Copyright (c) 2015 Apptrian (http://www.apptrian.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Apptrian_Minify_Block_About
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
    	$version  = Mage::helper('apptrian_minify')->getExtensionVersion();
        $logopath =	'http://www.apptrian.com/media/apptrian.gif';
        $html = <<<HTML
<div style="background:url('$logopath') no-repeat scroll 15px 15px #e7efef; border:1px solid #ccc; min-height:100px; margin:5px 0; padding:15px 15px 15px 140px;">
	<p>
		<strong>Apptrian Minify HTML CSS JS Extension v$version</strong><br />
		Minify HTML CSS JS including inline CSS/JS and speed up your site. Works with default Magento CSS/JS merger.
	</p>
    <p>
        Website: <a href="http://www.apptrian.com" target="_blank">www.apptrian.com</a><br />
		Like, share and follow us on 
		<a href="https://www.facebook.com/apptrian" target="_blank">Facebook</a>, 
		<a href="https://plus.google.com/+ApptrianCom" target="_blank">Google+</a>, 
		<a href="http://www.pinterest.com/apptrian" target="_blank">Pinterest</a>, and 
        <a href="http://twitter.com/apptrian" target="_blank">Twitter</a>.<br />
        If you have any questions send email at <a href="mailto:service@apptrian.com">service@apptrian.com</a>.
    </p>
</div>
HTML;
        return $html;
    }
}