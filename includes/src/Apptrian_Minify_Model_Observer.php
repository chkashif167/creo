<?php
/**
 * @category   Apptrian
 * @package    Apptrian_Minify
 * @author     Apptrian
 * @copyright  Copyright (c) 2015 Apptrian (http://www.apptrian.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Apptrian_Minify_Model_Observer
{
	
	/**
	 * Flag used to determine if block HTML minification is set in config.
	 * 
	 * @var null|bool
	 */
	protected $blockMinifyFlag = null;
	
	/**
	 * Flag used to determine if maximum HTML minification is set in config.
	 * 
	 * @var null|bool
	 */
	protected $maxMinifyFlag = null;
	
	/**
	 * Minify options array.
	 *
	 * @var array
	 */
	protected $minifyOptions = array('cssMinifier' => array('Minify_CSS', 'minify'),
									 'jsMinifier'  => array('JSMin', 'minify'));
	
	/**
	 * Method returns status of block minification.
	 * 
	 * @return bool
	 */
	public function getBlockMinifyStatus()
	{
		
		if ($this->blockMinifyFlag === null) {
			
			if (Mage::getStoreConfigFlag('apptrian_minify/general/minify_html')
				&& Mage::getStoreConfigFlag('apptrian_minify/general/compatibility')
			) {
				
				$this->blockMinifyFlag = true;
				
			} else {
				
				$this->blockMinifyFlag = false;
				
			}
			
		}
		
		return $this->blockMinifyFlag;
	
	}
	
	/**
	 * Method returns status of maximum HTML minification.
	 * 
	 * @return bool
	 */
	public function getMaxMinifyStatus()
	{
		
		if ($this->maxMinifyFlag === null) {
				
			$this->maxMinifyFlag = Mage::getStoreConfigFlag('apptrian_minify/general/max_minification');
		
		}
		
		return $this->maxMinifyFlag;
		
	}
	
	/**
	 * This method is minifying HTML of every block.
	 * Multiple calls per page but they are cached.
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function minifyBlockHtml(Varien_Event_Observer $observer)
	{
		
		if ($this->getBlockMinifyStatus()) {
			
			$block     = $observer->getBlock();
			$transport = $observer->getTransport();
			$html      = $transport->getHtml();
			
			if ($this->getMaxMinifyStatus()) {
				$transport->setHtml(Minify_HTMLMaxComp::minify($html, $this->minifyOptions));
			} else {
				$transport->setHtml(Minify_HTMLComp::minify($html, $this->minifyOptions));
			}
			
		}
		
	}
	
	/**
	 * This method is minifying HTML of entire page.
	 * One call per entire page.
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function minifyPageHtml(Varien_Event_Observer $observer)
	{
		
		if (Mage::getStoreConfigFlag('apptrian_minify/general/minify_html')
			&& !Mage::getStoreConfigFlag('apptrian_minify/general/compatibility')
		) {
			
			$response = $observer->getEvent()->getControllerAction()->getResponse();
			$html     = $response->getBody();
			
			if (stripos($html, '<!DOCTYPE html') !== false) {
				
				$type = false;
				
				foreach ($response->getHeaders() as $header) {
					
					if (stripos($header['name'], 'Content-Type') !== false) {
						
						if (stripos($header['value'], 'text/html') !== false) {
							
							$type = true;
							
							break;
							
						}
						
					}
					
				}
				
				if ($type) {
					
					if (Mage::getStoreConfigFlag('apptrian_minify/general/max_minification')) {
						$response->setBody(Minify_HTMLMax::minify($html, $this->minifyOptions));
					} else {
						$response->setBody(Minify_HTML::minify($html, $this->minifyOptions));
					}
					
				}
				
			}
			
		}
		
	}
	
}