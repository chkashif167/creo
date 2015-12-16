<?php
class Raveinfosys_Exporter_Block_Sales_Order_Form_Form extends Mage_Adminhtml_Block_Sales_Order_Shipment_View_Form
{
	public function canCreateShippingLabel()
    {
	    $shippingCarrier = $this->getOrder()->getShippingCarrier();
		if($shippingCarrier)
        return $shippingCarrier && $shippingCarrier->isShippingLabelsAvailable();
		else
		return $shippingCarrier;
    }
}