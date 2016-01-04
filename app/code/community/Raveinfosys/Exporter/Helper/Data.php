<?php

class Raveinfosys_Exporter_Helper_Data extends Mage_Core_Helper_Abstract
{
   public $error_file = '';
   
    public function __construct()
    {
		$this->error_file = Mage::getBaseDir('var') .'/raveinfosys/exporter/order_exception_log.htm';
		$handle = fopen($this->error_file, "a+");	
		chmod($this->error_file, 0777);
    }
   
   public function logException(Exception $exception,$order_id,$type,$line_nuber = '') 
   {
        $message = $exception->getMessage();
        $code = $exception->getCode();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();
        $date = date('M d, Y h:iA');
        
		if($type=='order'){		
            $log_message = "<p><strong>Order Id:</strong> {$order_id}
		    <p><strong>Error Message:</strong> {$message}</p>
            <p><strong>Line Number:</strong> {$line_nuber}</p>";
		}
		else if($type=='invoice'){
		    $log_message = "<p><strong>Invoice Error : </strong></p>
		    <p><strong>Order Id:</strong> {$order_id}</p>
            <p><strong>Error Message:</strong> {$message}</p>";
		}	
		else if($type=='shipment'){
		    $log_message = "<p><strong>Shipment Error : </strong></p>
		    <p><strong>Order Id:</strong> {$order_id}</p>
            <p><strong>Error Message:</strong> {$message}</p>";
		}
		else if($type=='creditmemo'){
		    $log_message = "<p><strong>Creditmemo Error : </strong></p>
		    <p><strong>Order Id:</strong> {$order_id}</p>
            <p><strong>Error Message:</strong> {$message}</p>";
		}	
         
        if( is_file($this->error_file) === false ) {
            file_put_contents($this->error_file, '');
        }
         
        $content = file_get_contents($this->error_file);
        file_put_contents($this->error_file, $content.$log_message);
    }
	
	public function logAvailable($order_id,$type,$line_nuber) 
    {
        $message = "Order id already exist";
        $log_message = "<p><strong>Order Id:</strong> {$order_id}</p>
        <p><strong>Error Message:</strong> {$message}</p>
        <p><strong>Line Number:</strong> {$line_nuber}</p>";
		
		if( is_file($this->error_file) === false ) {
            file_put_contents($this->error_file, '');
        }
        $content = file_get_contents($this->error_file);
        file_put_contents($this->error_file, $content.$log_message);
    }
	
	public function header()
	{
	  file_put_contents($this->error_file, '<h3 style="text-align:center;">Error information:</h3><hr /><br />');
	}
	
	public function isPrintable()
	{
	  if(filesize($this->error_file)>67)
	  return true;
	}
	
	public function footer()
	{
	  $content = file_get_contents($this->error_file);
      file_put_contents($this->error_file, $content.'<br /><hr /><br /><br />');
	}
	
	public function unlinkFile()
	{
	  unlink($this->error_file);
	}
	
	public function getVersion()
	{
	  $m= new Mage;
	  $version=$m->getVersion();
	  if(in_array($version,array('1.5.0.0','1.5.0.1','1.5.1.0','1.6.0.0','1.9.1.1','1.10.0.2','1.10.1.1','1.11.0.0')))
	  return true;
	  else
	  return false;
	}
  
}