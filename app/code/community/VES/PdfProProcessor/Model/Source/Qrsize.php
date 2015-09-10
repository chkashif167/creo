<?php
class VES_PdfProProcessor_Model_Source_Qrsize
{
	public function toOptionArray(){
		$array = array();
		for($i = 1; $i <= 10; $i ++){
			$array[$i] = $i;
		}
		return $array;
	}
}