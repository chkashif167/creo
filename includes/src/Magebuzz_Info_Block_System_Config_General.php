<?php
class Magebuzz_Info_Block_System_Config_General extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {
	public function render(Varien_Data_Form_Element_Abstract $element) {
		$html = $this->_getHeaderHtml($element);
		
		$html .= $this->_getInfo();
		
		$html .= $this->_getFooterHtml($element);
		return $html;
	}
	
	protected function _getInfo() {
		$html = '<div class="support-info">';
		$html .= '<h3>Support Policy</h3>';
		$html .= '<p>We provide 6 months free support for all of our extensions and templates. We are not responsible for any bug or issue caused of your changes to our products. To report a bug, you can easily go to <a href="http://www.magebuzz.com/support/" title="Magebuzz Support" target="_blank">our Support Page</a>, email, call or submit a ticket.</p>';
		$html .= '<h3>Read the blog</h3><p>The <a href="http://www.magebuzz.com/blog/" target="_blank">Magebuzz Blog</a> is updated regularly with Magento tutorials, Magebuzz new products, updates, promotions... Visit <a href="http://www.magebuzz.com/blog/" target="_blank">Magebuzz Blog</a> recently to be kept updated.</p>';
		$html .= '<h3>Follow Us</h3><div class="magebuzz-follow"><ul><li style="float:left" class="facebook"><a href="http://www.facebook.com/MageBuzz" title="Facebook" target="_blank"><img src="' . $this->getSkinUrl('images/magebuzz/facebook.png') . '" alt="Facebook"/></a></li><li style="float:left" class="twitter"><a href="https://twitter.com/MageBuzz" title="Twitter" target="_blank"><img src="' . $this->getSkinUrl('images/magebuzz/twitter.png') . '" alt="Twitter"/></a></li></ul></div>';
		$html .= '</div>';
		return $html;
	}
}