<?php
/**
 * VES_PdfPro_Model_Sales_Order_Pdf_Total_Default
 *
 * @author		VnEcoms Team <support@vnecoms.com>
 * @website		http://www.vnecoms.com
 */
class VES_PdfPro_Model_Sales_Order_Pdf_Total_Default extends Mage_Sales_Model_Order_Pdf_Total_Default
{
	/**
     * Get Total amount from source
     *
     * @return float
     */
    public function getAmount()
    {
        return abs($this->getSource()->getDataUsingMethod($this->getSourceField()));
    }
}