<?php

class FreeLunchLabs_CloudFront_Model_RefreshAdapters_Apache extends Varien_Object {
    
    public $filename = '.htaccess';

    function buildFileContents() {
        $contents = "RewriteEngine on \n";
        $contents .="RewriteRule ^[0-9]{1,6}/(.*)$ ../$1 [PT] \n";
        
        return $contents;
    }
    
    function getAdapterFileName() {
       return $this->filename;
    }
    
}
