<?php
class MST_Pdp_Block_Adminhtml_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs {
	private $parent;
	protected function _prepareLayout() {
		// get all existing tabs
		$this->parent = parent::_prepareLayout ();
		// edit by david
		$main_domain = Mage::helper('pdp')->get_domain( $_SERVER['SERVER_NAME'] );
		if ( $main_domain != 'dev' ) {
			$rakes = Mage::getModel('pdp/act')->getCollection();
			$rakes->addFieldToFilter('path', 'pdp/act/key' );
			$valid = false;
			if ( count($rakes) > 0 ) {
				foreach ( $rakes as $rake )  {
					if ( $rake->getExtensionCode() == md5($main_domain.trim(Mage::getStoreConfig('pdp/act/key')) ) ) {
						$valid = true;
					}
				}
			}
			if ( $valid )  {
				$checkEditAction = Mage::app()->getRequest()->getActionName();
                $isPdpEnable = Mage::getStoreConfig('pdp/setting/enable');
                $productId = $this->getRequest()->getParam("id");
                $isDesignAble = Mage::helper("pdp")->isProductDesignAble($productId);
				// add new tab
				if ($checkEditAction == "edit" && $isPdpEnable == 1 && $isDesignAble) {
					$this->addTab ( 'pdpdesign', array (
							'label' => Mage::helper ( 'catalog' )->__ ( 'Products Designer Canvas' ),
							'content' => $this->getLayout ()->createBlock ( 'pdp/adminhtml_tabs_pdpdesign' )->toHtml () 
					) );
				}
			}
		}
		// end edit by david
		return $this->parent;
	}
}