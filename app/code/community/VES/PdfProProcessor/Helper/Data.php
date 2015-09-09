<?php

class VES_PdfProProcessor_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
     * Gets the detailed PDF Pro version information
     *
     * @return array
     */
    public static function getVersionInfo()
    {
    	return array(
    			'major'     => '1',
    			'minor'     => '1',
    			'revision'  => '0',
    			'patch'     => '1',
    			'stability' => '',
    			'number'    => '',
    	);
    }
    
    /**
     * Gets the current PDF Pro version string
     *
     * @return string
     */
    public static function getVersion()
    {
    	$i = self::getVersionInfo();
    	return trim("{$i['major']}.{$i['minor']}.{$i['revision']}" . ($i['patch'] != '' ? ".{$i['patch']}" : "")
    	. "-{$i['stability']}{$i['number']}", '.-');
    }
}