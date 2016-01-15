<?php
/**
 * Loads GeoIP binary data files 
 *
 * @category   Chapagain
 * @package    Chapagain_AutoCurrency
 * @version    0.2.0
 * @author     Mukesh Chapagain <mukesh.chapagain@gmail.com> 
 * @link 	   http://blog.chapagain.com.np/category/magento/
 */
 
class Chapagain_AutoCurrency_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Check if the module is enabled
	 * 
	 * @return boolean 0 or 1
	 */ 
	public function isEnabled()
    { 		
        return Mage::getStoreConfig('catalog/autocurrency/enable_disable');
    }
    
    /**
	 * Check IP database source
	 * 
	 * @return string
	 */ 
	public function getDatabaseSource()
    { 		
        $db = Mage::getStoreConfig('catalog/autocurrency/database_source');
        if ($db == null) { 
			$db = 'ip2country';
		}
		return $db;
    }
    
	/**
     * Get IP Address
     *
     * @return string
     */
	public function getIpAddress() 
	{			
		return $_SERVER['REMOTE_ADDR'];
	}
	
	/**
     * Include GeoIP Inc file
     */
	public function loadGeoIpInc() 
	{	
		// Load geoip.inc
		include_once(Mage::getBaseDir().'/var/geoip/geoip.inc');	
	}
	
	/**
     * Load GeoIPv4 binary data file
     */
	public function loadGeoIpv4() 
	{			
		$geoIPv4 = geoip_open(Mage::getBaseDir().'/var/geoip/GeoIP.dat',GEOIP_STANDARD);				
		return $geoIPv4;
	}
	
	/**
     * Load GeoIPv6 binary data file
     */
	public function loadGeoIpv6() 
	{			
		$geoIPv6 = geoip_open(Mage::getBaseDir().'/var/geoip/GeoIPv6.dat',GEOIP_STANDARD);		
		return $geoIPv6;
	}
	
	/**
     * Check whether the given IP Address is valid
     * 
     * @param string IP Address
     * @return boolean True/False
     */
	public function checkValidIp($ip) 
	{			
		if(!filter_var($ip, FILTER_VALIDATE_IP)) {
			return false;
		}		
		return true;
	}
	
	/**
     * Check whether the given IP Address is IPv6
     * 
     * @param string IP Address
     * @return boolean True/False
     */
	public function checkIpv6($ip) 
	{			
		if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			return false;
		}		
		return true;
	}		
	
	/**
     * Include IP2Country.php file
     * and load ip2country.dat file 
     * 
     */
	public function loadIp2Country()
	{
		include_once(Mage::getBaseDir().'/var/geoip/ip2country/Ip2Country.php');
		$ipc = new Ip2Country(Mage::getBaseDir().'/var/geoip/ip2country/ip2country.dat');
		$ipc->preload();
		return $ipc;
	}
	
	/**
	 * Get Currency code by Country code
	 * 
	 * @return string
	 */ 
	public function getCurrencyByCountry($countryCode)
	{
		$map = array( '' => '',
		"EU" => "EUR", "AD" => "EUR", "AE" => "AED", "AF" => "AFN", "AG" => "XCD", "AI" => "XCD", 
		"AL" => "ALL", "AM" => "AMD", "CW" => "ANG", "AO" => "AOA", "AQ" => "AQD", "AR" => "ARS", "AS" => "EUR", 
		"AT" => "EUR", "AU" => "AUD", "AW" => "AWG", "AZ" => "AZN", "BA" => "BAM", "BB" => "BBD", 
		"BD" => "BDT", "BE" => "EUR", "BF" => "XOF", "BG" => "BGL", "BH" => "BHD", "BI" => "BIF", 
		"BJ" => "XOF", "BM" => "BMD", "BN" => "BND", "BO" => "BOB", "BR" => "BRL", "BS" => "BSD", 
		"BT" => "BTN", "BV" => "NOK", "BW" => "BWP", "BY" => "BYR", "BZ" => "BZD", "CA" => "CAD", 
		"CC" => "AUD", "CD" => "CDF", "CF" => "XAF", "CG" => "XAF", "CH" => "CHF", "CI" => "XOF", 
		"CK" => "NZD", "CL" => "CLP", "CM" => "XAF", "CN" => "CNY", "CO" => "COP", "CR" => "CRC", 
		"CU" => "CUP", "CV" => "CVE", "CX" => "AUD", "CY" => "EUR", "CZ" => "CZK", "DE" => "EUR", 
		"DJ" => "DJF", "DK" => "DKK", "DM" => "XCD", "DO" => "DOP", "DZ" => "DZD", "EC" => "ECS", 
		"EE" => "EEK", "EG" => "EGP", "EH" => "MAD", "ER" => "ETB", "ES" => "EUR", "ET" => "ETB", 
		"FI" => "EUR", "FJ" => "FJD", "FK" => "FKP", "FM" => "USD", "FO" => "DKK", "FR" => "EUR", "SX" => "ANG",
		"GA" => "XAF", "GB" => "GBP", "GD" => "XCD", "GE" => "GEL", "GF" => "EUR", "GH" => "GHS", 
		"GI" => "GIP", "GL" => "DKK", "GM" => "GMD", "GN" => "GNF", "GP" => "EUR", "GQ" => "XAF", 
		"GR" => "EUR", "GS" => "GBP", "GT" => "GTQ", "GU" => "USD", "GW" => "XOF", "GY" => "GYD", 
		"HK" => "HKD", "HM" => "AUD", "HN" => "HNL", "HR" => "HRK", "HT" => "HTG", "HU" => "HUF", 
		"ID" => "IDR", "IE" => "EUR", "IL" => "ILS", "IN" => "INR", "IO" => "USD", "IQ" => "IQD", 
		"IR" => "IRR", "IS" => "ISK", "IT" => "EUR", "JM" => "JMD", "JO" => "JOD", "JP" => "JPY", 
		"KE" => "KES", "KG" => "KGS", "KH" => "KHR", "KI" => "AUD", "KM" => "KMF", "KN" => "XCD", 
		"KP" => "KPW", "KR" => "KRW", "KW" => "KWD", "KY" => "KYD", "KZ" => "KZT", "LA" => "LAK", 
		"LB" => "LBP", "LC" => "XCD", "LI" => "CHF", "LK" => "LKR", "LR" => "LRD", "LS" => "LSL", 
		"LT" => "LTL", "LU" => "EUR", "LV" => "LVL", "LY" => "LYD", "MA" => "MAD", "MC" => "EUR", 
		"MD" => "MDL", "MG" => "MGF", "MH" => "USD", "MK" => "MKD", "ML" => "XOF", "MM" => "MMK", 
		"MN" => "MNT", "MO" => "MOP", "MP" => "USD", "MQ" => "EUR", "MR" => "MRO", "MS" => "XCD", 
		"MT" => "EUR", "MU" => "MUR", "MV" => "MVR", "MW" => "MWK", "MX" => "MXN", "MY" => "MYR", 
		"MZ" => "MZN", "NA" => "NAD", "NC" => "XPF", "NE" => "XOF", "NF" => "AUD", "NG" => "NGN", 
		"NI" => "NIO", "NL" => "EUR", "NO" => "NOK", "NP" => "NPR", "NR" => "AUD", "NU" => "NZD", 
		"NZ" => "NZD", "OM" => "OMR", "PA" => "PAB", "PE" => "PEN", "PF" => "XPF", "PG" => "PGK", 
		"PH" => "PHP", "PK" => "PKR", "PL" => "PLN", "PM" => "EUR", "PN" => "NZD", "PR" => "USD", "PS" => "ILS", "PT" => "EUR", 
		"PW" => "USD", "PY" => "PYG", "QA" => "QAR", "RE" => "EUR", "RO" => "RON", "RU" => "RUB", 
		"RW" => "RWF", "SA" => "SAR", "SB" => "SBD", "SC" => "SCR", "SD" => "SDD", "SE" => "SEK", 
		"SG" => "SGD", "SH" => "SHP", "SI" => "EUR", "SJ" => "NOK", "SK" => "SKK", "SL" => "SLL", 
		"SM" => "EUR", "SN" => "XOF", "SO" => "SOS", "SR" => "SRG", "ST" => "STD", "SV" => "SVC", 
		"SY" => "SYP", "SZ" => "SZL", "TC" => "USD", "TD" => "XAF", "TF" => "EUR", "TG" => "XOF", 
		"TH" => "THB", "TJ" => "TJS", "TK" => "NZD", "TM" => "TMM", "TN" => "TND", "TO" => "TOP", "TL" => "USD",
		"TR" => "TRY", "TT" => "TTD", "TV" => "AUD", "TW" => "TWD", "TZ" => "TZS", "UA" => "UAH", 
		"UG" => "UGX", "UM" => "USD", "US" => "USD", "UY" => "UYU", "UZ" => "UZS", "VA" => "EUR", 
		"VC" => "XCD", "VE" => "VEF", "VG" => "USD", "VI" => "USD", "VN" => "VND", "VU" => "VUV",
		"WF" => "XPF", "WS" => "EUR", "YE" => "YER", "YT" => "EUR", "RS" => "RSD", 
		"ZA" => "ZAR", "ZM" => "ZMK", "ME" => "EUR", "ZW" => "ZWD",
		"AX" => "EUR", "GG" => "GBP", "IM" => "GBP", 
		"JE" => "GBP", "BL" => "EUR", "MF" => "EUR", "BQ" => "USD", "SS" => "SSP"
		);
		
		return $map[$countryCode];
	}
}
