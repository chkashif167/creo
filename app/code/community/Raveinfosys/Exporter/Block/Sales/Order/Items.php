<?php
class Raveinfosys_Exporter_Block_Sales_Order_Items extends Mage_Adminhtml_Block_Sales_Order_Shipment_Create_Items
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