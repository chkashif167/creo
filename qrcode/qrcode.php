<?php
include "qrlib.php";
$errorCorrectionLevel = 'L';
if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
	$errorCorrectionLevel = $_REQUEST['level'];    

$matrixPointSize = 4;
if (isset($_REQUEST['size']))
	$matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);
	
if (isset($_REQUEST['data'])) { 
	$data = $_REQUEST['data'];
	if (trim($_REQUEST['data']) == '')
		$data = 'NO VALUE';
		
	// user data
	QRcode::png($data, false, $errorCorrectionLevel, $matrixPointSize, 2);

	
} else {    

	//default data
	echo 'You can provide data in GET parameter: <a href="?data=like_that">like that</a><hr/>';    
	QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
	
}